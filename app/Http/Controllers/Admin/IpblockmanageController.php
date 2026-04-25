<?php
// app/Http/Controllers/Admin/IpblockmanageController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ipblockmanage;
use Illuminate\Http\Request;

class IpblockmanageController extends Controller
{
    /**
     * INDEX - List all + create form (same page like image)
     */
    public function index()
    {
        $ipblocks = Ipblockmanage::latest()->get();
        return view('admin.ipblockmanage.index', compact('ipblocks'));
    }

    /**
     * STORE - Save new IP block
     */
    public function store(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip|unique:ipblockmanages,ip_address',
            'reason'     => 'required|string|max:1000',
        ], [
            'ip_address.required' => 'IP No is required.',
            'ip_address.ip'       => 'Please enter a valid IP address.',
            'ip_address.unique'   => 'This IP is already blocked.',
            'reason.required'     => 'Reason is required.',
        ]);

        Ipblockmanage::create([
            'ip_address' => $request->ip_address,
            'reason'     => $request->reason,
            'is_active'  => true,
        ]);

        return redirect()->route('admin.Ipblockmanage.index')
            ->with('success', 'IP blocked successfully.');
    }

    /**
     * EDIT - Show edit form
     */
    public function edit(string $id)
    {
        $ipblock = Ipblockmanage::findOrFail($id);
        return view('admin.ipblockmanage.edit', compact('ipblock'));
    }

    /**
     * UPDATE - Update IP block
     */
    public function update(Request $request, string $id)
    {
        $ipblock = Ipblockmanage::findOrFail($id);

        $request->validate([
            'ip_address' => 'required|ip|unique:ipblockmanages,ip_address,' . $id,
            'reason'     => 'required|string|max:1000',
        ], [
            'ip_address.required' => 'IP No is required.',
            'ip_address.ip'       => 'Please enter a valid IP address.',
            'ip_address.unique'   => 'This IP is already blocked.',
            'reason.required'     => 'Reason is required.',
        ]);

        $ipblock->update([
            'ip_address' => $request->ip_address,
            'reason'     => $request->reason,
        ]);

        return redirect()->route('admin.Ipblockmanage.index')
            ->with('success', 'IP block updated successfully.');
    }

    /**
     * DESTROY - Delete IP block
     */
    public function destroy(string $id)
    {
        $ipblock = Ipblockmanage::findOrFail($id);
        $ipblock->delete();

        return redirect()->route('admin.Ipblockmanage.index')
            ->with('success', 'IP block removed successfully.');
    }

    /**
     * TOGGLE STATUS
     */
    public function toggleStatus(string $id)
    {
        $ipblock = Ipblockmanage::findOrFail($id);
        $ipblock->update(['is_active' => !$ipblock->is_active]);

        return redirect()->route('admin.Ipblockmanage.index')
            ->with('success', 'Status updated successfully.');
    }

    // Not needed — redirect
    public function create()  { return redirect()->route('admin.Ipblockmanage.index'); }
    public function show(string $id) { return redirect()->route('admin.Ipblockmanage.index'); }
}
