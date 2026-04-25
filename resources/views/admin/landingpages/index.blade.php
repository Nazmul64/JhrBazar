@extends('admin.master')

@section('content')

@php
    $settings = \App\Models\GenaralSetting::first();
    $cur = $settings->default_currency ?? '৳';
@endphp

<style>
:root {
    --accent:#e7567c; --accent-dk:#c93f65; --blue:#4361ee;
    --green:#22c55e; --green-dk:#16a34a; --warning:#f59e0b;
    --text:#1a1f36; --muted:#6b7a99; --border:#e4e9f2;
    --bg:#f0f2f5; --white:#ffffff; --radius:8px; --radius-sm:5px;
    --shadow:0 1px 4px rgba(0,0,0,.07);
}
*,*::before,*::after{box-sizing:border-box;}

.lp-page{padding:24px;background:var(--bg);min-height:100vh;font-family:'Segoe UI',system-ui,sans-serif;}

.lp-page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px;}
.lp-page-title{font-size:20px;font-weight:800;color:var(--text);margin:0;}

.btn-add-new{display:inline-flex;align-items:center;gap:7px;padding:10px 20px;background:linear-gradient(135deg,#e7567c,#c93f65);color:#fff;border:none;border-radius:var(--radius-sm);font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;transition:opacity .15s;white-space:nowrap;}
.btn-add-new:hover{opacity:.88;color:#fff;text-decoration:none;}

.summary-row{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;}
.summary-card{background:var(--white);border-radius:var(--radius);padding:18px 20px;box-shadow:var(--shadow);display:flex;align-items:center;gap:14px;}
.sum-icon{width:46px;height:46px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
.sum-icon.blue{background:#dbeafe;color:#2563eb;} .sum-icon.green{background:#dcfce7;color:#16a34a;} .sum-icon.purple{background:#ede9fe;color:#7c3aed;}
.sum-value{font-size:20px;font-weight:800;color:var(--text);line-height:1.2;}
.sum-label{font-size:12px;color:var(--muted);margin-top:2px;}

.filter-card{background:var(--white);border-radius:var(--radius);padding:16px 20px;box-shadow:var(--shadow);margin-bottom:20px;display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;}
.filter-group{display:flex;flex-direction:column;gap:5px;flex:1;min-width:160px;}
.filter-label{font-size:11.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.3px;}
.filter-input{height:38px;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:0 12px;font-size:13px;color:var(--text);background:var(--white);outline:none;transition:border-color .15s;width:100%;font-family:inherit;}
.filter-input:focus{border-color:var(--accent);}
select.filter-input{appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%236b7a99' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;padding-right:32px;cursor:pointer;}
.filter-actions{display:flex;gap:8px;align-items:flex-end;}
.btn-filter{height:38px;padding:0 20px;background:var(--accent);color:var(--white);border:none;border-radius:var(--radius-sm);font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-family:inherit;transition:background .15s;white-space:nowrap;}
.btn-filter:hover{background:var(--accent-dk);}
.btn-reset{height:38px;padding:0 16px;background:#f1f5f9;color:var(--muted);border:1.5px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:6px;font-family:inherit;transition:background .15s;white-space:nowrap;}
.btn-reset:hover{background:#e2e8f0;color:var(--text);text-decoration:none;}

.table-card{background:var(--white);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;}
.table-card-top{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:10px;}
.table-card-title{font-size:15px;font-weight:700;color:var(--text);margin:0;}
.count-badge{background:#f3f4f6;color:var(--muted);border-radius:20px;padding:3px 10px;font-size:12px;font-weight:600;}

.lp-table{width:100%;border-collapse:collapse;}
.lp-table thead tr{background:#f8fafc;}
.lp-table thead th{padding:11px 16px;text-align:left;font-size:11.5px;font-weight:700;color:var(--muted);white-space:nowrap;text-transform:uppercase;letter-spacing:.4px;border-bottom:2px solid var(--border);}
.lp-table tbody tr{border-bottom:1px solid #f0f2f5;transition:background .12s;}
.lp-table tbody tr:last-child{border-bottom:none;}
.lp-table tbody tr:hover{background:#fafbff;}
.lp-table tbody td{padding:13px 16px;font-size:13px;color:var(--text);vertical-align:middle;}

.lp-thumb{width:54px;height:54px;object-fit:cover;border-radius:8px;border:1px solid var(--border);background:#f1f5f9;}
.lp-thumb-ph{width:54px;height:54px;border-radius:8px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;font-size:22px;color:#d1d5db;border:1px solid var(--border);}
.lp-title-cell{display:flex;align-items:center;gap:12px;}
.lp-title-text{font-weight:700;color:var(--text);font-size:14px;}
.lp-product-tag{font-size:11px;color:var(--muted);margin-top:2px;}

.media-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:5px;font-size:11.5px;font-weight:600;}
.media-badge.image{background:#dbeafe;color:#1d4ed8;}
.media-badge.video{background:#fce7f3;color:#be185d;}

.reviews-count{background:#f3f4f6;color:#374151;padding:3px 8px;border-radius:20px;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:4px;}

.status-toggle-form{display:inline;}
.status-btn{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;cursor:pointer;border:none;font-family:inherit;transition:all .15s;}
.status-btn.active{background:#dcfce7;color:#15803d;} .status-btn.active:hover{background:#bbf7d0;}
.status-btn.inactive{background:#f3f4f6;color:#6b7280;} .status-btn.inactive:hover{background:#e5e7eb;}
.status-dot{width:7px;height:7px;border-radius:50%;display:inline-block;flex-shrink:0;}
.status-dot.active{background:#15803d;} .status-dot.inactive{background:#6b7280;}

.action-cell{display:flex;align-items:center;gap:8px;}
.btn-edit{display:inline-flex;align-items:center;gap:5px;padding:7px 14px;background:#dbeafe;color:#2563eb;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;border:none;cursor:pointer;transition:background .15s;white-space:nowrap;}
.btn-edit:hover{background:#bfdbfe;color:#1d4ed8;text-decoration:none;}
.btn-delete{display:inline-flex;align-items:center;gap:5px;padding:7px 12px;background:#fee2e2;color:#dc2626;border-radius:6px;font-size:12px;font-weight:600;border:none;cursor:pointer;transition:background .15s;white-space:nowrap;font-family:inherit;}
.btn-delete:hover{background:#fecaca;}

.empty-state{text-align:center;padding:60px 20px;}
.empty-icon{font-size:52px;color:#d1d5db;display:block;margin-bottom:14px;}
.empty-state h4{font-size:16px;color:#374151;font-weight:700;margin-bottom:6px;}
.empty-state p{font-size:13.5px;color:var(--muted);margin:0;}

.pagi-area{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid var(--border);flex-wrap:wrap;gap:10px;}
.pagi-info{font-size:13px;color:var(--muted);}

.alert-ok{background:#ecfdf5;border:1px solid #6ee7b7;color:#065f46;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;font-weight:500;}
.alert-err{background:#fff1f2;border:1px solid #fecdd3;color:#be123c;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;font-weight:500;}

/* Modal */
.confirm-overlay{position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:50000;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .2s;padding:16px;}
.confirm-overlay.show{opacity:1;pointer-events:all;}
.confirm-modal{background:var(--white);border-radius:14px;width:420px;max-width:100%;box-shadow:0 24px 64px rgba(0,0,0,.22);transform:scale(.96) translateY(8px);transition:transform .2s;overflow:hidden;}
.confirm-overlay.show .confirm-modal{transform:scale(1) translateY(0);}
.confirm-head{padding:20px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px;}
.confirm-icon{width:44px;height:44px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;font-size:20px;color:#dc2626;flex-shrink:0;}
.confirm-head h5{font-size:16px;font-weight:700;color:var(--text);margin:0;}
.confirm-body{padding:20px 22px;}
.confirm-body p{font-size:14px;color:var(--muted);margin:0;line-height:1.6;}
.confirm-foot{padding:14px 22px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;}
.btn-cancel-modal{height:40px;padding:0 20px;background:#f1f5f9;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;cursor:pointer;color:var(--muted);font-family:inherit;}
.btn-confirm-delete{height:40px;padding:0 20px;background:#dc2626;color:#fff;border:none;border-radius:var(--radius-sm);font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;display:inline-flex;align-items:center;gap:6px;}
.btn-confirm-delete:hover{background:#b91c1c;}

.toast-container{position:fixed;bottom:24px;right:24px;z-index:99999;display:flex;flex-direction:column;gap:8px;}
.pos-toast{background:#1e293b;color:#fff;border-radius:var(--radius);padding:12px 18px;font-size:13px;font-weight:500;box-shadow:0 8px 24px rgba(0,0,0,.2);display:flex;align-items:center;gap:10px;min-width:260px;animation:tIn .3s ease;}
.pos-toast.t-success{background:#15803d;} .pos-toast.t-error{background:#be123c;}
@keyframes tIn{from{opacity:0;transform:translateX(40px);}to{opacity:1;transform:translateX(0);}}
@keyframes tOut{from{opacity:1;}to{opacity:0;transform:translateX(40px);}}

@media(max-width:900px){.summary-row{grid-template-columns:1fr 1fr;}}
@media(max-width:560px){.summary-row{grid-template-columns:1fr;}.table-card{overflow-x:auto;}}
</style>

<div class="lp-page">

    <div class="lp-page-header">
        <h2 class="lp-page-title">
            <i class="bi bi-layout-text-window-reverse" style="color:var(--accent);margin-right:6px;"></i>
            Landing Pages
        </h2>
        <a href="{{ route('admin.landingpages.create') }}" class="btn-add-new">
            <i class="bi bi-plus-circle"></i> Add New
        </a>
    </div>

    @if(session('success'))
        <div class="alert-ok"><i class="bi bi-check-circle-fill" style="margin-right:6px;"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-err"><i class="bi bi-x-circle-fill" style="margin-right:6px;"></i>{{ session('error') }}</div>
    @endif

    {{-- Summary Cards --}}
    @php
        $total  = \App\Models\Landingpage::count();
        $active = \App\Models\Landingpage::where('status',1)->count();
        $draft  = \App\Models\Landingpage::where('status',0)->count();
    @endphp
    <div class="summary-row">
        <div class="summary-card">
            <div class="sum-icon blue"><i class="bi bi-layout-text-window-reverse"></i></div>
            <div><div class="sum-value">{{ $total }}</div><div class="sum-label">Total Pages</div></div>
        </div>
        <div class="summary-card">
            <div class="sum-icon green"><i class="bi bi-check-circle"></i></div>
            <div><div class="sum-value">{{ $active }}</div><div class="sum-label">Active Pages</div></div>
        </div>
        <div class="summary-card">
            <div class="sum-icon purple"><i class="bi bi-pause-circle"></i></div>
            <div><div class="sum-value">{{ $draft }}</div><div class="sum-label">Inactive Pages</div></div>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('admin.landingpages.index') }}">
        <div class="filter-card">
            <div class="filter-group">
                <label class="filter-label">Search</label>
                <input type="text" name="search" class="filter-input"
                       placeholder="Search by title..." value="{{ request('search') }}">
            </div>
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select name="status" class="filter-input">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-filter"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('admin.landingpages.index') }}" class="btn-reset"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="table-card">
        <div class="table-card-top">
            <div style="display:flex;align-items:center;gap:10px;">
                <h3 class="table-card-title">All Landing Pages</h3>
                <span class="count-badge">{{ $landingpages->total() }} records</span>
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table class="lp-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Page</th>
                        <th>Media</th>
                        <th>Reviews</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($landingpages as $i => $lp)
                        <tr>
                            <td style="color:var(--muted);font-size:12px;font-weight:600;">{{ $landingpages->firstItem() + $i }}</td>

                            <td>
                                <div class="lp-title-cell">
                                    @if($lp->image && file_exists(public_path($lp->image)))
                                        <img class="lp-thumb" src="{{ asset($lp->image) }}" alt="{{ $lp->title }}">
                                    @else
                                        <div class="lp-thumb-ph">🖼️</div>
                                    @endif
                                    <div>
                                        <div class="lp-title-text">{{ Str::limit($lp->title, 40) }}</div>
                                        @if($lp->product)
                                            <div class="lp-product-tag">
                                                <i class="bi bi-box-seam" style="color:var(--blue);"></i>
                                                {{ Str::limit($lp->product->name, 30) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="media-badge {{ $lp->media_type }}">
                                    <i class="bi bi-{{ $lp->media_type === 'image' ? 'image' : 'camera-video' }}"></i>
                                    {{ ucfirst($lp->media_type) }}
                                </span>
                            </td>

                            <td>
                                <span class="reviews-count">
                                    <i class="bi bi-star" style="color:var(--warning);"></i>
                                    {{ count($lp->reviews ?? []) }}
                                </span>
                            </td>

                            <td>
                                <form class="status-toggle-form"
                                      action="{{ route('admin.landingpages.toggle-status', $lp->id) }}"
                                      method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="status-btn {{ $lp->status ? 'active' : 'inactive' }}">
                                        <span class="status-dot {{ $lp->status ? 'active' : 'inactive' }}"></span>
                                        {{ $lp->status ? 'Active' : 'Inactive' }}
                                    </button>
                                </form>
                            </td>

                            <td style="color:var(--muted);font-size:12px;">
                                {{ $lp->created_at?->format('d M Y') }}<br>
                                <span style="font-size:11px;">{{ $lp->created_at?->diffForHumans() }}</span>
                            </td>

                            <td>
                                <div class="action-cell">
                                    <a href="{{ route('admin.landingpages.edit', $lp->id) }}" class="btn-edit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <button class="btn-delete"
                                            onclick="confirmDelete({{ $lp->id }}, '{{ addslashes($lp->title) }}', '{{ route('admin.landingpages.destroy', $lp->id) }}')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7">
                            <div class="empty-state">
                                <span class="empty-icon bi bi-layout-text-window-reverse"></span>
                                <h4>No Landing Pages Found</h4>
                                <p>Create your first landing page to get started.</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($landingpages->hasPages())
            <div class="pagi-area">
                <div class="pagi-info">
                    Showing <strong>{{ $landingpages->firstItem() }}</strong>
                    to <strong>{{ $landingpages->lastItem() }}</strong>
                    of <strong>{{ $landingpages->total() }}</strong> results
                </div>
                <div>{{ $landingpages->withQueryString()->links() }}</div>
            </div>
        @endif
    </div>
</div>

{{-- Delete Modal --}}
<div class="confirm-overlay" id="deleteOverlay">
    <div class="confirm-modal">
        <div class="confirm-head">
            <div class="confirm-icon"><i class="bi bi-trash"></i></div>
            <h5>Delete Landing Page?</h5>
        </div>
        <div class="confirm-body">
            <p>Are you sure you want to delete <strong id="deleteTitle">this page</strong>?<br><br>
            <span style="color:#dc2626;font-weight:600;"><i class="bi bi-exclamation-triangle-fill"></i> This action cannot be undone. All images will also be deleted.</span></p>
        </div>
        <div class="confirm-foot">
            <button class="btn-cancel-modal" onclick="closeDeleteModal()"><i class="bi bi-x"></i> Cancel</button>
            <form id="deleteForm" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-confirm-delete"><i class="bi bi-trash"></i> Yes, Delete</button>
            </form>
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
'use strict';
function confirmDelete(id, title, url) {
    document.getElementById('deleteTitle').textContent = title;
    document.getElementById('deleteForm').action = url;
    document.getElementById('deleteOverlay').classList.add('show');
}
function closeDeleteModal() { document.getElementById('deleteOverlay').classList.remove('show'); }
document.getElementById('deleteOverlay').addEventListener('click', function(e){ if(e.target===this) closeDeleteModal(); });
document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeDeleteModal(); });

function showToast(msg, type, ms) {
    type=type||'success'; ms=ms||3200;
    var c=document.getElementById('toastContainer');
    var t=document.createElement('div');
    t.className='pos-toast t-'+type;
    t.innerHTML='<i class="bi bi-'+(type==='success'?'check-circle-fill':'x-circle-fill')+'"></i><span>'+msg+'</span>';
    c.appendChild(t);
    setTimeout(function(){t.style.animation='tOut .3s ease forwards';t.addEventListener('animationend',function(){t.remove();},{once:true});},ms);
}
@if(session('success')) showToast('{{ addslashes(session("success")) }}','success'); @endif
@if(session('error'))   showToast('{{ addslashes(session("error")) }}','error');   @endif
</script>

@endsection
