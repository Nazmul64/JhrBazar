@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1a1a2e; font-size:22px;">Supplier</h4>
            <p class="text-muted mb-0" style="font-size:13px;">This is a list of all Supplier</p>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            {{-- Search --}}
            <form method="GET" action="{{ route('admin.supplier.index') }}"
                  class="d-flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="sup-search-input" placeholder="Search ...">
                <button type="submit" class="btn-sup-search">
                    <i class="fas fa-search me-1"></i>Search
                </button>
            </form>
            <a href="{{ route('admin.supplier.create') }}" class="btn-sup-create">
                <i class="fas fa-plus me-1"></i> Create New supplier
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
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
                            <th class="sup-th text-center">Status</th>
                            <th class="sup-th text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $i => $supplier)
                        <tr class="sup-row">
                            <td class="ps-4 sup-td-muted">{{ $i + 1 }}</td>

                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $supplier->profile_image
                                            ? asset($supplier->profile_image)
                                            : asset('admin/images/default-avatar.png') }}"
                                         class="rounded-circle sup-avatar" width="42" height="42"
                                         alt="{{ $supplier->name }}">
                                    <div>
                                        <div class="sup-name">{{ $supplier->name }}</div>
                                        <div class="sup-email">{{ $supplier->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="sup-td-muted">{{ $supplier->phone }}</td>

                            <td class="text-center">
                                <form action="{{ route('admin.supplier.toggleStatus', $supplier->id) }}"
                                      method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="toggle-btn"
                                            title="{{ $supplier->is_active ? 'Disable' : 'Enable' }}">
                                        <div class="toggle-track {{ $supplier->is_active ? 'on' : 'off' }}">
                                            <div class="toggle-thumb"></div>
                                        </div>
                                    </button>
                                </form>
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.supplier.edit', $supplier->id) }}"
                                       class="sup-action-btn sup-edit" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    {{-- View --}}
                                    <a href="{{ route('admin.supplier.show', $supplier->id) }}"
                                       class="sup-action-btn sup-view" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                <span class="text-muted" style="font-size:14px;">No suppliers found.</span>
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
.sup-search-input {
    font-size:13px; border:1px solid #e2e8f0; border-radius:8px;
    padding:8px 14px; color:#333; background:#fff; width:200px;
    transition: border-color .2s;
}
.sup-search-input:focus { border-color:#e91e63; outline:none; }

.btn-sup-search {
    background:#e91e63; color:#fff; border:none; border-radius:8px;
    padding:8px 18px; font-size:13px; font-weight:500; cursor:pointer;
    display:inline-flex; align-items:center; gap:5px; transition:all .2s;
}
.btn-sup-search:hover { background:#c2185b; }

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

/* Toggle */
.toggle-btn { background:none; border:none; cursor:pointer; padding:0; }
.toggle-track {
    width:44px; height:24px; border-radius:12px; position:relative;
    transition:background .25s;
}
.toggle-track.on  { background:#e91e63; }
.toggle-track.off { background:#cbd5e0; }
.toggle-thumb {
    width:18px; height:18px; background:#fff; border-radius:50%;
    position:absolute; top:3px; transition:left .25s;
    box-shadow:0 1px 3px rgba(0,0,0,.2);
}
.toggle-track.on  .toggle-thumb { left:23px; }
.toggle-track.off .toggle-thumb { left:3px; }

/* Action buttons */
.sup-action-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:34px; height:34px; border-radius:8px; border:none;
    font-size:14px; cursor:pointer; transition:all .2s; text-decoration:none;
}
.sup-edit { background:rgba(59,130,246,.1); color:#3b82f6; }
.sup-edit:hover { background:#3b82f6; color:#fff; }
.sup-view { background:rgba(233,30,99,.1); color:#e91e63; }
.sup-view:hover { background:#e91e63; color:#fff; }
</style>
@endsection
