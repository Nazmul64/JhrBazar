@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color:#1a1a2e; font-size:22px;">SMS Gateway Setup</h4>
        <a href="{{ route('admin.smsgatewaysetup.create') }}" class="btn-add-gateway">
            <i class="fas fa-plus me-1"></i> Add New Gateway
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
                            <th class="ps-4 py-3 gw-th">SL.</th>
                            <th class="py-3 gw-th">URL</th>
                            <th class="py-3 gw-th">API Key</th>
                            <th class="py-3 gw-th">Sender ID</th>
                            <th class="py-3 gw-th text-center">Status</th>
                            <th class="py-3 gw-th text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gateways as $index => $gateway)
                        <tr class="gw-row">
                            <td class="ps-4 gw-td-muted">{{ $loop->iteration }}</td>
                            <td class="gw-td-value">{{ $gateway->url }}</td>
                            <td class="gw-td-muted">{{ Str::limit($gateway->api_key, 25) }}</td>
                            <td class="gw-td-value">{{ $gateway->sender_id }}</td>
                            <td class="text-center">
                                <label class="sms-switch">
                                    <input type="checkbox" 
                                           class="gateway-status-checkbox"
                                           onchange="toggleGatewayStatus({{ $gateway->id }}, this)"
                                           {{ $gateway->status ? 'checked' : '' }}>
                                    <span class="sms-slider"></span>
                                </label>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.smsgatewaysetup.edit', $gateway->id) }}"
                                       class="gw-action-btn gw-edit" title="Edit Gateway">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.smsgatewaysetup.destroy', $gateway->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this gateway?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="gw-action-btn gw-del" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <span class="text-muted">No SMS gateways configured.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Dynamic Toast Notification --}}
<div id="status-toast" class="status-toast"></div>

<style>
.btn-add-gateway {
    display: inline-flex; align-items: center; gap: 6px;
    background: linear-gradient(135deg, #0faf96, #0d9480);
    color: #fff !important; border: none; border-radius: 4px;
    padding: 9px 20px; font-size: 14px; font-weight: 500;
    text-decoration: none; transition: all .2s ease;
    box-shadow: 0 3px 10px rgba(15, 175, 150, 0.3);
}
.btn-add-gateway:hover {
    background: linear-gradient(135deg, #0d9480, #0b806e);
}
.gw-th {
    font-size: 13px; font-weight: 600; color: #555;
    border-bottom: 1px solid #e8e8e8 !important;
    text-transform: uppercase; letter-spacing: .4px;
}
.gw-row { border-bottom: 1px solid #f7f7f7 !important; transition: background .15s; }
.gw-row:hover { background: #fafafa !important; }
.gw-td-value { font-size: 14px; font-weight: 500; color: #222; }
.gw-td-muted { font-size: 13px; color: #666; }

/* Switch style */
.sms-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}
.sms-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.sms-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: .3s;
    border-radius: 24px;
}
.sms-slider:before {
    position: absolute;
    content: "";
    height: 18px; width: 18px;
    left: 3px; bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}
.sms-switch input:checked + .sms-slider {
    background-color: #10b981;
}
.sms-switch input:checked + .sms-slider:before {
    transform: translateX(26px);
}

/* Action buttons */
.gw-action-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 4px; border: none;
    font-size: 13px; cursor: pointer; transition: all .2s; text-decoration: none;
}
.gw-edit { background: rgba(255,152,0,.1); color: #ff9800; }
.gw-edit:hover { background: #ff9800; color: #fff; }
.gw-del { background: rgba(244,67,54,.1); color: #f44336; }
.gw-del:hover { background: #f44336; color: #fff; }

/* Toast styles */
.status-toast {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #1f2937;
    color: #fff;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    display: none;
    z-index: 9999;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    animation: fadeInUp 0.3s ease-out;
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
function toggleGatewayStatus(id, checkbox) {
    const isChecked = checkbox.checked;
    const url = `/admin/smsgatewaysetup/${id}/toggle`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.status) {
                document.querySelectorAll('.gateway-status-checkbox').forEach(cb => {
                    if (cb !== checkbox) {
                        cb.checked = false;
                    }
                });
            }
            showToast(data.message);
        } else {
            checkbox.checked = !isChecked;
            showToast('Failed to update status', 'error');
        }
    })
    .catch(error => {
        checkbox.checked = !isChecked;
        showToast('An error occurred', 'error');
    });
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('status-toast');
    toast.textContent = message;
    if (type === 'error') {
        toast.style.background = '#ef4444';
    } else {
        toast.style.background = '#10b981';
    }
    toast.style.display = 'block';
    setTimeout(() => {
        toast.style.display = 'none';
    }, 3000);
}
</script>
@endsection
