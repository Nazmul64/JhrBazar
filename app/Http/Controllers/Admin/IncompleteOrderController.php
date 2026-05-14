<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncompleteOrder;
use App\Models\Shop;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;

class IncompleteOrderController extends Controller
{
    /**
     * Display a listing of incomplete orders (leads).
     */
    public function index(Request $request)
    {
        $query = IncompleteOrder::with(['shop', 'staff'])->latest();

        // If seller, only show their shop's leads
        if (auth()->user()->role === 'seller') {
            $shop = Shop::where('user_id', auth()->id())->first();
            if ($shop) {
                $query->where('shop_id', $shop->id);
            } else {
                $query->whereRaw('1=0'); // No shop, no leads
            }
        }

        // Stats
        $statsQuery = clone $query;
        $totalIncomplete = (clone $statsQuery)->where('status', 'incomplete')->count();
        $totalRecovered  = (clone $statsQuery)->where('status', 'recovered')->count();
        $totalContacted  = (clone $statsQuery)->where('status', 'contacted')->count();
        $totalEntry      = (clone $statsQuery)->count();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $leads = $query->paginate(20)->withQueryString();

        return view('admin.orders.incomplete', compact(
            'leads', 
            'totalIncomplete', 
            'totalRecovered', 
            'totalContacted', 
            'totalEntry'
        ));
    }

    /**
     * Update the status of a lead.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,called,converted,cancelled'
        ]);

        $lead = IncompleteOrder::findOrFail($id);
        
        // Authorization check for seller
        if (auth()->user()->role === 'seller') {
            $shop = Shop::where('user_id', auth()->id())->first();
            if (!$shop || $lead->shop_id !== $shop->id) {
                abort(403);
            }
        }

        $oldStatus = $lead->status;
        $lead->status = $request->status;
        $lead->save();

        // If status changed to an order status, convert to real order
        $orderStatuses = ['pending', 'processing', 'shipped', 'delivered'];
        if (in_array($request->status, $orderStatuses) && !in_array($oldStatus, $orderStatuses)) {
            $this->convertToOrder($lead);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lead status updated to ' . $request->status
            ]);
        }

        return back()->with('success', 'Lead status updated successfully.');
    }

    /**
     * Remove a lead.
     */
    public function destroy($id)
    {
        $lead = IncompleteOrder::findOrFail($id);
        
        // Authorization check for seller
        if (auth()->user()->role === 'seller') {
            $shop = Shop::where('user_id', auth()->id())->first();
            if (!$shop || $lead->shop_id !== $shop->id) {
                abort(403);
            }
        }

        $lead->delete();

        return back()->with('success', 'Lead deleted successfully.');
    }

    /**
     * Update lead information.
     */
    public function updateLead(Request $request, $id)
    {
        $request->validate([
            'name'    => 'nullable|string|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $lead = IncompleteOrder::findOrFail($id);
        $lead->update($request->only(['name', 'phone', 'address']));

        return response()->json(['success' => true, 'message' => 'Lead information updated successfully.']);
    }

    /**
     * Bulk assign leads to staff.
     */
    public function bulkAssign(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'staff_id' => 'required|exists:users,id']);
        IncompleteOrder::whereIn('id', $request->ids)->update(['staff_id' => $request->staff_id]);
        return response()->json(['success' => true, 'message' => 'Leads assigned successfully.']);
    }

    /**
     * Bulk update status of leads.
     */
    public function bulkStatusUpdate(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'status' => 'required']);
        
        $leads = IncompleteOrder::whereIn('id', $request->ids)->get();
        $orderStatuses = ['pending', 'processing', 'shipped', 'delivered'];

        foreach ($leads as $lead) {
            $oldStatus = $lead->status;
            $lead->status = $request->status;
            $lead->save();

            if (in_array($request->status, $orderStatuses) && !in_array($oldStatus, $orderStatuses)) {
                $this->convertToOrder($lead);
            }
        }

        return response()->json(['success' => true, 'message' => 'Leads status updated successfully.']);
    }

    /**
     * Bulk delete leads.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        IncompleteOrder::whereIn('id', $request->ids)->delete();
        return response()->json(['success' => true, 'message' => 'Leads deleted successfully.']);
    }

    /**
     * Convert an incomplete order (lead) to a real order.
     */
    private function convertToOrder(IncompleteOrder $lead)
    {
        \DB::beginTransaction();
        try {
            // 1. Handle User/Customer
            $user = User::where('phone', $lead->phone)->first();
            if (!$user) {
                $user = User::create([
                    'name'     => $lead->name ?? 'Guest Customer',
                    'phone'    => $lead->phone,
                    'email'    => $lead->phone . '@jhrbazar.com',
                    'role'     => 'customer',
                    'password' => \Hash::make($lead->phone),
                ]);
            }

            $customer = Customer::where('user_id', $user->id)->first();
            if (!$customer) {
                $customer = Customer::create([
                    'user_id'    => $user->id,
                    'first_name' => $lead->name ?? 'Guest',
                    'last_name'  => '',
                    'address'    => $lead->address ?? 'N/A',
                ]);
            }

            // 2. Map Cart Items
            $itemSnapshots = [];
            $subTotal = 0;
            if (is_array($lead->cart_items)) {
                foreach ($lead->cart_items as $item) {
                    $itemPrice = (float)($item['price'] ?? 0);
                    $qty = (int)($item['qty'] ?? 1);
                    $lineTotal = $itemPrice * $qty;
                    $subTotal += $lineTotal;

                    $itemSnapshots[] = [
                        'id'        => $item['id'] ?? null,
                        'name'      => $item['title'] ?? ($item['name'] ?? 'Product'),
                        'thumbnail' => $item['image'] ?? null,
                        'price'     => $itemPrice,
                        'qty'       => $qty,
                        'discount'  => 0,
                        'line_total'=> $lineTotal,
                        'color'     => $item['color'] ?? 'N/A',
                        'size'      => $item['size'] ?? 'N/A',
                    ];
                }
            }

            $shipping = 120; // Default or can be dynamic
            $grandTotal = $subTotal + $shipping;

            // 3. Create POS Order
            $order = \App\Models\Pointofsalepo::create([
                'seller_id'      => $lead->shop_id,
                'customer_id'    => $customer->id,
                'staff_id'       => $lead->staff_id,
                'items'          => $itemSnapshots,
                'sub_total'      => $subTotal,
                'grand_total'    => $grandTotal,
                'delivery_charge'=> $shipping,
                'payment_method' => $lead->payment_method ?? 'cod',
                'payment_status' => 'pending',
                'status'         => $lead->status,
                'note'           => "Recovered from Incomplete Order. Area: " . ($lead->area ?? 'N/A'),
            ]);

            // 4. Create Invoice
            \App\Models\PosInvoice::create([
                'invoice_number'   => \App\Models\PosInvoice::generateInvoiceNumber(),
                'pointofsalepo_id' => $order->id,
                'seller_id'        => $lead->shop_id,
                'customer_id'      => $customer->id,
                'items'            => $itemSnapshots,
                'sub_total'        => $subTotal,
                'delivery_charge'  => $shipping,
                'grand_total'      => $grandTotal,
                'payment_method'   => $lead->payment_method ?? 'cod',
            ]);

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Lead conversion failed: " . $e->getMessage());
        }
    }
}
