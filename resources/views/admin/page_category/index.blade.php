@extends('admin.master')

@section('content')

<style>
:root {
    --accent:#4361ee; --accent-dk:#2563eb; --blue:#4361ee;
    --green:#22c55e; --green-dk:#16a34a; --warning:#f59e0b;
    --text:#1a1f36; --muted:#6b7a99; --border:#e4e9f2;
    --bg:#f0f2f5; --white:#ffffff; --radius:8px; --radius-sm:5px;
    --shadow:0 1px 4px rgba(0,0,0,.07);
}
.pc-page{padding:24px;background:var(--bg);min-height:100vh;font-family:'Segoe UI',system-ui,sans-serif;}
.pc-page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;}
.pc-page-title{font-size:20px;font-weight:800;color:var(--text);margin:0;}
.btn-add{display:inline-flex;align-items:center;gap:7px;padding:10px 20px;background:var(--accent);color:#fff;border:none;border-radius:var(--radius-sm);font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;}
.btn-add:hover{opacity:.9;color:#fff;}
.table-card{background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;}
.pc-table{width:100%;border-collapse:collapse;}
.pc-table thead tr{background:#f8fafc;}
.pc-table thead th{padding:12px 16px;text-align:left;font-size:11.5px;font-weight:700;color:var(--muted);text-transform:uppercase;border-bottom:2px solid var(--border);}
.pc-table tbody tr{border-bottom:1px solid #f0f2f5;}
.pc-table tbody td{padding:14px 16px;font-size:13px;color:var(--text);}
.status-badge{padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;}
.status-badge.active{background:#dcfce7;color:#15803d;}
.status-badge.inactive{background:#f3f4f6;color:#6b7280;}
.action-btns{display:flex;gap:8px;}
.btn-edit{padding:6px 12px;background:#dbeafe;color:#2563eb;border-radius:6px;font-size:12px;text-decoration:none;font-weight:600;}
.btn-delete{padding:6px 12px;background:#fee2e2;color:#dc2626;border-radius:6px;font-size:12px;border:none;cursor:pointer;font-weight:600;}
</style>

<div class="pc-page">
    <div class="pc-page-header">
        <h2 class="pc-page-title"><i class="bi bi-folder2-open" style="margin-right:8px;color:var(--accent);"></i>Page Categories</h2>
        <a href="{{ route('admin.page_categories.create') }}" class="btn-add"><i class="bi bi-plus-lg"></i> Add Category</a>
    </div>

    @if(session('success'))
        <div style="background:#dcfce7;color:#15803d;padding:12px;border-radius:8px;margin-bottom:15px;font-size:14px;font-weight:600;">
            <i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}
        </div>
    @endif

    <div class="table-card">
        <table class="pc-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="font-weight:700;">{{ $cat->name }}</td>
                    <td style="color:var(--muted);">{{ $cat->slug }}</td>
                    <td>
                        <span class="status-badge {{ $cat->status == 1 ? 'active' : 'inactive' }}">
                            {{ $cat->status == 1 ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-btns">
                            <a href="{{ route('admin.page_categories.edit', $cat->id) }}" class="btn-edit"><i class="bi bi-pencil"></i> Edit</a>
                            <form action="{{ route('admin.page_categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete"><i class="bi bi-trash"></i> Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:40px;color:var(--muted);">No categories found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
