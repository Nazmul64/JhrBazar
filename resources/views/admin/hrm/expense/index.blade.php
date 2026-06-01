@extends('admin.master')
@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1a1a2e;font-size:22px;">Office Expenses</h4>
            <p class="text-muted mb-0" style="font-size:13px;">Manage office operational expenses.</p>
        </div>
        <a href="{{ route('admin.hrm.expense.create') }}" class="btn-hrm-add">
            <i class="fas fa-plus me-1"></i> Add Expense
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background:#f8f9fa;">
                            <th class="ps-4 py-3">SL</th>
                            <th class="py-3">Title</th>
                            <th class="py-3">Amount</th>
                            <th class="py-3">Category</th>
                            <th class="py-3">Date</th>
                            <th class="py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $i => $exp)
                            <tr>
                                <td class="ps-4">{{ $i + 1 }}</td>
                                <td>{{ $exp->title }}</td>
                                <td>{{ number_format($exp->amount,2) }}</td>
                                <td>{{ $exp->category?->name ?? '—' }}</td>
                                <td>{{ date('d M, Y', strtotime($exp->expense_date)) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.hrm.expense.edit', $exp->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form action="{{ route('admin.hrm.expense.destroy', $exp->id) }}" method="POST" style="display:inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-4">No expenses recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
