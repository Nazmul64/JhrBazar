<?php
// app/Http/Controllers/Admin/AlltaxesController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alltaxe;
use Illuminate\Http\Request;

class AlltaxesController extends Controller
{
    /**
     * Display all taxes.
     */
    public function index()
    {
        $taxes = Alltaxe::latest()->get();
        return view('admin.alltaxes.index', compact('taxes'));
    }

    /**
     * Store a new tax (AJAX modal).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        Alltaxe::create([
            'name'       => $request->name,
            'percentage' => $request->percentage,
            'status'     => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tax added successfully.',
        ]);
    }

    /**
     * Update an existing tax (AJAX modal).
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $tax = Alltaxe::findOrFail($id);
        $tax->update([
            'name'       => $request->name,
            'percentage' => $request->percentage,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tax updated successfully.',
        ]);
    }

    /**
     * Delete a tax.
     */
    public function destroy(string $id)
    {
        Alltaxe::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tax deleted successfully.',
        ]);
    }

    /**
     * Toggle tax active/inactive status.
     */
    public function toggleStatus(string $id)
    {
        $tax = Alltaxe::findOrFail($id);
        $tax->update(['status' => !$tax->status]);

        return response()->json([
            'success' => true,
            'status'  => $tax->status,
            'message' => 'Status updated.',
        ]);
    }
}
