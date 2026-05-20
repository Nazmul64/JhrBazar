<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SalaryAdvance;
use Illuminate\Http\Request;

class SalaryAdvanceController extends Controller
{
    /**
     * Display a listing of salary advances.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $query = SalaryAdvance::with('employee')->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $advances = $query->get();
        $employees = User::whereIn('role', ['employee', 'manager'])->orderBy('name')->get();

        // Calculate summary cards
        $totalApprovedAmount = SalaryAdvance::where('status', 'Approved')->sum('amount');
        $totalPendingRequests = SalaryAdvance::where('status', 'Pending')->count();

        return view('admin.salary_advance.index', compact('advances', 'employees', 'status', 'totalApprovedAmount', 'totalPendingRequests'));
    }

    /**
     * Store a newly created salary advance request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'              => 'required|exists:users,id',
            'amount'                   => 'required|numeric|min:1',
            'advance_date'             => 'required|date',
            'deduction_type'           => 'required|in:Monthly Deduct,One Time Deduct,Manual Payback',
            'monthly_deduction_amount' => 'required_if:deduction_type,Monthly Deduct|nullable|numeric|min:0',
            'status'                   => 'required|in:Pending,Approved,Rejected',
            'reason'                   => 'nullable|string',
        ]);

        if ($validated['deduction_type'] !== 'Monthly Deduct') {
            $validated['monthly_deduction_amount'] = 0.00;
        }

        SalaryAdvance::create($validated);

        return redirect()->route('admin.salary-advance.index')
            ->with('success', 'Salary advance recorded successfully.');
    }

    /**
     * Update the approval status of an advance request.
     */
    public function updateStatus(Request $request, SalaryAdvance $advance)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected'
        ]);

        $advance->update([
            'status' => $request->status
        ]);

        return redirect()->route('admin.salary-advance.index')
            ->with('success', 'Salary advance request status updated successfully.');
    }

    /**
     * Mark an approved advance as paid/disbursed or unpaid.
     */
    public function togglePaid(Request $request, SalaryAdvance $advance)
    {
        $request->validate([
            'paid_status' => 'required|in:Unpaid,Paid'
        ]);

        $advance->update([
            'paid_status' => $request->paid_status
        ]);

        return redirect()->route('admin.salary-advance.index')
            ->with('success', 'Salary advance disbursement status updated successfully.');
    }

    /**
     * Remove an advance log.
     */
    public function destroy(SalaryAdvance $advance)
    {
        $advance->delete();
        return redirect()->route('admin.salary-advance.index')
            ->with('success', 'Salary advance record deleted successfully.');
    }
}
