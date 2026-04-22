<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class SupplierController extends Controller
{
    // ══════════════════════════════════════════════════
    //  INDEX
    // ══════════════════════════════════════════════════
    public function index(Request $request)
    {
        $query = Supplier::with('user')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', function ($q) use ($s) {
                $q->where('name',  'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $suppliers = $query->get();
        return view('admin.supplier.index', compact('suppliers'));
    }

    // ══════════════════════════════════════════════════
    //  CREATE
    // ══════════════════════════════════════════════════
    public function create()
    {
        return view('admin.supplier.create');
    }

    // ══════════════════════════════════════════════════
    //  STORE
    // ══════════════════════════════════════════════════
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'email'         => 'required|email|unique:users,email',
            'address'       => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'phone'    => $request->phone,
            'email'    => $request->email,
            'password' => Hash::make($request->phone), // default password = phone number
            'role'     => 'vendor',
        ]);

        $imagePath = null;
        if ($request->hasFile('profile_image')) {
            $imagePath = $this->uploadImage($request->file('profile_image'));
        }

        Supplier::create([
            'user_id'       => $user->id,
            'address'       => $request->address,
            'profile_image' => $imagePath,
        ]);

        return redirect()->route('admin.supplier.index')
            ->with('success', 'Supplier created successfully.');
    }

    // ══════════════════════════════════════════════════
    //  SHOW
    // ══════════════════════════════════════════════════
    public function show(Supplier $supplier)
    {
        $supplier->load('user');

        $purchases = $supplier->purchases()->latest()->take(10)->get();

        $transactions = SupplierTransaction::where('supplier_id', $supplier->id)
            ->latest()
            ->get();

        // ── Monthly stats for current year chart ──────
        $year            = now()->year;
        $monthlyStats    = array_fill(0, 12, 0);
        $monthlyProducts = array_fill(0, 12, 0);

        $supplier->purchases()
            ->whereYear('created_at', $year)
            ->get()
            ->each(function ($p) use (&$monthlyStats, &$monthlyProducts) {
                $m = (int) $p->created_at->format('n') - 1; // 0-indexed month
                $monthlyStats[$m]    += 1;
                $monthlyProducts[$m] += $p->items_count ?? 0;
            });

        return view('admin.supplier.show', compact(
            'supplier',
            'purchases',
            'transactions',
            'monthlyStats',
            'monthlyProducts'
        ));
    }

    // ══════════════════════════════════════════════════
    //  EDIT
    // ══════════════════════════════════════════════════
    public function edit(Supplier $supplier)
    {
        $supplier->load('user');
        return view('admin.supplier.edit', compact('supplier'));
    }

    // ══════════════════════════════════════════════════
    //  UPDATE
    // ══════════════════════════════════════════════════
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'email'         => 'required|email|unique:users,email,' . $supplier->user_id,
            'address'       => 'nullable|string|max:500',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $supplier->user->update([
            'name'  => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);

        $data = ['address' => $request->address];

        if ($request->hasFile('profile_image')) {
            $this->deleteImage($supplier->profile_image);
            $data['profile_image'] = $this->uploadImage($request->file('profile_image'));
        }

        $supplier->update($data);

        return redirect()->route('admin.supplier.index')
            ->with('success', 'Supplier updated successfully.');
    }

    // ══════════════════════════════════════════════════
    //  DESTROY
    // ══════════════════════════════════════════════════
    public function destroy(Supplier $supplier)
    {
        $this->deleteImage($supplier->profile_image);
        $supplier->user->delete(); // cascades to supplier row
        return redirect()->route('admin.supplier.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    // ══════════════════════════════════════════════════
    //  TOGGLE STATUS
    // ══════════════════════════════════════════════════
    public function toggleStatus(Supplier $supplier)
    {
        $supplier->update(['is_active' => !$supplier->is_active]);
        return redirect()->back()->with('success', 'Status updated.');
    }

    // ══════════════════════════════════════════════════
    //  PAY NOW  (Pay amount to supplier)
    // ══════════════════════════════════════════════════
    public function pay(Request $request, Supplier $supplier)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'transaction_id' => 'nullable|string|max:255',
            'attachment'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'note'           => 'nullable|string|max:1000',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $this->uploadAttachment($request->file('attachment'));
        }

        SupplierTransaction::create([
            'supplier_id'    => $supplier->id,
            'amount'         => $request->amount,
            'transaction_id' => $request->transaction_id,
            'attachment'     => $attachmentPath,
            'note'           => $request->note,
        ]);

        return redirect()->back()
            ->with('success', 'Payment of $' . number_format($request->amount, 2) . ' recorded successfully.');
    }

    // ══════════════════════════════════════════════════
    //  PRIVATE HELPERS
    // ══════════════════════════════════════════════════

    private function uploadImage($file): string
    {
        $path = public_path('uploads/supplier');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($path, $fileName);
        return 'uploads/supplier/' . $fileName;
    }

    private function uploadAttachment($file): string
    {
        $path = public_path('uploads/supplier/transactions');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($path, $fileName);
        return 'uploads/supplier/transactions/' . $fileName;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
