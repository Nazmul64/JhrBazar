<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payroll;
use App\Models\SalaryAdvance;
use App\Models\Expenditure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    /**
     * Display a listing of payroll history.
     */
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $payrolls = Payroll::with(['employee.department', 'employee.designation'])
            ->where('month', $month)
            ->where('year', $year)
            ->latest()
            ->get();

        $employees = User::where('role', 'employee')->orderBy('name')->get();

        // Calculate summary cards
        $totalPaid = Payroll::where('month', $month)->where('year', $year)->where('payment_status', 'Paid')->sum('net_salary');
        $totalUnpaid = Payroll::where('month', $month)->where('year', $year)->where('payment_status', 'Unpaid')->sum('net_salary');

        return view('admin.payroll.index', compact('payrolls', 'employees', 'month', 'year', 'totalPaid', 'totalUnpaid'));
    }

    /**
     * Show a generator form for payroll creation.
     */
    public function generate(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $employee = null;
        $activeAdvance = 0;
        $suggestedDeduction = 0;

        // Auto calculated corporate metrics
        $suggestedHRA = 0;
        $suggestedMedical = 0;
        $suggestedConveyance = 0;
        $suggestedPF = 0;
        $suggestedTax = 0;

        if ($employeeId) {
            $employee = User::with(['department', 'designation'])->findOrFail($employeeId);
            
            // Check active unpaid advances
            $advances = SalaryAdvance::where('employee_id', $employeeId)
                ->where('status', 'Approved')
                ->where('paid_status', 'Paid')
                ->get();
            
            $activeAdvance = $advances->sum('amount');
            
            foreach ($advances as $adv) {
                if ($adv->deduction_type === 'Monthly Deduct') {
                    $suggestedDeduction += $adv->monthly_deduction_amount;
                } elseif ($adv->deduction_type === 'One Time Deduct') {
                    $suggestedDeduction += $adv->amount;
                }
            }

            // Standard International Corporates split basic salaries:
            // HRA: 20%, Medical: 10%, Conveyance: 10%, PF: 10%, Tax: 5%
            $basic = $employee->salary ?? 0;
            $suggestedHRA = $basic * 0.20;
            $suggestedMedical = $basic * 0.10;
            $suggestedConveyance = $basic * 0.10;
            $suggestedPF = $basic * 0.10;
            $suggestedTax = $basic * 0.05;
        }

        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('admin.payroll.create', compact(
            'employees', 
            'employee', 
            'month', 
            'year', 
            'activeAdvance', 
            'suggestedDeduction',
            'suggestedHRA',
            'suggestedMedical',
            'suggestedConveyance',
            'suggestedPF',
            'suggestedTax'
        ));
    }

    /**
     * Store a generated payroll record.
     */
    public function storePayroll(Request $request)
    {
        $validated = $request->validate([
            'employee_id'          => 'required|exists:users,id',
            'month'                => 'required|integer|between:1,12',
            'year'                 => 'required|integer',
            'basic_salary'         => 'required|numeric|min:0',
            'house_rent_allowance' => 'required|numeric|min:0',
            'medical_allowance'    => 'required|numeric|min:0',
            'conveyance_allowance' => 'required|numeric|min:0',
            'extra_incentives'     => 'required|numeric|min:0',
            'allowances'           => 'required|numeric|min:0',
            'bonuses'              => 'required|numeric|min:0',
            'provident_fund'       => 'required|numeric|min:0',
            'professional_tax'     => 'required|numeric|min:0',
            'advances_deduction'   => 'required|numeric|min:0',
            'note'                 => 'nullable|string',
        ]);

        // Check if payroll already exists
        $exists = Payroll::where('employee_id', $validated['employee_id'])
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Payroll has already been generated for this employee for the selected month and year.');
        }

        // Net computation
        $validated['total_deductions'] = $validated['advances_deduction'] + $validated['provident_fund'] + $validated['professional_tax'];
        
        $grossEarnings = $validated['basic_salary'] + 
                         $validated['house_rent_allowance'] + 
                         $validated['medical_allowance'] + 
                         $validated['conveyance_allowance'] + 
                         $validated['extra_incentives'] + 
                         $validated['allowances'] + 
                         $validated['bonuses'];

        $validated['net_salary'] = max(0, $grossEarnings - $validated['total_deductions']);

        Payroll::create($validated);

        return redirect()->route('admin.payroll.index', ['month' => $validated['month'], 'year' => $validated['year']])
            ->with('success', 'Enterprise Payroll generated successfully.');
    }

    /**
     * Mark a payroll record as Paid and log as a Company Salary Expense.
     */
    public function pay(Request $request, Payroll $payroll)
    {
        $request->validate([
            'payment_method' => 'required|string|max:100',
            'note'           => 'nullable|string'
        ]);

        $payroll->update([
            'payment_status' => 'Paid',
            'payment_date'   => date('Y-m-d'),
            'payment_method' => $request->payment_method,
            'note'           => $request->note
        ]);

        // Automatically log this salary payment as an Expenditure (Company Expense)
        $monthName = date('F', mktime(0, 0, 0, $payroll->month, 10));
        Expenditure::create([
            'title'       => "Salary Outflow - " . $payroll->employee->name . " ({$monthName} {$payroll->year})",
            'category'    => 'Salaries',
            'amount'      => $payroll->net_salary,
            'date'        => date('Y-m-d'),
            'description' => "Monthly net salary disbursed via {$request->payment_method}. Note: " . ($request->note ?? ''),
            'created_by'  => Auth::id()
        ]);

        return redirect()->back()
            ->with('success', 'Payroll marked as paid and successfully logged in Expenditures!');
    }

    /**
     * Display a premium printable payslip view.
     */
    public function slip(Payroll $payroll)
    {
        $payroll->load(['employee.department', 'employee.designation']);
        return view('admin.payroll.slip', compact('payroll'));
    }

    /**
     * Remove a payroll log.
     */
    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->back()
            ->with('success', 'Payroll record deleted successfully.');
    }

    /**
     * Convert float/integer salary figures into clean English wording text.
     */
    public static function numberToWord($num) {
        $num = (int)$num;
        if ($num == 0) return 'zero';
        $words = array();
        $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
            'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
        );
        $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
        $list3 = array('', 'thousand', 'million', 'billion', 'trillion');
        
        $num_length = strlen($num);
        $levels = (int)(($num_length + 2) / 3);
        $max_length = $levels * 3;
        $num = str_pad($num, $max_length, "0", STR_PAD_LEFT);
        $num_levels = str_split($num, 3);
        
        for ($i = 0; $i < count($num_levels); $i++) {
            $levels--;
            $hundreds = (int)($num_levels[$i] / 100);
            $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred ' : '');
            $tens = (int)($num_levels[$i] % 100);
            $singles = '';
            if ($tens < 20) {
                $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '' );
            } else {
                $tens = (int)($tens / 10);
                $tens = ' ' . $list2[$tens] . ' ';
                $singles = (int)($num_levels[$i] % 10);
                $singles = ' ' . $list1[$singles] . ' ';
            }
            $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_levels[$i] ) ) ? ' ' . $list3[$levels] . ' ' : '' );
        }
        
        return preg_replace('/\s+/', ' ', trim(implode(' ', $words)));
    }
}
