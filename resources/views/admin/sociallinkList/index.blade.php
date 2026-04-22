@extends('admin.master')
@section('content')
<style>
    :root {
        --brand-primary: #e8174a;
        --brand-primary-light: rgba(232,23,74,0.08);
        --brand-primary-hover: #c9113e;
        --text-dark: #1a1d23;
        --text-muted: #6b7280;
        --border-color: #f0f0f5;
        --surface-bg: #f8f9fc;
        --shadow-sm: 0 1px 4px rgba(0,0,0,0.06), 0 2px 12px rgba(0,0,0,0.04);
        --radius-lg: 14px;
        --radius-md: 10px;
        --radius-sm: 7px;
        --transition: all 0.18s ease;
    }

    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.6rem; }
    .page-title { font-size:1.35rem; font-weight:700; color:var(--text-dark); letter-spacing:-0.3px; margin:0; }
    .page-subtitle { font-size:12.5px; color:var(--text-muted); margin:2px 0 0 0; }

    .alert-custom { border-radius:var(--radius-md); border:none; font-size:13.5px; padding:12px 16px; display:flex; align-items:center; gap:9px; margin-bottom:1.2rem; }
    .alert-success-custom { background:#f0fdf4; color:#15803d; border-left:3.5px solid #22c55e; }
    .alert-danger-custom  { background:#fff1f3; color:#be123c; border-left:3.5px solid var(--brand-primary); }
    .alert-custom .btn-close { margin-left:auto; opacity:.5; font-size:11px; }

    .data-card { background:#fff; border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); border:1px solid var(--border-color); overflow:hidden; }
    .card-head { display:flex; align-items:center; justify-content:space-between; padding:14px 22px; border-bottom:1px solid var(--border-color); background:var(--surface-bg); }
    .card-head-label { font-size:12.5px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.6px; }
    .badge-count { background:var(--brand-primary-light); color:var(--brand-primary); border-radius:20px; padding:2px 10px; font-size:12px; font-weight:700; }

    .social-table { width:100%; border-collapse:collapse; }
    .social-table thead tr { background:var(--surface-bg); }
    .social-table th { padding:10px 18px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.55px; color:var(--text-muted); border-bottom:1px solid var(--border-color); white-space:nowrap; }
    .social-table td { padding:12px 18px; font-size:13.5px; color:var(--text-dark); border-bottom:1px solid var(--border-color); vertical-align:middle; }
    .social-table tbody tr:last-child td { border-bottom:none; }
    .social-table tbody tr:hover { background:#fafbff; }

    .sl-badge { display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; background:var(--surface-bg); border-radius:50%; font-size:12px; font-weight:600; color:var(--text-muted); border:1px solid var(--border-color); }

    .platform-name { font-weight:600; color:var(--text-dark); font-size:13.5px; }
    .link-text { font-size:12.5px; color:var(--text-muted); max-width:300px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:block; }

    .form-check-input { width:40px !important; height:22px !important; cursor:pointer; border-radius:11px !important; }
    .form-check-input:checked { background-color:var(--brand-primary) !important; border-color:var(--brand-primary) !important; }
    .form-check-input:focus { box-shadow:0 0 0 .18rem rgba(232,23,74,.2) !important; }
    .form-check-input:not(:checked) { background-color:#d1d5db !important; border-color:#d1d5db !important; }

    .btn-icon-edit { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:var(--radius-sm); border:1.5px solid rgba(232,23,74,.3); background:transparent; cursor:pointer; transition:var(--transition); font-size:13px; color:var(--brand-primary); }
    .btn-icon-edit:hover { background:var(--brand-primary); border-color:var(--brand-primary); color:#fff; transform:translateY(-1px); box-shadow:0 3px 10px rgba(232,23,74,.25); }

    /* Modal */
    .modal-content { border-radius:var(--radius-lg) !important; border:none !important; box-shadow:0 20px 60px rgba(0,0,0,0.15) !important; }
    .modal-header { border-radius:var(--radius-lg) var(--radius-lg) 0 0 !important; padding:18px 22px !important; border-bottom:1px solid var(--border-color) !important; background:var(--surface-bg); }
    .modal-title-text { font-size:15px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:9px; }
    .modal-title-icon { width:30px; height:30px; background:var(--brand-primary-light); border-radius:var(--radius-sm); display:inline-flex; align-items:center; justify-content:center; color:var(--brand-primary); font-size:14px; }
    .modal-body { padding:24px 22px !important; }
    .modal-footer { padding:14px 22px !important; border-top:1px solid var(--border-color) !important; background:var(--surface-bg); border-radius:0 0 var(--radius-lg) var(--radius-lg) !important; }
    .form-label-custom { font-size:13px; font-weight:600; color:var(--text-dark); margin-bottom:7px; display:block; }
    .form-control-custom { border-radius:var(--radius-sm) !important; border:1.5px solid var(--border-color) !important; font-size:13.5px !important; padding:9px 13px !important; color:var(--text-dark) !important; background:#fff !important; transition:border-color .15s, box-shadow .15s !important; width:100%; outline:none; }
    .form-control-custom:focus { border-color:var(--brand-primary) !important; box-shadow:0 0 0 3px rgba(232,23,74,.1) !important; }
    .form-control-custom::placeholder { color:#b0b7c3 !important; }
    .btn-modal-close { background:transparent; border:1.5px solid var(--border-color); color:var(--text-muted); border-radius:var(--radius-sm); padding:8px 20px; font-size:13px; font-weight:500; cursor:pointer; transition:var(--transition); }
    .btn-modal-close:hover { background:var(--surface-bg); color:var(--text-dark); }
    .btn-modal-submit { background:var(--brand-primary); border:none; color:#fff; border-radius:var(--radius-sm); padding:8px 24px; font-size:13px; font-weight:600; cursor:pointer; transition:var(--transition); box-shadow:0 2px 8px rgba(232,23,74,.22); }
    .btn-modal-submit:hover { background:var(--brand-primary-hover); box-shadow:0 3px 14px rgba(232,23,74,.32); transform:translateY(-1px); }
</style>

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h4 class="page-title">Social Links</h4>
        <p class="page-subtitle">Manage all social media links</p>
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="alert-custom alert-success-custom alert-dismissible" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert-custom alert-danger-custom alert-dismissible" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>{{ $errors->first() }}</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Data Card --}}
<div class="data-card">
    <div class="card-head">
        <span class="card-head-label">Social Link List</span>
        <span class="badge-count">{{ $socialLinks->count() }} total</span>
    </div>

    <div class="table-responsive">
        <table class="social-table">
            <thead>
                <tr>
                    <th style="width:64px;">#</th>
                    <th style="width:220px;">Platform</th>
                    <th>Link</th>
                    <th style="width:130px; text-align:center;">Status</th>
                    <th style="width:90px; text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($socialLinks as $i => $social)
                <tr>
                    {{-- SL --}}
                    <td><span class="sl-badge">{{ $i + 1 }}</span></td>

                    {{-- Platform --}}
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            @switch($social->platform)
                                @case('facebook')
                                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none"><circle cx="14" cy="14" r="14" fill="#1877F2"/><path d="M17.5 8.5H16c-.8 0-1 .3-1 1.1V11h2.5l-.3 2.5H15V21h-3v-7.5h-1.5V11H12V9.4C12 7.3 13.2 6 15.5 6c1 0 2 .1 2 .1V8.5z" fill="white"/></svg>
                                @break
                                @case('linkedin')
                                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none"><circle cx="14" cy="14" r="14" fill="#0A66C2"/><path d="M9.5 11h-2v8h2v-8zm-1-3a1.1 1.1 0 100 2.2A1.1 1.1 0 008.5 8zM20 14.2c0-2-.9-3.2-2.6-3.2-1 0-1.7.5-2 1.2V11h-2v8h2v-4.3c0-1.1.5-1.9 1.5-1.9s1.1.7 1.1 1.8V19h2v-4.8z" fill="white"/></svg>
                                @break
                                @case('instagram')
                                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none"><defs><radialGradient id="ig{{ $i }}" cx="30%" cy="107%" r="150%"><stop offset="0%" stop-color="#fdf497"/><stop offset="5%" stop-color="#fdf497"/><stop offset="45%" stop-color="#fd5949"/><stop offset="60%" stop-color="#d6249f"/><stop offset="90%" stop-color="#285AEB"/></radialGradient></defs><circle cx="14" cy="14" r="14" fill="url(#ig{{ $i }})"/><rect x="8" y="8" width="12" height="12" rx="3.5" stroke="white" stroke-width="1.5" fill="none"/><circle cx="14" cy="14" r="3" stroke="white" stroke-width="1.5" fill="none"/><circle cx="18" cy="10" r="1" fill="white"/></svg>
                                @break
                                @case('youtube')
                                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none"><circle cx="14" cy="14" r="14" fill="#FF0000"/><path d="M21 10.5s-.2-1.3-.8-1.9c-.7-.8-1.5-.8-1.9-.8C16.2 7.7 14 7.7 14 7.7s-2.2 0-4.3.1c-.4 0-1.2.1-1.9.8C7.2 9.2 7 10.5 7 10.5S6.8 12 6.8 13.5v1.4c0 1.4.2 2.9.2 2.9s.2 1.3.8 1.9c.7.8 1.7.7 2.2.8 1.5.2 6 .2 6 .2s2.2 0 4.3-.2c.4 0 1.2-.1 1.9-.8.6-.6.8-1.9.8-1.9s.2-1.4.2-2.9v-1.4c0-1.5-.2-3-.2-3zm-8.5 5.7V11.7l5 2.3-5 2.2z" fill="white"/></svg>
                                @break
                                @case('whatsapp')
                                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none"><circle cx="14" cy="14" r="14" fill="#25D366"/><path d="M19.5 8.4A7.8 7.8 0 006.2 18l-1.2 4.4 4.5-1.2A7.8 7.8 0 0019.5 8.4zm-5.5 12a6.5 6.5 0 01-3.3-.9l-.2-.1-2.7.7.7-2.6-.2-.3a6.5 6.5 0 1111.6-4 6.5 6.5 0 01-5.9 6.2zm3.6-4.9c-.2-.1-1.2-.6-1.4-.6-.2-.1-.3-.1-.4.1l-.6.7c-.1.1-.2.1-.4 0-.2-.1-.8-.3-1.5-1a5.5 5.5 0 01-1-1.4c-.1-.2 0-.3.1-.4l.3-.4c.1-.1.1-.2.2-.3v-.3c-.1-.2-.4-1-.6-1.4-.1-.4-.3-.3-.4-.3h-.4c-.1 0-.4.1-.6.3-.2.2-.8.8-.8 1.9s.8 2.2.9 2.3c.1.2 1.6 2.4 3.8 3.4.5.2.9.4 1.3.5.5.2 1 .1 1.4.1.4-.1 1.2-.5 1.4-1s.2-.9.1-1l-.4-.2z" fill="white"/></svg>
                                @break
                                @case('twitter')
                                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none"><circle cx="14" cy="14" r="14" fill="#000"/><path d="M8 8h3.5l3 4.2L18 8h2l-4.5 5.5L21 20h-3.5l-3.2-4.5L10 20H8l4.8-5.8L8 8z" fill="white"/></svg>
                                @break
                                @case('telegram')
                                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none"><circle cx="14" cy="14" r="14" fill="#26A5E4"/><path d="M7 14l3 1 1.5 4.5 2-2.5 4 3 2.5-10L7 14zm4.5 3.7l-.4-3.3 7.8-5-7.4 8.3z" fill="white"/></svg>
                                @break
                                @default
                                    <span style="width:28px;height:28px;background:#e5e7eb;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#6b7280;">
                                        {{ strtoupper(substr($social->name, 0, 1)) }}
                                    </span>
                            @endswitch
                            <span class="platform-name">{{ $social->name }}</span>
                        </div>
                    </td>

                    {{-- Link --}}
                    <td>
                        @if($social->link)
                            <span class="link-text" title="{{ $social->link }}">{{ $social->link }}</span>
                        @else
                            <span style="color:#d1d5db; font-size:13px;">—</span>
                        @endif
                    </td>

                    {{-- Status Toggle --}}
                    <td style="text-align:center;">
                        <form action="{{ route('admin.sociallinkList.toggle', $social->id) }}" method="POST" class="d-inline">
                            @csrf
                            <div class="form-check form-switch d-flex justify-content-center m-0">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       {{ $social->is_active ? 'checked' : '' }}
                                       onchange="this.closest('form').submit()">
                            </div>
                        </form>
                    </td>

                    {{-- Edit --}}
                    <td style="text-align:right;">
                        <button type="button"
                                class="btn-icon-edit"
                                title="Edit"
                                onclick="openEditModal({{ $social->id }}, '{{ addslashes($social->name) }}', '{{ addslashes($social->link ?? '') }}')">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div style="padding:56px 20px; text-align:center;">
                            <i class="bi bi-link-45deg" style="font-size:40px; color:#d1d5db; display:block; margin-bottom:12px;"></i>
                            <p style="font-size:14px; font-weight:600; color:#6b7280; margin:0 0 4px;">No Social Links Found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title-text">
                    <span class="modal-title-icon"><i class="bi bi-pencil-square"></i></span>
                    Update Social Link
                </span>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-custom">Platform Name <span style="color:var(--brand-primary)">*</span></label>
                        <input type="text" name="name" id="modal-name"
                               class="form-control-custom" required
                               placeholder="e.g. Facebook">
                    </div>
                    <div class="mb-1">
                        <label class="form-label-custom">Link URL</label>
                        <input type="text" name="link" id="modal-link"
                               class="form-control-custom"
                               placeholder="https://...">
                    </div>
                </div>

                <div class="modal-footer gap-2">
                    <button type="button" class="btn-modal-close" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-submit">
                        <i class="bi bi-check-lg me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditModal(id, name, link) {
    var baseUrl = "{{ route('admin.sociallinkList.update', '__ID__') }}";
    document.getElementById('editForm').action = baseUrl.replace('__ID__', id);
    document.getElementById('modal-name').value = name;
    document.getElementById('modal-link').value = (link === 'null' || !link) ? '' : link;
    var modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
    document.getElementById('editModal').addEventListener('shown.bs.modal', function handler() {
        document.getElementById('modal-name').focus();
        this.removeEventListener('shown.bs.modal', handler);
    });
}
</script>

@endsection
