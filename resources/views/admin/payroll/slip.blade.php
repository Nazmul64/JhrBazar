<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip Voucher - {{ $payroll->employee->name }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f3f4f6; font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #374151; }
        .payslip-container { max-width: 850px; margin: 40px auto; background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); border: 1px solid #e5e7eb; }
        .company-header { border-bottom: 2px solid #f3f4f6; padding-bottom: 24px; margin-bottom: 30px; }
        .text-premium { color: #c2185b !important; }
        .table-custom th { background-color: #f9fafb; font-weight: 600; font-size: 13px; color: #4b5563; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e5e7eb !important; }
        .table-custom td { font-size: 13.5px; padding: 10px 16px; border-bottom: 1px solid #f3f4f6; }
        .table-custom tr.total-row td { background-color: #f9fafb; font-weight: 700; color: #111827; }
        .net-pay-card { background: linear-gradient(135deg, #1a1a2e, #162447); color: #ffffff; border-radius: 8px; padding: 24px; text-align: center; margin-top: 30px; }
        .sig-block { margin-top: 60px; padding-top: 20px; border-top: 1.5px dashed #e5e7eb; }
        @media print {
            body { background: none; }
            .payslip-container { box-shadow: none; border: none; margin: 0; padding: 0; }
            .no-print { display: none !important; }
            .net-pay-card { background: #1a1a2e !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="payslip-container">
        
        {{-- Slip Actions --}}
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <a href="{{ route('admin.payroll.index') }}" class="btn btn-outline-secondary btn-sm" style="border-radius:6px;">
                <i class="fas fa-arrow-left me-1"></i> Back to Payrolls
            </a>
            <button onclick="window.print()" class="btn btn-sm text-white" style="background: linear-gradient(135deg, #e91e63, #c2185b); border: none; border-radius:6px; padding: 6px 16px;">
                <i class="fas fa-print me-1"></i> Print Payslip
            </button>
        </div>

        {{-- Company Header --}}
        <div class="company-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-1 text-premium">JhrBazar Corporate Ltd.</h3>
                <p class="text-muted mb-0" style="font-size:12.5px;">Plot 12, Block C, Banani, Dhaka, Bangladesh</p>
            </div>
            <div class="text-end">
                <span class="badge bg-soft-premium px-3 py-2 text-premium fw-bold rounded-pill" style="font-size:12.5px; background: rgba(233,30,99,0.1);">
                    PAYSLIP VOUCHER
                </span>
                <div class="text-muted mt-1" style="font-size:12px;">Reference: JB-HR-{{ $payroll->year }}-{{ sprintf('%02d', $payroll->month) }}-{{ $payroll->id }}</div>
            </div>
        </div>

        {{-- Employee Details Grid --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6 border-end">
                <h6 class="fw-bold mb-2 text-secondary" style="font-size:12px; text-transform:uppercase;">Employee Information</h6>
                <div style="font-size:13.5px; line-height: 1.6;">
                    <strong>Name:</strong> {{ $payroll->employee->name }}<br>
                    <strong>ID Number:</strong> #EMP-{{ sprintf('%04d', $payroll->employee->id) }}<br>
                    <strong>Department:</strong> {{ $payroll->employee->department->name ?? 'Management & Administration' }}<br>
                    <strong>Designation:</strong> {{ $payroll->employee->designation->name ?? 'Executive Associate' }}<br>
                    <strong>Joining Date:</strong> {{ $payroll->employee->joining_date ?? 'N/A' }}
                </div>
            </div>
            <div class="col-md-6 ps-md-4">
                <h6 class="fw-bold mb-2 text-secondary" style="font-size:12px; text-transform:uppercase;">Payout Details</h6>
                <div style="font-size:13.5px; line-height: 1.6;">
                    <strong>Pay Period:</strong> {{ date('F', mktime(0, 0, 0, $payroll->month, 10)) }} {{ $payroll->year }}<br>
                    <strong>Disbursement Date:</strong> {{ $payroll->payment_date ? date('d-M-Y', strtotime($payroll->payment_date)) : 'Pending' }}<br>
                    <strong>Payment Method:</strong> {{ $payroll->payment_method ?? 'Not Disbursed' }}<br>
                    <strong>Status:</strong> 
                    @if($payroll->payment_status === 'Paid')
                        <span class="badge bg-success">Disbursed</span>
                    @else
                        <span class="badge bg-danger">Unpaid</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Calculation Grid --}}
        <div class="row g-4 mb-4">
            
            {{-- Earnings Columns --}}
            <div class="col-md-6">
                <h6 class="fw-bold text-success border-bottom pb-2 mb-0" style="font-size: 13px; text-transform: uppercase;">1. Earnings & Allowances</h6>
                <div class="table-responsive">
                    <table class="table table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Amount (BDT)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Base Basic Salary</td>
                                <td class="text-end fw-bold">৳{{ number_format($payroll->basic_salary, 2) }}</td>
                            </tr>
                            <tr>
                                <td>House Rent Allowance (HRA)</td>
                                <td class="text-end">৳{{ number_format($payroll->house_rent_allowance, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Medical Allowance</td>
                                <td class="text-end">৳{{ number_format($payroll->medical_allowance, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Conveyance / Transport</td>
                                <td class="text-end">৳{{ number_format($payroll->conveyance_allowance, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Performance Incentives</td>
                                <td class="text-end">৳{{ number_format($payroll->extra_incentives, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Bonuses</td>
                                <td class="text-end">৳{{ number_format($payroll->bonuses, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Other Perks</td>
                                <td class="text-end">৳{{ number_format($payroll->allowances, 2) }}</td>
                            </tr>
                            @php
                                $grossEarnings = $payroll->basic_salary + $payroll->house_rent_allowance + $payroll->medical_allowance + $payroll->conveyance_allowance + $payroll->extra_incentives + $payroll->bonuses + $payroll->allowances;
                            @endphp
                            <tr class="total-row">
                                <td>Gross Earnings</td>
                                <td class="text-end text-success">৳{{ number_format($grossEarnings, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Deductions Columns --}}
            <div class="col-md-6">
                <h6 class="fw-bold text-danger border-bottom pb-2 mb-0" style="font-size: 13px; text-transform: uppercase;">2. Statutory Deductions</h6>
                <div class="table-responsive">
                    <table class="table table-custom align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Amount (BDT)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Provident Fund (PF)</td>
                                <td class="text-end">৳{{ number_format($payroll->provident_fund, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Professional Tax / TDS</td>
                                <td class="text-end">৳{{ number_format($payroll->professional_tax, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Salary Advance / Loan Repay</td>
                                <td class="text-end">৳{{ number_format($payroll->advances_deduction, 2) }}</td>
                            </tr>
                            {{-- Add empty spacer rows to match heights --}}
                            <tr>
                                <td style="color:transparent;">—</td>
                                <td style="color:transparent;">—</td>
                            </tr>
                            <tr>
                                <td style="color:transparent;">—</td>
                                <td style="color:transparent;">—</td>
                            </tr>
                            <tr>
                                <td style="color:transparent;">—</td>
                                <td style="color:transparent;">—</td>
                            </tr>
                            <tr>
                                <td style="color:transparent;">—</td>
                                <td style="color:transparent;">—</td>
                            </tr>
                            <tr class="total-row">
                                <td>Total Deductions</td>
                                <td class="text-end text-danger">৳{{ number_format($payroll->total_deductions, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Net Pay Tally Card --}}
        <div class="net-pay-card">
            <span class="d-block mb-1 opacity-75" style="font-size: 12.5px; text-transform: uppercase; font-weight: 600;">Net Payable Salary Outflow</span>
            <h2 class="fw-bold mb-1" style="font-size:36px;">৳{{ number_format($payroll->net_salary, 2) }}</h2>
            <span class="opacity-75" style="font-size: 13px; font-style:italic;">
                (In Words: BDT {{ ucwords(\App\Http\Controllers\Admin\PayrollController::numberToWord($payroll->net_salary)) }} Only)
            </span>
        </div>

        @if($payroll->note)
            <div class="mt-4 p-3 bg-light rounded-3 border" style="font-size: 13px;">
                <strong>Payroll Remarks:</strong> {{ $payroll->note }}
            </div>
        @endif

        {{-- Signature Blocks --}}
        <div class="row sig-block text-center flex-nowrap" style="font-size:13px; font-weight: 500;">
            <div class="col-6">
                <br><br><br>
                <div class="border-top pt-2 mx-auto" style="width: 180px; border-color: #ccc !important;">
                    Employee Signature
                </div>
            </div>
            <div class="col-6">
                <br><br><br>
                <div class="border-top pt-2 mx-auto" style="width: 180px; border-color: #ccc !important;">
                    HR Manager Authorization
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
