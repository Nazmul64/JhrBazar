@extends('admin.master')
@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1a1a2e;font-size:22px;">Expense Categories</h4>
            <p class="text-muted mb-0" style="font-size:13px;">Manage categories for office expenses.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form action="{{ route('admin.hrm.expense.category.store') }}" method="POST" class="mb-3">
                @csrf
                <div class="d-flex gap-2">
                    <input name="name" class="form-control" placeholder="New category name" required>
                    <input name="color" type="color" value="#6366f1">
                    <button class="btn btn-primary">Add</button>
                </div>
            </form>
            <ul class="list-group">
                @foreach($categories as $cat)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div><span class="badge me-2" style="background:{{ $cat->color }};width:12px;height:12px;display:inline-block;border-radius:3px"></span>{{ $cat->name }}</div>
                        <form action="{{ route('admin.hrm.expense.category.destroy', $cat->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
