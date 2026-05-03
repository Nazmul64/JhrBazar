@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- ── Important Information Banner ── --}}
    <div class="tax-info-banner mb-4">
        <div class="d-flex align-items-start gap-2">
            <span style="font-size:20px;">💥</span>
            <div>
                <div class="fw-semibold mb-1" style="font-size:15px; color:#1a1a2e;">Important Information</div>
                <div style="font-size:13.5px; color:#444; line-height:1.6;">
                    Just a quick note: <strong>VAT and Taxes are calculated based on your order.</strong>
                    If you have multiple VAT and Tax rates active, your total VAT and Tax amount will be
                    clearly displayed on the checkout page for your convenience.
                </div>
            </div>
        </div>
    </div>

    {{-- ── All Taxes Table Card ── --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0" style="font-size:20px; color:#1a1a2e;">All Taxes</h5>
                <button class="btn-add-tax" onclick="openAddModal()">
                    <i class="fas fa-plus me-1"></i> Add Tax
                </button>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="taxTable">
                    <thead>
                        <tr style="background:#f8f9fa;">
                            <th class="tax-th text-center" style="width:80px;">SL</th>
                            <th class="tax-th">Name</th>
                            <th class="tax-th">Percentage</th>
                            <th class="tax-th text-center">Status</th>
                            <th class="tax-th text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="taxTableBody">
                        @forelse($taxes as $index => $tax)
                        <tr class="tax-row" id="tax-row-{{ $tax->id }}">
                            <td class="tax-td text-center">{{ $index + 1 }}</td>
                            <td class="tax-td fw-medium">{{ $tax->name }}</td>
                            <td class="tax-td">{{ rtrim(rtrim(number_format($tax->percentage, 2), '0'), '.') }}%</td>
                            <td class="tax-td text-center">
                                <div class="form-check form-switch d-flex justify-content-center align-items-center m-0">
                                    <input class="form-check-input tax-toggle"
                                           type="checkbox"
                                           role="switch"
                                           id="toggle-{{ $tax->id }}"
                                           data-id="{{ $tax->id }}"
                                           {{ $tax->status ? 'checked' : '' }}
                                           style="width:44px; height:22px; cursor:pointer;">
                                </div>
                            </td>
                            <td class="tax-td text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="tax-action-btn tax-edit-btn"
                                            title="Edit"
                                            onclick="openEditModal({{ $tax->id }}, '{{ addslashes($tax->name) }}', '{{ $tax->percentage }}')">
                                        <i class="fas fa-external-link-alt"></i>
                                    </button>
                                    <button class="tax-action-btn tax-del-btn"
                                            title="Delete"
                                            onclick="deleteTax({{ $tax->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyRow">
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-receipt fa-2x mb-2 d-block"></i>
                                No taxes found. Click "Add Tax" to create one.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

{{-- ════ ADD MODAL ════ --}}
<div class="modal fade" id="addTaxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content border-0 rounded-3 shadow">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h6 class="modal-title fw-semibold" style="font-size:16px;">Add New Tax</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <div class="mb-3">
                    <label class="tax-label">Tax Name <span class="text-danger">*</span></label>
                    <input type="text" id="add_name" class="form-control tax-input" placeholder="Tax Name">
                    <div class="invalid-feedback" id="add_name_err"></div>
                </div>
                <div class="mb-2">
                    <label class="tax-label">Percentage(%) <span class="text-danger">*</span></label>
                    <input type="number" id="add_percentage" class="form-control tax-input"
                           placeholder="Percentage(%)" min="0" max="100" step="0.01">
                    <div class="invalid-feedback" id="add_percentage_err"></div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn-modal-submit" id="addSubmitBtn" onclick="submitAddTax()">Submit</button>
            </div>
        </div>
    </div>
</div>

{{-- ════ EDIT MODAL ════ --}}
<div class="modal fade" id="editTaxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
        <div class="modal-content border-0 rounded-3 shadow">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h6 class="modal-title fw-semibold" style="font-size:16px;">Update Tax</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <input type="hidden" id="edit_id">
                <div class="mb-3">
                    <label class="tax-label">Tax Name <span class="text-danger">*</span></label>
                    <input type="text" id="edit_name" class="form-control tax-input" placeholder="Tax Name">
                    <div class="invalid-feedback" id="edit_name_err"></div>
                </div>
                <div class="mb-2">
                    <label class="tax-label">Percentage(%) <span class="text-danger">*</span></label>
                    <input type="number" id="edit_percentage" class="form-control tax-input"
                           placeholder="Percentage(%)" min="0" max="100" step="0.01">
                    <div class="invalid-feedback" id="edit_percentage_err"></div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2">
                <button type="button" class="btn-modal-close" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn-modal-submit" id="editSubmitBtn" onclick="submitEditTax()">Update</button>
            </div>
        </div>
    </div>
</div>

{{-- ── Toast ── --}}
<div id="taxToast"
     style="position:fixed; top:20px; right:20px; z-index:9999;
            background:#333; color:#fff; padding:12px 20px;
            border-radius:8px; font-size:13px; display:none;
            min-width:200px; box-shadow:0 4px 15px rgba(0,0,0,.2);
            transition: opacity .4s;">
</div>

<style>
.tax-info-banner {
    background: #e8f4fd;
    border: 1px solid #bee3f8;
    border-radius: 10px;
    padding: 16px 20px;
}
.btn-add-tax {
    background: linear-gradient(135deg, #e91e63, #c2185b);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 9px 20px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 3px 10px rgba(233,30,99,.3);
}
.btn-add-tax:hover {
    background: linear-gradient(135deg, #c2185b, #ad1457);
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(233,30,99,.4);
}
.tax-th {
    font-size: 13px;
    font-weight: 600;
    color: #888;
    border-bottom: 1px solid #f0f0f0 !important;
    border-top: none !important;
    padding: 12px 16px;
}
.tax-td {
    font-size: 14px;
    color: #333;
    padding: 14px 16px;
}
.tax-row {
    border-bottom: 1px solid #f7f7f7 !important;
    transition: background .15s;
}
.tax-row:hover { background: #fafafa !important; }
.tax-row:last-child { border-bottom: none !important; }
.form-check-input:checked {
    background-color: #e91e63 !important;
    border-color: #e91e63 !important;
}
.tax-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 7px;
    border: none;
    cursor: pointer;
    font-size: 13px;
    transition: all .2s;
    background: transparent;
}
.tax-edit-btn { color: #3b82f6; }
.tax-edit-btn:hover { background: rgba(59,130,246,.1); color: #1d4ed8; }
.tax-del-btn { color: #e91e63; }
.tax-del-btn:hover { background: rgba(233,30,99,.1); color: #c2185b; }
.tax-label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #333;
    margin-bottom: 6px;
}
.tax-input {
    font-size: 14px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 14px;
    color: #333;
    transition: border-color .2s, box-shadow .2s;
}
.tax-input:focus {
    border-color: #e91e63;
    box-shadow: 0 0 0 3px rgba(233,30,99,.1);
    outline: none;
}
.btn-modal-close {
    background: #6b7280;
    color: #fff;
    border: none;
    border-radius: 7px;
    padding: 8px 22px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all .2s;
}
.btn-modal-close:hover { background: #4b5563; }
.btn-modal-submit {
    background: linear-gradient(135deg, #e91e63, #c2185b);
    color: #fff;
    border: none;
    border-radius: 7px;
    padding: 8px 22px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all .2s;
    box-shadow: 0 2px 8px rgba(233,30,99,.3);
}
.btn-modal-submit:hover {
    background: linear-gradient(135deg, #c2185b, #ad1457);
    transform: translateY(-1px);
}
.btn-modal-submit:disabled {
    opacity: .7;
    cursor: not-allowed;
    transform: none;
}
</style>

<script>
const CSRF        = '{{ csrf_token() }}';
// ✅ Laravel route() helper দিয়ে সঠিক base URL নাও
const STORE_URL   = '{{ route("admin.alltaxes.store") }}';
const BASE_URL    = '{{ url("admin/alltaxes") }}'; // /admin/alltaxes

// ── Open Modals ──────────────────────────────────────
function openAddModal() {
    document.getElementById('add_name').value       = '';
    document.getElementById('add_percentage').value = '';
    clearErrors(['add_name', 'add_percentage']);
    new bootstrap.Modal(document.getElementById('addTaxModal')).show();
}

function openEditModal(id, name, percentage) {
    document.getElementById('edit_id').value         = id;
    document.getElementById('edit_name').value       = name;
    document.getElementById('edit_percentage').value = percentage;
    clearErrors(['edit_name', 'edit_percentage']);
    new bootstrap.Modal(document.getElementById('editTaxModal')).show();
}

// ── Add Tax ──────────────────────────────────────────
async function submitAddTax() {
    const name       = document.getElementById('add_name').value.trim();
    const percentage = document.getElementById('add_percentage').value.trim();

    clearErrors(['add_name', 'add_percentage']);
    let valid = true;

    if (!name) { showFieldError('add_name', 'Tax name is required.'); valid = false; }
    if (percentage === '' || Number(percentage) < 0 || Number(percentage) > 100) {
        showFieldError('add_percentage', 'Valid percentage (0–100) is required.');
        valid = false;
    }
    if (!valid) return;

    const btn = document.getElementById('addSubmitBtn');
    btn.disabled    = true;
    btn.textContent = 'Saving...';

    try {
        const res = await fetch(STORE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept'      : 'application/json',
            },
            body: JSON.stringify({ name, percentage }),
        });

        const data = await res.json();

        if (res.ok && data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addTaxModal')).hide();
            showToast('Tax added successfully!', 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            // Laravel validation errors আসলে দেখাও
            if (data.errors) {
                if (data.errors.name)       showFieldError('add_name',       data.errors.name[0]);
                if (data.errors.percentage) showFieldError('add_percentage', data.errors.percentage[0]);
            } else {
                showToast(data.message || 'Something went wrong.', 'error');
            }
        }
    } catch (e) {
        showToast('Request failed. Check console.', 'error');
        console.error(e);
    } finally {
        btn.disabled    = false;
        btn.textContent = 'Submit';
    }
}

// ── Edit Tax ─────────────────────────────────────────
async function submitEditTax() {
    const id         = document.getElementById('edit_id').value;
    const name       = document.getElementById('edit_name').value.trim();
    const percentage = document.getElementById('edit_percentage').value.trim();

    clearErrors(['edit_name', 'edit_percentage']);
    let valid = true;

    if (!name) { showFieldError('edit_name', 'Tax name is required.'); valid = false; }
    if (percentage === '' || Number(percentage) < 0 || Number(percentage) > 100) {
        showFieldError('edit_percentage', 'Valid percentage (0–100) is required.');
        valid = false;
    }
    if (!valid) return;

    const btn = document.getElementById('editSubmitBtn');
    btn.disabled    = true;
    btn.textContent = 'Updating...';

    try {
        // ✅ সঠিক URL: BASE_URL = /admin/alltaxes
        const res = await fetch(`${BASE_URL}/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept'      : 'application/json',
            },
            body: JSON.stringify({ name, percentage }),
        });

        const data = await res.json();

        if (res.ok && data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editTaxModal')).hide();
            showToast('Tax updated successfully!', 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            if (data.errors) {
                if (data.errors.name)       showFieldError('edit_name',       data.errors.name[0]);
                if (data.errors.percentage) showFieldError('edit_percentage', data.errors.percentage[0]);
            } else {
                showToast(data.message || 'Something went wrong.', 'error');
            }
        }
    } catch (e) {
        showToast('Request failed. Check console.', 'error');
        console.error(e);
    } finally {
        btn.disabled    = false;
        btn.textContent = 'Update';
    }
}

// ── Delete Tax ───────────────────────────────────────
async function deleteTax(id) {
    if (!confirm('Are you sure you want to delete this tax?')) return;

    try {
        // ✅ সঠিক URL
        const res = await fetch(`${BASE_URL}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': CSRF,
                'Accept'      : 'application/json',
            },
        });

        const data = await res.json();

        if (res.ok && data.success) {
            document.getElementById(`tax-row-${id}`).remove();
            showToast('Tax deleted.', 'success');
        } else {
            showToast(data.message || 'Delete failed.', 'error');
        }
    } catch (e) {
        showToast('Request failed. Check console.', 'error');
        console.error(e);
    }
}

// ── Toggle Status ────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tax-toggle').forEach(function (toggle) {
        toggle.addEventListener('change', async function () {
            const id = this.dataset.id;
            const cb = this;

            try {
                // ✅ সঠিক URL: /admin/alltaxes/{id}/toggle
                const res = await fetch(`${BASE_URL}/${id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept'      : 'application/json',
                    },
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    showToast('Status updated.', 'success');
                } else {
                    cb.checked = !cb.checked; // revert
                    showToast('Update failed.', 'error');
                }
            } catch (e) {
                cb.checked = !cb.checked; // revert
                showToast('Request failed.', 'error');
                console.error(e);
            }
        });
    });
});

// ── Helpers ──────────────────────────────────────────
function showFieldError(fieldId, msg) {
    const input = document.getElementById(fieldId);
    const err   = document.getElementById(fieldId + '_err');
    if (input) input.classList.add('is-invalid');
    if (err)  { err.textContent = msg; err.style.display = 'block'; }
}

function clearErrors(fieldIds) {
    fieldIds.forEach(id => {
        const input = document.getElementById(id);
        const err   = document.getElementById(id + '_err');
        if (input) input.classList.remove('is-invalid');
        if (err)  { err.textContent = ''; err.style.display = 'none'; }
    });
}

function showToast(msg, type = 'success') {
    const toast          = document.getElementById('taxToast');
    toast.textContent    = msg;
    toast.style.background = type === 'success' ? '#16a34a' : '#dc2626';
    toast.style.opacity  = '1';
    toast.style.display  = 'block';
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => { toast.style.display = 'none'; }, 400);
    }, 2500);
}
</script>
@endsection
