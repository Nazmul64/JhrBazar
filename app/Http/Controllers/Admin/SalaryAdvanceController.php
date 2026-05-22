<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryAdvance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryAdvanceController extends Controller
{
    /** List with filters */
    public function index(Request $request)
    {
        $query = SalaryAdvance::with('employee', 'approver')->latest();

        // Filter by Employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Paid Status
        if ($request->filled('paid_status')) {
            $query->where('paid_status', $request->paid_status);
        }

        // Filter by Month/Year
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('request_date', $request->month)
                  ->whereYear('request_date', $request->year);
        }

        $advances = $query->paginate(20)->withQueryString();

        $status = $request->get('status', null);

        // Summary Stats
        $totalAdvanced  = SalaryAdvance::where('status', 'Approved')->sum('amount');
        $totalDeducted  = SalaryAdvance::where('status', 'Approved')->sum('deducted_amount');
        $totalPending   = SalaryAdvance::where('status', 'Pending')->count();
        $netOutstanding = max(0, $totalAdvanced - $totalDeducted);

        // Compatibility variables for existing views
        $totalApprovedAmount = $netOutstanding;
        $totalPendingRequests = $totalPending;

        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('admin/salary_advance.index', compact(
            'advances', 'employees', 'totalAdvanced',
            'totalDeducted', 'netOutstanding', 'totalPending',
            'totalApprovedAmount', 'totalPendingRequests'
        ,'status'
        ));
    }

    /** Show create form */
    public function create()
    {
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        return view('admin/salary_advance.create', compact('employees'));
    }

    /** Store new advance */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id'  => 'required|exists:users,id',
            'amount'       => 'required|numeric|min:1',
            'installments' => 'required|integer|min:1|max:24',
            'reason'       => 'nullable|string|max:500',
            'request_date' => 'required|date',
        ]);

        SalaryAdvance::create([
            'employee_id'     => $request->employee_id,
            'amount'          => $request->amount,
            'installments'    => $request->installments,
            'per_installment' => round($request->amount / $request->installments, 2),
            'reason'          => $request->reason,
            'request_date'    => $request->request_date,
            // also populate legacy advance_date column if exists
            'advance_date'    => $request->request_date,
            'status'          => 'Pending',
            'paid_status'     => 'Unpaid',
        ]);

        return redirect()->route('admin.hrm.salary-advance.index')
            ->with('success', 'Salary advance request created successfully.');
    }

    /** Approve */
    public function approve(Request $request, $id)
    {
        $advance = SalaryAdvance::findOrFail($id);

        $advance->update([
            'status'        => 'Approved',
            'approved_by'   => auth()->id(),
            'approved_date' => now()->toDateString(),
            'admin_note'    => $request->admin_note,
            'paid_status'   => 'Unpaid',
        ]);

        return back()->with('success', 'Advance approved successfully.');
    }

    /** Reject */
    public function reject(Request $request, $id)
    {
        $advance = SalaryAdvance::findOrFail($id);
        $advance->update([
            'status'     => 'Rejected',
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('success', 'Advance rejected.');
    }

    /** Manual deduction update */
    public function deduct(Request $request, $id)
    {
        $request->validate([
            'deduct_amount' => 'required|numeric|min:0.01',
        ]);

        $advance = SalaryAdvance::findOrFail($id);
        $newDeducted = $advance->deducted_amount + $request->deduct_amount;

        $paidStatus = 'Partial';
        $status     = 'Approved';
        if ($newDeducted >= $advance->amount) {
            $newDeducted = $advance->amount;
            $paidStatus  = 'Paid';
            $status      = 'Completed';
        }

        $advance->update([
            'deducted_amount' => $newDeducted,
            'paid_status'     => $paidStatus,
            'status'          => $status,
        ]);

        return back()->with('success', 'Deduction recorded successfully.');
    }

    /** Mark as paid/disbursed */
    public function pay(Request $request, $id)
    {
        $advance = SalaryAdvance::findOrFail($id);
        $advance->update([
            'paid_status' => 'Paid',
            'status' => $advance->status === 'Approved' ? 'Completed' : $advance->status,
        ]);

        return back()->with('success', 'Advance marked as paid.');
    }

    /** Delete */
    public function destroy($id)
    {
        SalaryAdvance::findOrFail($id)->delete();
        return back()->with('success', 'Record deleted.');
    }
}
