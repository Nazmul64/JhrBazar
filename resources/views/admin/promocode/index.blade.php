@extends('admin.master')

@section('content')

<style>
    .promo-page-wrapper {
        padding: 24px;
        background: #f3f4f6;
        min-height: 100vh;
    }

    .promo-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .promo-page-title {
        font-size: 20px;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }

    .btn-add-promo {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ef4444;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.2s;
    }

    .btn-add-promo:hover {
        background: #dc2626;
        color: #fff;
        text-decoration: none;
    }

    .btn-add-promo svg { width: 16px; height: 16px; }

    .promo-table-card {
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }

    .promo-table { width: 100%; border-collapse: collapse; }

    .promo-table thead tr {
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .promo-table thead th {
        padding: 14px 20px;
        text-align: left;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        white-space: nowrap;
    }

    .promo-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.15s;
    }

    .promo-table tbody tr:last-child { border-bottom: none; }
    .promo-table tbody tr:hover { background: #fafafa; }

    .promo-table tbody td {
        padding: 16px 20px;
        font-size: 14px;
        color: #374151;
        vertical-align: middle;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
    }

    .toggle-switch input { opacity: 0; width: 0; height: 0; }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #d1d5db;
        border-radius: 24px;
        transition: background 0.3s;
    }

    .toggle-slider::before {
        content: '';
        position: absolute;
        width: 18px;
        height: 18px;
        left: 3px;
        top: 3px;
        background: #fff;
        border-radius: 50%;
        transition: transform 0.3s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    .toggle-switch input:checked + .toggle-slider { background: #ef4444; }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(22px); }

    .action-btns { display: flex; align-items: center; gap: 10px; }

    .btn-edit-icon {
        background: none; border: none; cursor: pointer;
        color: #3b82f6; padding: 4px; display: flex;
        align-items: center; transition: color 0.2s; text-decoration: none;
    }

    .btn-edit-icon:hover { color: #2563eb; }

    .btn-delete-icon {
        background: none; border: none; cursor: pointer;
        color: #ef4444; padding: 4px; display: flex;
        align-items: center; transition: color 0.2s;
    }

    .btn-delete-icon:hover { color: #dc2626; }
    .btn-edit-icon svg, .btn-delete-icon svg { width: 18px; height: 18px; }

    .alert-success {
        background: #ecfdf5;
        border: 1px solid #6ee7b7;
        color: #065f46;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 14px;
    }

    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.4); z-index: 9999;
        align-items: center; justify-content: center;
    }

    .modal-overlay.active { display: flex; }

    .modal-box {
        background: #fff; border-radius: 12px;
        padding: 28px 32px; max-width: 420px;
        width: 90%; text-align: center;
        box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    }

    .modal-box h4 { font-size: 18px; font-weight: 600; color: #111827; margin-bottom: 8px; }
    .modal-box p { font-size: 14px; color: #6b7280; margin-bottom: 24px; }
    .modal-actions { display: flex; gap: 12px; justify-content: center; }

    .modal-btn-cancel {
        padding: 9px 22px; border: 1px solid #d1d5db;
        background: #fff; border-radius: 6px; font-size: 14px;
        cursor: pointer; color: #374151;
    }

    .modal-btn-cancel:hover { background: #f3f4f6; }

    .modal-btn-delete {
        padding: 9px 22px; background: #ef4444;
        border: none; border-radius: 6px; font-size: 14px;
        cursor: pointer; color: #fff;
    }

    .modal-btn-delete:hover { background: #dc2626; }
</style>

<div class="promo-page-wrapper">

    <div class="promo-page-header">
        <h2 class="promo-page-title">Promo Codes</h2>
        <a href="{{ route('admin.promocode.create') }}" class="btn-add-promo">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="16"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            Add Promo Code
        </a>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="promo-table-card">
        <table class="promo-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Discount</th>
                    <th>Min Amount</th>
                    <th>Started At</th>
                    <th>Expired At</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($promocodes as $promo)
                    <tr>
                        <td>{{ $promo->coupon_code }}</td>
                        <td>{{ $promo->formatted_discount }}</td>
                        <td>${{ number_format($promo->minimum_order_amount, 0) }}</td>
                        <td>{{ $promo->started_at }}</td>
                        <td>{{ $promo->expired_at }}</td>
                        <td>
                            <label class="toggle-switch">
                                <input
                                    type="checkbox"
                                    class="status-toggle"
                                    data-id="{{ $promo->id }}"
                                    {{ $promo->status ? 'checked' : '' }}
                                >
                                <span class="toggle-slider"></span>
                            </label>
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="{{ route('admin.promocode.edit', $promo->id) }}" class="btn-edit-icon" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                                <button class="btn-delete-icon" title="Delete" onclick="openDeleteModal({{ $promo->id }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        <path d="M10 11v6"/><path d="M14 11v6"/>
                                        <path d="M9 6V4h6v2"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:40px; color:#9ca3af;">
                            No promo codes found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <h4>Delete Promo Code?</h4>
        <p>Are you sure you want to delete this promo code? This action cannot be undone.</p>
        <div class="modal-actions">
            <button class="modal-btn-cancel" onclick="closeDeleteModal()">Cancel</button>
            <form id="deleteForm" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="modal-btn-delete">Delete</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(id) {
        document.getElementById('deleteForm').action = '/admin/promocode/' + id;
        document.getElementById('deleteModal').classList.add('active');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
    }

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    document.querySelectorAll('.status-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            var id = this.dataset.id;
            var checkbox = this;
            fetch('/admin/promocode/' + id + '/toggle', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            })
            .then(res => res.json())
            .then(data => { if (!data.success) checkbox.checked = !checkbox.checked; })
            .catch(() => { checkbox.checked = !checkbox.checked; });
        });
    });
</script>

@endsection
