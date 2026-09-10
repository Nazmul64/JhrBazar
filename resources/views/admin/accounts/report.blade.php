<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Statement / Cashbook Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #1e293b;
        }
        .report-card {
            background: #fff;
            max-width: 960px;
            margin: 30px auto;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 40px;
        }
        .kpi-box {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .signature-line {
            width: 180px;
            border-top: 1px dashed #64748b;
            margin-top: 60px;
            padding-top: 8px;
            text-align: center;
            font-size: 13px;
            color: #475569;
        }
        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .report-card {
                box-shadow: none !important;
                margin: 0 !important;
                max-width: 100% !important;
                padding: 0 !important;
                border: none !important;
            }
        }
    </style>
</head>
<body>

<div class="no-print text-center py-3 bg-white border-bottom shadow-sm sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Ledger
        </a>
        <div>
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                <i class="bi bi-printer-fill me-1"></i> Print Statement
            </button>
        </div>
    </div>
</div>

<div class="report-card">

    {{-- ── Company Header ── --}}
    <div class="row align-items-center pb-4 border-bottom mb-4">
        <div class="col-8">
            <h3 class="fw-bold text-dark mb-1">{{ config('app.name', 'JhrBazar') }}</h3>
            <p class="text-muted small mb-0">Financial Accounts & Cashbook Statement</p>
            <div class="small text-secondary mt-1">
                <strong>Period:</strong>
                @if($startDate && $endDate)
                    {{ $startDate->format('d M, Y') }} to {{ $endDate->format('d M, Y') }}
                @elseif($startDate)
                    From {{ $startDate->format('d M, Y') }}
                @elseif($endDate)
                    Up to {{ $endDate->format('d M, Y') }}
                @else
                    All Time Complete History
                @endif
            </div>
        </div>
        <div class="col-4 text-end">
            <div class="small text-muted">Generated On:</div>
            <div class="fw-semibold text-dark">{{ date('d M, Y h:i A') }}</div>
        </div>
    </div>

    {{-- ── Summary Cards ── --}}
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="kpi-box bg-success-subtle border-success-subtle">
                <div class="small fw-semibold text-success text-uppercase">Total Income (Inflow)</div>
                <h4 class="fw-bold text-success mb-0 mt-1">৳{{ number_format($totalIncome, 2) }}</h4>
            </div>
        </div>
        <div class="col-4">
            <div class="kpi-box bg-danger-subtle border-danger-subtle">
                <div class="small fw-semibold text-danger text-uppercase">Total Expense (Outflow)</div>
                <h4 class="fw-bold text-danger mb-0 mt-1">৳{{ number_format($totalExpense, 2) }}</h4>
            </div>
        </div>
        <div class="col-4">
            <div class="kpi-box {{ $netBalance >= 0 ? 'bg-primary-subtle border-primary-subtle' : 'bg-warning-subtle border-warning-subtle' }}">
                <div class="small fw-semibold {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }} text-uppercase">Net Balance</div>
                <h4 class="fw-bold {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }} mb-0 mt-1">
                    ৳{{ number_format($netBalance, 2) }}
                </h4>
            </div>
        </div>
    </div>

    {{-- ── Ledger Table ── --}}
    <table class="table table-bordered table-sm align-middle mb-4" style="font-size: 13px;">
        <thead class="table-light text-uppercase" style="font-size: 11px;">
            <tr>
                <th style="width: 90px;">Date</th>
                <th style="width: 110px;">Voucher #</th>
                <th>Type</th>
                <th>Category</th>
                <th>Particulars / Details</th>
                <th class="text-end" style="width: 100px;">Income (৳)</th>
                <th class="text-end" style="width: 100px;">Expense (৳)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ledgers as $item)
                <tr>
                    <td>{{ $item->transaction_date ? $item->transaction_date->format('d/m/Y') : '' }}</td>
                    <td class="font-monospace">{{ $item->voucher_no }}</td>
                    <td>
                        <span class="badge {{ $item->transaction_type === 'income' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($item->transaction_type) }}
                        </span>
                    </td>
                    <td>{{ $item->category?->name ?? '—' }}</td>
                    <td>
                        <strong>{{ $item->title }}</strong>
                        @if($item->reference)
                            <span class="text-muted small"> (Ref: {{ $item->reference }})</span>
                        @endif
                    </td>
                    <td class="text-end font-monospace text-success fw-semibold">
                        {{ $item->income_amount > 0 ? number_format($item->income_amount, 2) : '—' }}
                    </td>
                    <td class="text-end font-monospace text-danger fw-semibold">
                        {{ $item->expense_amount > 0 ? number_format($item->expense_amount, 2) : '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No records found for the selected criteria.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot class="table-light fw-bold">
            <tr>
                <td colspan="5" class="text-end">Total Summary:</td>
                <td class="text-end text-success font-monospace">৳{{ number_format($totalIncome, 2) }}</td>
                <td class="text-end text-danger font-monospace">৳{{ number_format($totalExpense, 2) }}</td>
            </tr>
            <tr>
                <td colspan="5" class="text-end">Net Period Balance:</td>
                <td colspan="2" class="text-end font-monospace {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }}">
                    ৳{{ number_format($netBalance, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- ── Signatures ── --}}
    <div class="d-flex justify-content-between mt-5 pt-4">
        <div class="signature-line">Prepared By</div>
        <div class="signature-line">Verified By</div>
        <div class="signature-line">Authorized Signature</div>
    </div>

</div>

</body>
</html>
