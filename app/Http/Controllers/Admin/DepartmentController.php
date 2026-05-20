<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments and designations.
     */
    public function index()
    {
        $departments = Department::with(['designations', 'employees'])->get();
        return view('admin.department.index', compact('departments'));
    }

    /**
     * Store a newly created department.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:departments,name|max:255',
            'description' => 'nullable|string'
        ]);

        Department::create($request->only('name', 'description'));

        return redirect()->back()->with('success', 'Department created successfully!');
    }

    /**
     * Store a newly created designation.
     */
    public function storeDesignation(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'grade' => 'nullable|string|max:100'
        ]);

        Designation::create($request->only('department_id', 'name', 'grade'));

        return redirect()->back()->with('success', 'Designation created successfully!');
    }

    /**
     * Remove the specified department.
     */
    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->back()->with('success', 'Department deleted successfully!');
    }

    /**
     * Remove the specified designation.
     */
    public function destroyDesignation($id)
    {
        $designation = Designation::findOrFail($id);
        $designation->delete();
        return redirect()->back()->with('success', 'Designation deleted successfully!');
    }
}
