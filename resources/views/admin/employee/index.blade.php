@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color:#1a1a2e; font-size:22px;">Employees</h4>
        <a href="{{ route('admin.employees.create') }}" class="btn-add-emp">
            <i class="fas fa-plus me-1"></i> Add Employee
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background:#f8f9fa;">
                            <th class="ps-4 py-3 emp-th">SL.</th>
                            <th class="py-3 emp-th">Profile</th>
                            <th class="py-3 emp-th">Name</th>
                            <th class="py-3 emp-th">Phone</th>
                            <th class="py-3 emp-th">Email</th>
                            <th class="py-3 emp-th">Role</th>
                            <th class="py-3 emp-th text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $index => $employee)
                        <tr class="emp-row">
                            <td class="ps-4 emp-td-muted">{{ $index + 1 }}</td>

                            <td>
                                {{-- ✅ asset() ব্যবহার করা হচ্ছে, storage নয় --}}
                                <img src="{{ $employee->profile_image
                                        ? asset($employee->profile_image)
                                        : asset('admin/images/default-avatar.png') }}"
                                     alt="{{ $employee->name }}"
                                     class="rounded-circle emp-avatar"
                                     width="38" height="38">
                            </td>

                            <td class="emp-td-name">{{ $employee->name }}</td>
                            <td class="emp-td-muted">{{ $employee->phone }}</td>
                            <td class="emp-td-muted">{{ $employee->email }}</td>

                            <td>
                                @php
                                    $roleLower = strtolower($employee->role ?? '');
                                    $badgeStyle = match($roleLower) {
                                        'admin'    => 'background:#3b82f6;',
                                        'supplier' => 'background:#6b7280;',
                                        'visitor'  => 'background:#6b7280;',
                                        'root'     => 'background:#16a34a;',
                                        'manager'  => 'background:#8b5cf6;',
                                        'staff'    => 'background:#f59e0b;',
                                        default    => 'background:#6b7280;',
                                    };
                                @endphp
                                <span class="emp-badge" style="{{ $badgeStyle }}">
                                    {{ $employee->role ?? '—' }}
                                </span>
                            </td>

                            <td>
                                @if(!in_array($roleLower, ['root']))
                                <div class="d-flex justify-content-center gap-2">

                                    {{-- Permission --}}
                                    <a href="{{ route('admin.employees.permission', $employee->id) }}"
                                       class="emp-action-btn emp-perm" title="Manage Permissions">
                                        <i class="fas fa-user-cog"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.employees.edit', $employee->id) }}"
                                       class="emp-action-btn emp-edit" title="Edit Employee">
                                        <i class="fas fa-key"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.employees.destroy', $employee->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="emp-action-btn emp-del" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </div>
                                @else
                                    <div class="text-center emp-td-muted">N/A</div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
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
.btn-add-emp {
    display: inline-flex; align-items: center; gap: 6px;
    background: linear-gradient(135deg, #e91e63, #c2185b);
    color: #fff !important; border: none; border-radius: 8px;
    padding: 9px 20px; font-size: 14px; font-weight: 500;
    text-decoration: none; transition: all .2s ease;
    box-shadow: 0 3px 10px rgba(233,30,99,.35);
}
.btn-add-emp:hover {
    background: linear-gradient(135deg, #c2185b, #ad1457);
    transform: translateY(-1px); box-shadow: 0 5px 15px rgba(233,30,99,.4);
}
.emp-th {
    font-size: 13px; font-weight: 600; color: #888;
    border-bottom: 1px solid #f0f0f0 !important; border-top: none !important;
    text-transform: uppercase; letter-spacing: .4px;
}
.emp-row { border-bottom: 1px solid #f7f7f7 !important; transition: background .15s; }
.emp-row:hover { background: #fafafa !important; }
.emp-row:last-child { border-bottom: none !important; }
.emp-avatar { object-fit: cover; border: 2px solid #f0f0f0; }
.emp-td-name { font-size: 14px; font-weight: 500; color: #222; }
.emp-td-muted { font-size: 13px; color: #777; }
.emp-badge {
    display: inline-block; color: #fff; font-size: 11px; font-weight: 500;
    padding: 3px 10px; border-radius: 4px; letter-spacing: .3px;
}
.emp-action-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 7px; border: none;
    font-size: 13px; cursor: pointer; transition: all .2s; text-decoration: none;
}
.emp-perm { background: rgba(233,30,99,.1); color: #e91e63; }
.emp-perm:hover { background: #e91e63; color: #fff; }
.emp-edit { background: rgba(255,152,0,.1); color: #ff9800; }
.emp-edit:hover { background: #ff9800; color: #fff; }
.emp-del { background: rgba(244,67,54,.1); color: #f44336; }
.emp-del:hover { background: #f44336; color: #fff; }
</style>
@endsection
