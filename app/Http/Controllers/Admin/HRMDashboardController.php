<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\SalaryAdvance;
use App\Models\Payroll;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HRMDashboardController extends Controller
{
    /**
     * Show Executive HRM Analytics Dashboard.
     */
    public function index()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        // 1. Core KPIs
        $totalEmployees = User::where('role', 'employee')->count();

        // Pending leaves
        $pendingLeaves = Leave::where('status', 'Pending')->count();

        // Unpaid Outstanding Advances
        $outstandingAdvances = SalaryAdvance::where('status', 'Approved')
            ->where('paid_status', 'Paid') // Disbursed
            // outstanding is amount - amount deducted in paid payrolls,
            // for simplicity, let's sum total active advances that aren't fully completed
            ->sum('amount');
        
        // Sum of deductions paid so far
        $totalDeductedSoFar = Payroll::where('payment_status', 'Paid')->sum('advances_deduction');
        $netOutstanding = max(0, $outstandingAdvances - $totalDeductedSoFar);

        // Total Salary Disbursed this month
        $totalSalaryPaidThisMonth = Payroll::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->where('payment_status', 'Paid')
            ->sum('net_salary');

        // 2. Attendance Analysis
        $today = date('Y-m-d');
        $todayPresent = Attendance::where('date', $today)->where('status', 'Present')->count();
        $todayLate = Attendance::where('date', $today)->where('status', 'Present')->where('late_minutes', '>', 0)->count();
        $todayAbsent = Attendance::where('date', $today)->where('status', 'Absent')->count();

        // 3. Department Employee Counts
        $departments = Department::withCount('employees')->get();

        // 4. Monthly Payroll Disbursements Chart Data
        $payrollStats = Payroll::select('month', 'year', DB::raw('SUM(net_salary) as total_net'))
            ->where('payment_status', 'Paid')
            ->groupBy('month', 'year')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get()
            ->reverse();

        // 5. Recent Clock Punches (Today's checkins)
        $recentPunches = Attendance::with('employee')
            ->where('date', $today)
            ->orderBy('clock_in', 'desc')
            ->take(5)
            ->get();

        return view('admin.hrm.dashboard', compact(
            'totalEmployees',
            'pendingLeaves',
            'netOutstanding',
            'totalSalaryPaidThisMonth',
            'todayPresent',
            'todayLate',
            'todayAbsent',
            'departments',
            'payrollStats',
            'recentPunches'
        ));
    }
}
