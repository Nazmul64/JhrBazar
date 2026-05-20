<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    /**
     * Display a listing of employee leaves.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $query = Leave::with(['employee', 'approver'])->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $leaves = $query->get();
        $employees = User::whereIn('role', ['employee', 'manager'])->orderBy('name')->get();

        return view('admin.leave.index', compact('leaves', 'employees', 'status'));
    }

    /**
     * Store a newly created leave log (manually added by Admin).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'leave_type'  => 'required|string|max:100',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'reason'      => 'nullable|string',
            'status'      => 'required|in:Pending,Approved,Rejected',
        ]);

        if ($validated['status'] === 'Approved') {
            $validated['approved_by'] = Auth::id();
        }

        Leave::create($validated);

        return redirect()->route('admin.leave.index')
            ->with('success', 'Leave logged successfully.');
    }

    /**
     * Update the approval status of a leave application.
     */
    public function updateStatus(Request $request, Leave $leave)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected'
        ]);

        $leave->update([
            'status'      => $request->status,
            'approved_by' => $request->status === 'Approved' ? Auth::id() : null
        ]);

        return redirect()->route('admin.leave.index')
            ->with('success', 'Leave application status updated successfully.');
    }

    /**
     * Remove a leave log.
     */
    public function destroy(Leave $leave)
    {
        $leave->delete();
        return redirect()->route('admin.leave.index')
            ->with('success', 'Leave record deleted successfully.');
    }
}
