@extends('admin.master')
@section('content')
<style>
    :root {
        --brand: #e8174a;
        --brand-light: rgba(232,23,74,.08);
        --brand-hover: #c9113e;
        --dark: #1a1d23;
        --muted: #6b7280;
        --border: #e5e7eb;
        --surface: #f8f9fc;
        --shadow: 0 1px 4px rgba(0,0,0,.06), 0 2px 12px rgba(0,0,0,.04);
        --r-lg: 14px; --r-md: 10px; --r-sm: 7px;
        --ease: all .18s ease;
    }
    .ph {display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;}
    .ph-title {font-size:1.4rem;font-weight:700;color:var(--dark);margin:0;}
    .btn-add {display:inline-flex;align-items:center;gap:7px;background:var(--brand);color:#fff;border:none;border-radius:var(--r-md);padding:10px 22px;font-size:13.5px;font-weight:600;cursor:pointer;box-shadow:0 2px 10px rgba(232,23,74,.3);text-decoration:none;transition:var(--ease);} 
    .btn-add:hover {background:var(--brand-hover);}
    .alert-ok {background:#f0fdf4;color:#15803d;border-left:3.5px solid #22c55e;border-radius:var(--r-md);padding:12px 16px;font-size:13.5px;margin-bottom:1.2rem;display:flex;align-items:center;gap:9px;}
    .data-card {background:#fff;border-radius:var(--r-lg);box-shadow:var(--shadow);border:1px solid var(--border);overflow:hidden;}
    .fs-table {width:100%;border-collapse:collapse;}
    .fs-table th {padding:13px 18px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--muted);background:var(--surface);border-bottom:1px solid var(--border);white-space:nowrap;}
    .fs-table td {padding:14px 18px;font-size:13px;color:var(--dark);border-bottom:1px solid var(--border);vertical-align:middle;}
    .fs-table tbody tr:hover {background:#fafbff;}
    .fs-thumb {width:72px;height:48px;object-fit:cover;border-radius:var(--r-sm);border:1px solid var(--border);display:block;}
    .fs-thumb-ph {width:72px;height:48px;background:var(--surface);border-radius:var(--r-sm);display:flex;align-items:center;justify-content:center;color:#d1d5db;font-size:20px;border:1px solid var(--border);}
    .action-wrap {display:flex;align-items:center;gap:7px;justify-content:flex-end;}
    .btn-act {width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--r-sm);border:1.5px solid;background:transparent;cursor:pointer;font-size:14px;text-decoration:none;transition:var(--ease);}
    .btn-act-edit {border-color:rgba(20,184,166,.3);color:#0d9488;}
    .btn-act-edit:hover {background:#0d9488;color:#fff;}
    .btn-act-del {border-color:rgba(239,68,68,.3);color:#ef4444;}
    .btn-act-del:hover {background:#ef4444;color:#fff;}
    .empty-row td {text-align:center;padding:50px 20px;color:var(--muted);font-size:13px;}
</style>
<div class="ph">
    <h4 class="ph-title">Seller Banners</h4>
    <a href="{{ route('seller.banners.create') }}" class="btn-add"><i class="bi bi-plus-circle"></i> Add Banner</a>
</div>
@if(session('success'))
    <div class="alert-ok"><i class="bi bi-check-circle-fill"></i> <span>{{ session('success') }}</span></div>
@endif
<div class="data-card">
    <div class="table-responsive">
        <table class="fs-table">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>Thumbnail</th>
                    <th>Title</th>
                    <th>Link</th>
                    <th>Period</th>
                    <th>Status</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($banners as $i => $banner)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        @if($banner->image)
                            <img src="{{ asset($banner->image) }}" class="fs-thumb" alt="{{ $banner->title }}" onerror="this.style.display='none'">
                        @else
                            <div class="fs-thumb-ph"><i class="bi bi-image"></i></div>
                        @endif
                    </td>
                    <td style="font-weight:600;min-width:140px;">{{ $banner->title }}</td>
                    <td><a href="{{ $banner->link }}" target="_blank" style="color:var(--brand);">{{ $banner->link ?? '—' }}</a></td>
                    <td>{{ $banner->start_date ? $banner->start_date->format('Y-m-d') : '—' }} – {{ $banner->end_date ? $banner->end_date->format('Y-m-d') : '—' }}</td>
                    <td>
                        @if($banner->is_active)
                            <span style="color:#16a34a;">Active</span>
                        @else
                            <span style="color:#6b7280;">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-wrap">
                            <a href="{{ route('seller.banners.edit', $banner->id) }}" class="btn-act btn-act-edit" title="Edit"><i class="bi bi-pencil-square"></i></a>
                            <button type="button" class="btn-act btn-act-del" title="Delete" onclick="openDel({{ $banner->id }}, '{{ addslashes($banner->title) }}')"><i class="bi bi-trash3"></i></button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row"><td colspan="7"><i class="bi bi-image" style="font-size:36px;color:#d1d5db;display:block;margin-bottom:8px;"></i>No banners found. Click "Add Banner" to create one.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{-- Delete Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content">
            <div class="modal-body" style="padding:32px 24px;text-align:center;">
                <div class="del-icon"><i class="bi bi-trash3-fill"></i></div>
                <p style="font-size:15px;font-weight:700;color:#1a1d23;margin:0 0 8px;">Delete Banner?</p>
                <p style="font-size:13.5px;color:var(--muted);margin:0;">Are you sure you want to delete <strong id="del-name"></strong>?<br>This action cannot be undone.</p>
            </div>
            <div class="modal-footer gap-2" style="justify-content:center;border-top:1px solid var(--border);padding:14px 22px;">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                <form id="delForm" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del-confirm"><i class="bi bi-trash3 me-1"></i> Yes, Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function openDel(id, name) {
    var base = "{{ route('seller.banners.destroy', '__ID__') }}";
    document.getElementById('delForm').action = base.replace('__ID__', id);
    document.getElementById('del-name').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
