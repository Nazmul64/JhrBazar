<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SociallinkList;
use Illuminate\Http\Request;

class SociallinkListController extends Controller
{
    // ══════════════════════════════════════════
    //  INDEX
    // ══════════════════════════════════════════
    public function index()
    {
        $socialLinks = SociallinkList::orderBy('id')->get();
        return view('admin.sociallinklist.index', compact('socialLinks'));
    }

    // ══════════════════════════════════════════
    //  CREATE / STORE  (not used — fixed list)
    // ══════════════════════════════════════════
    public function create()
    {
        return redirect()->route('admin.sociallinkList.index');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.sociallinkList.index');
    }

    // ══════════════════════════════════════════
    //  SHOW  (not used)
    // ══════════════════════════════════════════
    public function show(string $id)
    {
        return redirect()->route('admin.sociallinkList.index');
    }

    // ══════════════════════════════════════════
    //  EDIT  (returns JSON for modal)
    // ══════════════════════════════════════════
    public function edit(SociallinkList $sociallinkList)
    {
        return response()->json([
            'id'   => $sociallinkList->id,
            'name' => $sociallinkList->name,
            'link' => $sociallinkList->link,
        ]);
    }

    // ══════════════════════════════════════════
    //  UPDATE
    // ══════════════════════════════════════════
    public function update(Request $request, SociallinkList $sociallinkList)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'link' => 'nullable|url|max:500',
        ]);

        $sociallinkList->update([
            'name' => $request->name,
            'link' => $request->link ?: null,
        ]);

        return redirect()->route('admin.sociallinkList.index')
            ->with('success', $sociallinkList->name . ' link updated successfully.');
    }

    // ══════════════════════════════════════════
    //  DESTROY  (not used — fixed list)
    // ══════════════════════════════════════════
    public function destroy(string $id)
    {
        return redirect()->route('admin.sociallinkList.index');
    }

    // ══════════════════════════════════════════
    //  TOGGLE STATUS
    // ══════════════════════════════════════════
    public function toggleStatus(SociallinkList $sociallinkList)
    {
        $sociallinkList->update(['is_active' => !$sociallinkList->is_active]);
        return redirect()->back()->with('success', 'Status updated.');
    }
}
