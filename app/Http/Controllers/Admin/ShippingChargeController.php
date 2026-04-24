<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;

class ShippingChargeController extends Controller
{
    /**
     * Display a listing (Manage page).
     */
    public function index(Request $request)
    {
        $query = ShippingCharge::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('area_name', 'like', "%{$search}%");
        }

        $shippingCharges = $query->latest()->paginate(10);

        return view('admin.shippingcharge.index', compact('shippingCharges'));
    }

    /**
     * Show the form for creating a new shipping charge.
     */
    public function create()
    {
        return view('admin.shippingcharge.create');
    }

    /**
     * Store a newly created shipping charge.
     */
    public function store(Request $request)
    {
        $request->validate([
            'area_name' => 'required|string|max:255',
            'charge'    => 'required|numeric|min:0',
        ]);

        ShippingCharge::create([
            'area_name' => $request->area_name,
            'charge'    => $request->charge,
            'status'    => $request->has('status'),
        ]);

        return redirect()->route('admin.shippingcharge.index')
            ->with('success', 'Shipping charge created successfully.');
    }

    /**
     * Show the form for editing the specified shipping charge.
     */
    public function edit(ShippingCharge $shippingcharge)
    {
        return view('admin.shippingcharge.edit', compact('shippingcharge'));
    }

    /**
     * Update the specified shipping charge.
     */
    public function update(Request $request, ShippingCharge $shippingcharge)
    {
        $request->validate([
            'area_name' => 'required|string|max:255',
            'charge'    => 'required|numeric|min:0',
        ]);

        $shippingcharge->update([
            'area_name' => $request->area_name,
            'charge'    => $request->charge,
            'status'    => $request->has('status'),
        ]);

        return redirect()->route('admin.shippingcharge.index')
            ->with('success', 'Shipping charge updated successfully.');
    }

    /**
     * Remove the specified shipping charge.
     */
    public function destroy(ShippingCharge $shippingcharge)
    {
        $shippingcharge->delete();

        return redirect()->route('admin.shippingcharge.index')
            ->with('success', 'Shipping charge deleted successfully.');
    }

    /**
     * Toggle the status of the specified shipping charge.
     */
    public function toggleStatus(ShippingCharge $shippingcharge)
    {
        $shippingcharge->update(['status' => !$shippingcharge->status]);

        return redirect()->route('admin.shippingcharge.index')
            ->with('success', 'Status updated successfully.');
    }
}
