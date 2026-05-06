@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1a1a2e; font-size:22px;">My Employees</h4>
            <p class="text-muted mb-0" style="font-size:13px;">This is a list of your Employees</p>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <a href="{{ route('seller.employeeseller.create') }}" class="btn-sup-create">
                <i class="bi bi-plus-lg me-1"></i> Add New Employee
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background:#f8f9fa;">
                            <th class="sup-th ps-4">SL</th>
                            <th class="sup-th">Name</th>
                            <th class="sup-th">Phone</th>
                            <th class="sup-th">Email</th>
                            <th class="sup-th">Gender</th>
                            <th class="sup-th">Role</th>
                            <th class="sup-th text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $i => $employee)
                        <tr class="sup-row">
                            <td class="ps-4 sup-td-muted">{{ $i + 1 }}</td>

                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($employee->profile_image)
                                        <img src="{{ asset($employee->profile_image) }}"
                                             class="rounded-circle sup-avatar" width="42" height="42"
                                             alt="{{ $employee->first_name }}" style="object-fit:cover;">
                                    @else
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                             style="width:42px; height:42px; font-size:18px; background:#e91e63;">
                                            {{ strtoupper(substr($employee->first_name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="sup-name">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                        <div class="sup-email">{{ $employee->email ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="sup-td-muted">{{ $employee->phone ?? '—' }}</td>
                            <td class="sup-td-muted">{{ $employee->email ?? '—' }}</td>
                            <td class="sup-td-muted">
                                <span class="badge-gender {{ $employee->gender }}">
                                    {{ ucfirst($employee->gender) }}
                                </span>
                            </td>
                            <td class="sup-td-muted">
                                <span class="badge-role">{{ ucfirst($employee->role) }}</span>
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Edit --}}
                                    <a href="{{ route('seller.employeeseller.edit', $employee->id) }}"
                                       class="sup-action-btn sup-edit" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    {{-- Delete --}}
                                    <form action="{{ route('seller.employeeseller.destroy', $employee->id) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sup-action-btn sup-delete" title="Delete">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-people fs-1 text-muted mb-3 d-block"></i>
                                <span class="text-muted" style="font-size:14px;">No employees found.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<style>
.btn-sup-create {
    background: linear-gradient(135deg, #e91e63, #c2185b);
    color:#fff !important; border:none; border-radius:8px;
    padding:9px 20px; font-size:14px; font-weight:500;
    text-decoration:none; display:inline-flex; align-items:center; gap:6px;
    transition:all .2s; box-shadow:0 3px 10px rgba(233,30,99,.35);
}
.btn-sup-create:hover {
    background: linear-gradient(135deg, #c2185b, #ad1457);
    transform:translateY(-1px);
}
.sup-th {
    font-size:13px; font-weight:600; color:#888;
    border-bottom:1px solid #f0f0f0 !important; border-top:none !important;
    text-transform:uppercase; letter-spacing:.4px; padding:14px 12px;
}
.sup-row { border-bottom:1px solid #f7f7f7 !important; transition:background .15s; }
.sup-row:hover { background:#fafafa !important; }
.sup-row:last-child { border-bottom:none !important; }
.sup-avatar { object-fit:cover; border:2px solid #f0f0f0; }
.sup-name { font-size:14px; font-weight:600; color:#222; }
.sup-email { font-size:12px; color:#999; }
.sup-td-muted { font-size:13px; color:#666; }

.badge-gender {
    display:inline-block; padding:3px 10px; border-radius:20px;
    font-size:11px; font-weight:600; text-transform:uppercase;
}
.badge-gender.male   { background:rgba(59,130,246,.12); color:#3b82f6; }
.badge-gender.female { background:rgba(233,30,99,.12);  color:#e91e63; }
.badge-gender.other  { background:rgba(107,114,128,.12);color:#6b7280; }

.badge-role {
    display:inline-block; padding:3px 10px; border-radius:20px;
    font-size:11px; font-weight:600; background:rgba(16,185,129,.12); color:#10b981;
}

/* Action buttons */
.sup-action-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:34px; height:34px; border-radius:8px; border:none;
    font-size:14px; cursor:pointer; transition:all .2s; text-decoration:none;
}
.sup-edit { background:rgba(59,130,246,.1); color:#3b82f6; }
.sup-edit:hover { background:#3b82f6; color:#fff; }
.sup-delete { background:rgba(239,68,68,.1); color:#ef4444; }
.sup-delete:hover { background:#ef4444; color:#fff; }
</style>
@endsection
