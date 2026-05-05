@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color:#1a1a2e; font-size:22px;">User Management</h4>
        <a href="{{ route('admin.users.create') }}" class="btn-add-user">
            <i class="fas fa-plus me-1"></i> Add New User
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
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
                            <th class="ps-4 py-3 user-th">SL.</th>
                            <th class="py-3 user-th">Profile</th>
                            <th class="py-3 user-th">Name</th>
                            <th class="py-3 user-th">Phone / Email</th>
                            <th class="py-3 user-th">Role</th>
                            <th class="py-3 user-th text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                        <tr class="user-row">
                            <td class="ps-4 user-td-muted">{{ ($users->currentPage()-1) * $users->perPage() + $loop->iteration }}</td>

                            <td>
                                <img src="{{ $user->profile_image 
                                        ? asset($user->profile_image) 
                                        : asset('admin/images/default-avatar.png') }}"
                                     alt="{{ $user->name }}"
                                     class="rounded-circle user-avatar"
                                     width="38" height="38">
                            </td>

                            <td class="user-td-name">{{ $user->name }}</td>
                            <td>
                                <div class="user-td-muted">{{ $user->phone }}</div>
                                <div class="user-td-muted" style="font-size: 11px;">{{ $user->email }}</div>
                            </td>

                            <td>
                                @php
                                    $roleLower = strtolower($user->role ?? '');
                                    $badgeStyle = match($roleLower) {
                                        'admin'    => 'background:#3b82f6;',
                                        'manager'  => 'background:#8b5cf6;',
                                        'employee' => 'background:#f59e0b;',
                                        'customer' => 'background:#10b981;',
                                        'seller'   => 'background:#ef4444;',
                                        default    => 'background:#6b7280;',
                                    };
                                @endphp
                                <span class="user-badge" style="{{ $badgeStyle }}">
                                    {{ ucfirst($user->role ?? '—') }}
                                </span>
                            </td>

                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                       class="user-action-btn user-edit" title="Edit User">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.users.destroy', $user->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="user-action-btn user-del" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <span class="text-muted">No users found.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="p-3">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

</div>

<style>
.btn-add-user {
    display: inline-flex; align-items: center; gap: 6px;
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    color: #fff !important; border: none; border-radius: 8px;
    padding: 9px 20px; font-size: 14px; font-weight: 500;
    text-decoration: none; transition: all .2s ease;
    box-shadow: 0 3px 10px rgba(99, 102, 241, 0.3);
}
.user-th {
    font-size: 13px; font-weight: 600; color: #888;
    border-bottom: 1px solid #f0f0f0 !important;
    text-transform: uppercase; letter-spacing: .4px;
}
.user-row { border-bottom: 1px solid #f7f7f7 !important; transition: background .15s; }
.user-row:hover { background: #fafafa !important; }
.user-avatar { object-fit: cover; border: 2px solid #f0f0f0; }
.user-td-name { font-size: 14px; font-weight: 500; color: #222; }
.user-td-muted { font-size: 13px; color: #777; }
.user-badge {
    display: inline-block; color: #fff; font-size: 11px; font-weight: 500;
    padding: 3px 10px; border-radius: 4px;
}
.user-action-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 7px; border: none;
    font-size: 13px; cursor: pointer; transition: all .2s; text-decoration: none;
}
.user-edit { background: rgba(255,152,0,.1); color: #ff9800; }
.user-edit:hover { background: #ff9800; color: #fff; }
.user-del { background: rgba(244,67,54,.1); color: #f44336; }
.user-del:hover { background: #f44336; color: #fff; }
</style>
@endsection
