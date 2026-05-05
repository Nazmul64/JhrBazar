<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::latest()->get();
        return view('admin.banks.index', compact('banks'));
    }

    public function create()
    {
        return view('admin.banks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:banks',
        ]);

        Bank::create([
            'name' => $request->name,
            'status' => 'active',
        ]);

        return redirect()->route('admin.banks.index')->with('success', 'Bank added successfully!');
    }

    public function edit(Bank $bank)
    {
        return view('admin.banks.edit', compact('bank'));
    }

    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:banks,name,' . $bank->id,
            'status' => 'required|in:active,inactive',
        ]);

        $bank->update($request->only('name', 'status'));

        return redirect()->route('admin.banks.index')->with('success', 'Bank updated successfully!');
    }

    public function destroy(Bank $bank)
    {
        $bank->delete();
        return back()->with('success', 'Bank deleted successfully!');
    }
}
