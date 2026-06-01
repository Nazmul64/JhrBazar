@extends('admin.master')

@section('title', 'Landing Pages')

@section('content')

@php
    $settings = \App\Models\GenaralSetting::first();
    $cur = $settings->default_currency ?? '৳';
@endphp

<style>
:root {
    --lp-accent: #1e3a8a;
    --lp-accent-lt: #2563eb;
    --lp-green: #22c55e;
    --lp-red: #ef4444;
    --lp-text: #1a1f36;
    --lp-muted: #64748b;
    --lp-border: #e2e8f0;
    --lp-bg: #f1f5f9;
    --lp-white: #ffffff;
    --lp-radius: 12px;
    --lp-shadow: 0 1px 6px rgba(0,0,0,0.08);
}
*,*::before,*::after { box-sizing: border-box; }

.lp-wrap { padding: 28px 28px 60px; background: var(--lp-bg); min-height: 100vh; font-family: 'DM Sans', 'Segoe UI', system-ui, sans-serif; }

/* ── Header ── */
.lp-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; gap: 12px; flex-wrap: wrap; }
.lp-header-left h1 { font-size: 22px; font-weight: 800; color: var(--lp-text); margin: 0; }
.lp-breadcrumb { font-size: 12px; color: var(--lp-muted); margin: 4px 0 0; }
.lp-breadcrumb a { color: var(--lp-muted); text-decoration: none; }
.lp-breadcrumb a:hover { color: var(--lp-accent); }
.lp-breadcrumb span { margin: 0 5px; }

.lp-header-right { display: flex; align-items: center; gap: 10px; }

.btn-theme-lib {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 18px; border-radius: 8px;
    background: var(--lp-white); border: 1.5px solid var(--lp-border);
    font-size: 13px; font-weight: 600; color: var(--lp-text);
    text-decoration: none; cursor: pointer; transition: all .15s;
}
.btn-theme-lib:hover { border-color: var(--lp-accent-lt); color: var(--lp-accent-lt); text-decoration: none; }
.btn-theme-lib .grid-icon { display: inline-grid; grid-template-columns: 1fr 1fr; gap: 2px; width: 14px; }
.btn-theme-lib .grid-icon span { width: 5px; height: 5px; background: currentColor; border-radius: 1px; }

.btn-create-new {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 20px; border-radius: 8px;
    background: var(--lp-accent); border: none;
    font-size: 13px; font-weight: 700; color: #fff;
    text-decoration: none; cursor: pointer; transition: opacity .15s;
    white-space: nowrap;
}
.btn-create-new:hover { opacity: .88; color: #fff; text-decoration: none; }

/* ── Alert ── */
.lp-alert {
    background: #f0fdf4; border: 1px solid #86efac;
    border-radius: 8px; padding: 12px 20px;
    font-size: 13.5px; color: #15803d; font-weight: 600;
    margin-bottom: 20px; display: flex; align-items: center;
    justify-content: space-between;
}
.lp-alert-close { background: none; border: none; cursor: pointer; color: #15803d; font-size: 18px; padding: 0; line-height: 1; }

/* ── Empty State ── */
.lp-empty {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    min-height: 55vh; text-align: center;
}
.lp-empty-icon {
    width: 90px; height: 90px; border-radius: 20px;
    background: #e0e7ff; display: flex; align-items: center; justify-content: center;
    margin-bottom: 24px;
}
.lp-empty-icon svg { width: 48px; height: 48px; color: #4f46e5; }
.lp-empty h3 { font-size: 20px; font-weight: 800; color: var(--lp-text); margin: 0 0 8px; }
.lp-empty p { font-size: 14px; color: var(--lp-muted); margin: 0 0 24px; }
.btn-get-started {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 32px; border-radius: 8px;
    background: #4361ee; color: #fff; font-size: 14px; font-weight: 700;
    text-decoration: none; border: none; cursor: pointer; transition: opacity .15s;
}
.btn-get-started:hover { opacity: .88; color: #fff; text-decoration: none; }

/* ── Cards Grid ── */
.lp-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.lp-card {
    background: var(--lp-white);
    border-radius: var(--lp-radius);
    box-shadow: var(--lp-shadow);
    overflow: hidden;
    border: 1px solid var(--lp-border);
    transition: box-shadow .2s, transform .2s;
    position: relative;
    cursor: pointer;
}
.lp-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,0.12); transform: translateY(-2px); }

/* Thumbnail */
.lp-card-thumb {
    height: 150px; background: #f8fafc;
    display: flex; align-items: center; justify-content: center;
    border-bottom: 1px solid var(--lp-border); position: relative; overflow: hidden;
}
.lp-card-thumb img { width: 100%; height: 100%; object-fit: cover; }
.lp-card-thumb-ph { font-size: 48px; color: #cbd5e1; }
.lp-card-thumb-ph svg { width: 52px; height: 52px; color: #94a3b8; }

/* Status badge on card */
.lp-card-status {
    position: absolute; top: 10px; right: 10px;
    padding: 3px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 700;
}
.lp-card-status.active { background: #d1fae5; color: #065f46; }
.lp-card-status.inactive { background: #fee2e2; color: #991b1b; }

/* Card body */
.lp-card-body { padding: 14px 16px 10px; }
.lp-card-product { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--lp-muted); margin-bottom: 5px; }
.lp-card-product svg { width: 14px; height: 14px; }
.lp-card-slug { display: flex; align-items: center; gap: 5px; font-size: 12px; color: var(--lp-muted); margin-bottom: 8px; }
.lp-card-slug svg { width: 13px; height: 13px; }
.lp-card-title { font-size: 14px; font-weight: 700; color: var(--lp-text); margin: 0 0 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* Card actions */
.lp-card-actions {
    display: flex; align-items: center; gap: 6px;
    padding: 10px 14px 12px;
    border-top: 1px solid #f1f5f9;
}
.lp-card-btn {
    width: 32px; height: 32px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    border: none; cursor: pointer; font-size: 14px;
    text-decoration: none; transition: opacity .15s; flex-shrink: 0;
}
.lp-card-btn:hover { opacity: .8; }
.lp-card-btn.view   { background: #e0e7ff; color: #4f46e5; }
.lp-card-btn.qr     { background: #1e293b; color: #fff; }
.lp-card-btn.edit   { background: #e0e7ff; color: #2563eb; }
.lp-card-btn.builder { background: #2563eb; color: #fff; }
.lp-card-btn.preview { background: #06b6d4; color: #fff; }
.lp-card-btn.delete { background: #fee2e2; color: #dc2626; }

/* Status toggle in actions */
.lp-card-status-toggle form { display: inline; }
.lp-card-status-toggle button {
    background: none; border: none; cursor: pointer; padding: 0;
    width: 32px; height: 32px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 14px; transition: opacity .15s;
}
.lp-card-status-toggle button:hover { opacity: .8; }

/* Pagination */
.lp-pagination { margin-top: 28px; display: flex; justify-content: center; }
.lp-pagination .pagination { gap: 4px; display: flex; list-style: none; padding: 0; margin: 0; }
.lp-pagination .page-item .page-link { border-radius: 6px !important; border: 1px solid var(--lp-border); color: var(--lp-text); font-size: 13px; padding: 6px 12px; }
.lp-pagination .page-item.active .page-link { background: var(--lp-accent); border-color: var(--lp-accent); color: #fff; }

/* QR Modal */
.qr-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; align-items: center; justify-content: center; }
.qr-modal-overlay.show { display: flex; }
.qr-modal { background: #fff; border-radius: 16px; padding: 32px; text-align: center; max-width: 320px; width: 90%; }
.qr-modal h5 { font-size: 16px; font-weight: 700; margin-bottom: 16px; }
.qr-modal img { width: 200px; height: 200px; }
.qr-modal p { font-size: 12px; color: var(--lp-muted); margin-top: 12px; word-break: break-all; }
.qr-close-btn { margin-top: 16px; padding: 8px 24px; background: var(--lp-accent); color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
</style>

<div class="lp-wrap">

    {{-- ── Header ── --}}
    <div class="lp-header">
        <div class="lp-header-left">
            <h1>Landing Pages</h1>
            <p class="lp-breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <span>›</span> Marketing
                <span>›</span> Landing Pages
            </p>
        </div>
        <div class="lp-header-right">
            {{-- Theme Library --}}
            <button class="btn-theme-lib" onclick="alert('Theme Library coming soon!')">
                <span class="grid-icon">
                    <span></span><span></span><span></span><span></span>
                </span>
                Theme Library ({{ $landingpages->total() }})
            </button>
            {{-- Create New --}}
            <a href="{{ route('admin.landingpages.create') }}" class="btn-create-new">
                + Create New Page
            </a>
        </div>
    </div>

    {{-- ── Success Alert ── --}}
    @if(session('success'))
    <div class="lp-alert" id="lpAlert">
        <span>{{ session('success') }}</span>
        <button class="lp-alert-close" onclick="document.getElementById('lpAlert').style.display='none'">✕</button>
    </div>
    @endif

    {{-- ── Empty State ── --}}
    @if($landingpages->isEmpty())
    <div class="lp-empty">
        <div class="lp-empty-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                <path d="M8 21h8M12 17v4"/>
                <path d="M2 7h20M7 3v4M17 3v4"/>
            </svg>
        </div>
        <h3>No Landing Pages Found</h3>
        <p>Create your first high-converting landing page now!</p>
        <a href="{{ route('admin.landingpages.create') }}" class="btn-get-started">
            Get Started
        </a>
    </div>

    {{-- ── Cards Grid ── --}}
    @else
    <div class="lp-cards-grid">
        @foreach($landingpages as $lp)
        <div class="lp-card">
            {{-- Thumbnail --}}
            <div class="lp-card-thumb">
                @if($lp->feature_image)
                    <img src="{{ asset($lp->feature_image) }}" alt="{{ $lp->title }}">
                @else
                    <div class="lp-card-thumb-ph">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                            <path d="M21 15l-5-5L5 21"/>
                        </svg>
                    </div>
                @endif
                {{-- Status Badge --}}
                <span class="lp-card-status {{ $lp->status ? 'active' : 'inactive' }}">
                    {{ $lp->status ? 'Active' : 'Inactive' }}
                </span>
            </div>

            {{-- Body --}}
            <div class="lp-card-body">
                <div class="lp-card-product">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                    </svg>
                    {{ $lp->product?->name ?? 'No Product' }}
                </div>
                <div class="lp-card-slug">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                    </svg>
                    /{{ $lp->slug }}
                </div>
                <div class="lp-card-title">{{ $lp->title }}</div>
            </div>

            {{-- Actions --}}
            <div class="lp-card-actions">
                {{-- Preview (eye) --}}
                <a href="/l/{{ $lp->slug }}" target="_blank" class="lp-card-btn view" title="Preview">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><path d="M2 12S5 4 12 4s10 8 10 8-3 8-10 8S2 12 2 12z"/></svg>
                </a>
                {{-- QR Code --}}
                <button class="lp-card-btn qr" title="QR Code" onclick="showQR('{{ url('/l/' . $lp->slug) }}', '{{ addslashes($lp->title) }}')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="3" y="14" width="7" height="7"/>
                        <path d="M14 14h1v1h-1zM17 14h1v1h-1zM14 17h1v1h-1zM17 17h3v3h-3z"/>
                    </svg>
                </button>
                {{-- Builder --}}
                <a href="{{ route('admin.landingpages.builder', $lp->id) }}" class="lp-card-btn builder" title="Page Builder">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="2" width="9" height="9" rx="1"/><rect x="13" y="2" width="9" height="9" rx="1"/><rect x="2" y="13" width="9" height="9" rx="1"/><rect x="13" y="13" width="9" height="9" rx="1"/></svg>
                </a>
                {{-- Edit --}}
                <a href="{{ route('admin.landingpages.edit', $lp->id) }}" class="lp-card-btn edit" title="Edit Settings">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </a>
                {{-- Status Toggle --}}
                <div class="lp-card-status-toggle" style="margin-left:auto;">
                    <form method="POST" action="{{ route('admin.landingpages.toggle-status', $lp->id) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="lp-card-btn {{ $lp->status ? 'preview' : '' }}"
                            style="{{ $lp->status ? 'background:#06b6d4;color:#fff;' : 'background:#94a3b8;color:#fff;' }}"
                            title="{{ $lp->status ? 'Deactivate' : 'Activate' }}"
                        >
                            @if($lp->status)
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18.36 6.64A9 9 0 1 1 5.64 17.36"/><path d="M12 2v4"/></svg>
                            @else
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                            @endif
                        </button>
                    </form>
                </div>
                {{-- Delete --}}
                <button class="lp-card-btn delete" title="Delete"
                    onclick="confirmDeleteLP({{ $lp->id }}, '{{ addslashes($lp->title) }}', '{{ route('admin.landingpages.destroy', $lp->id) }}')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                </button>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($landingpages->hasPages())
    <div class="lp-pagination">
        {{ $landingpages->links('pagination::bootstrap-5') }}
    </div>
    @endif
    @endif

</div>

{{-- QR Modal --}}
<div class="qr-modal-overlay" id="qrModal">
    <div class="qr-modal">
        <h5 id="qrTitle">QR Code</h5>
        <img id="qrImage" src="" alt="QR Code">
        <p id="qrUrl"></p>
        <button class="qr-close-btn" onclick="closeQR()">Close</button>
    </div>
</div>

{{-- Delete Form --}}
<form id="deleteForm" method="POST" style="display:none;">
    @csrf @method('DELETE')
</form>

<script>
function showQR(url, title) {
    const encoded = encodeURIComponent(url);
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encoded}`;
    document.getElementById('qrTitle').textContent = title;
    document.getElementById('qrImage').src = qrUrl;
    document.getElementById('qrUrl').textContent = url;
    document.getElementById('qrModal').classList.add('show');
}
function closeQR() {
    document.getElementById('qrModal').classList.remove('show');
}
document.getElementById('qrModal').addEventListener('click', function(e) {
    if (e.target === this) closeQR();
});

function confirmDeleteLP(id, title, action) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Delete Landing Page?',
            text: `"${title}" will be permanently deleted.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
        }).then(result => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = action;
                form.submit();
            }
        });
    } else {
        if (confirm(`Delete "${title}"?`)) {
            const form = document.getElementById('deleteForm');
            form.action = action;
            form.submit();
        }
    }
}
</script>

@endsection
