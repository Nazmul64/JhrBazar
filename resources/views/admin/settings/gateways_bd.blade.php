@extends('admin.master')

@section('content')
<style>
/* ═══════════════════════════════════════════
   GATEWAY SETTINGS — GLOBAL STYLES
═══════════════════════════════════════════ */
.gateway-page {
    background: #f4f6f9;
    min-height: 100vh;
    padding: 28px 24px;
    font-family: 'Segoe UI', sans-serif;
}

.page-title {
    font-size: 22px;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 28px;
    letter-spacing: .3px;
}

/* ── Grid Wrapper ── */
.gateway-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}
.gateway-grid.single {
    grid-template-columns: 1fr;
    max-width: 780px;
}

/* ── Card ── */
.gw-card {
    background: #ffffff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.07);
}

.gw-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 22px;
    border-bottom: 1px solid #f0f0f0;
}

.gw-card-header h3 {
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 1px;
    color: #1a1a2e;
    margin: 0;
    text-transform: uppercase;
}

.gw-card-body {
    padding: 24px 24px 20px;
}

/* ── Logo Area ── */
.gw-logo-area {
    text-align: center;
    margin-bottom: 24px;
}

.gw-logo-area img {
    max-height: 45px;
    max-width: 180px;
    object-fit: contain;
}

/* ── Toggle Switch ── */
.toggle-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
}

.toggle-label {
    font-size: 13px;
    font-weight: 600;
    color: #e91e8c;
}

.toggle-switch {
    position: relative;
    width: 46px;
    height: 24px;
    cursor: pointer;
    display: inline-block;
}

.toggle-switch input { display: none; }

.toggle-slider {
    position: absolute;
    inset: 0;
    background: #ccc;
    border-radius: 34px;
    transition: .3s;
}

.toggle-slider:before {
    content: '';
    position: absolute;
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background: #fff;
    border-radius: 50%;
    transition: .3s;
}

.toggle-switch input:checked + .toggle-slider { background: #e91e8c; }
.toggle-switch input:checked + .toggle-slider:before { transform: translateX(22px); }

/* ── Form Fields ── */
.gw-form-group {
    margin-bottom: 16px;
}

gw-form-group label {
    display: block;
    margin-bottom: 8px;
    font-size: 13px;
    color: #444;
}

.gw-form-control {
    width: 100%;
    border: 1px solid #d9d9d9;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 13px;
    color: #333;
    background: #fff;
}

.gw-file-wrap {
    display: flex;
    align-items: center;
    gap: 12px;
}
.gw-file-wrap input[type="file"] {
    display: none;
}

.gw-file-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #0d6efd;
    color: #fff;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
}

.gw-file-name {
    font-size: 13px;
    color: #666;
    word-break: break-word;
}

.gw-card-footer {
    padding: 16px 24px 24px;
}

.btn-save,
.btn-submit-teal,
.btn-submit-indigo {
    display: inline-block;
    border: none;
    border-radius: 8px;
    padding: 12px 20px;
    background: #0d6efd;
    color: #fff;
    cursor: pointer;
    font-weight: 700;
}

.btn-submit-teal { background: #26c6a6; }
.btn-submit-indigo { background: #5c6bc0; }

.section-label {
    display: inline-block;
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    margin: 30px 0 16px;
    color: #1a1a2e;
}
</style>

<div class="gateway-page">
    <h2 class="page-title">BD Payment Gateways</h2>

    <div class="gateway-grid">
        {{-- ── AAMARPAY ── --}}
        <div class="gw-card" id="aamarpay">
            <div class="gw-card-header">
                <h3>AamarPay</h3>
                <div class="toggle-wrap">
                    <span class="toggle-label">{{ $aamarpay && $aamarpay->status ? 'On' : 'Off' }}</span>
                    <label class="toggle-switch">
                        <input type="checkbox" {{ $aamarpay && $aamarpay->status ? 'checked' : '' }}
                            onchange="ajaxToggle('{{ route('admin.aamarpay.toggle') }}', this)">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="gw-card-body">
                <div class="gw-logo-area">
                    <img src="https://aamarpay.com/assets/images/aamarPay-logo.png" alt="aamarPay"
                        onerror="this.outerHTML='<span style=font-size:22px;font-weight:800;color:#f7931e>aamarPay</span>'">
                </div>

                <form action="{{ route('admin.aamarpay.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="gw-form-group">
                        <label>Mode</label>
                        <select name="mode" class="gw-form-control">
                            <option value="test" {{ ($aamarpay->mode ?? 'test') == 'test' ? 'selected' : '' }}>Test (Sandbox)</option>
                            <option value="live" {{ ($aamarpay->mode ?? 'test') == 'live' ? 'selected' : '' }}>Live (Production)</option>
                        </select>
                    </div>
                    <div class="gw-form-group">
                        <label>Store Id <span class="req">*</span></label>
                        <input type="text" name="store_id" class="gw-form-control"
                            value="{{ $aamarpay->store_id ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Signature Key <span class="req">*</span></label>
                        <input type="text" name="signature_key" class="gw-form-control"
                            value="{{ $aamarpay->signature_key ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Payment Gateway Title <span class="req">*</span></label>
                        <input type="text" name="title" class="gw-form-control"
                            value="{{ $aamarpay->title ?? 'aamarPay' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Choose Logo</label>
                        <div class="gw-file-wrap">
                            <span class="gw-file-btn" onclick="document.getElementById('aamarpay-logo').click()">Choose File</span>
                            <span class="gw-file-name" id="aamarpay-logo-name">No file chosen</span>
                            <input type="file" id="aamarpay-logo" name="logo" accept="image/*"
                                onchange="updateFileName(this,'aamarpay-logo-name')">
                        </div>
                        @if($aamarpay && $aamarpay->logo)
                            <div class="current-logo-preview" style="margin-top: 10px;">
                                <span style="display:block; font-size:12px; color:#666; margin-bottom:4px;">Current Logo:</span>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <img src="{{ Str::startsWith($aamarpay->logo, ['http://', 'https://']) ? $aamarpay->logo : (Str::startsWith($aamarpay->logo, ['storage/', 'uploads/']) ? asset($aamarpay->logo) : asset('storage/' . $aamarpay->logo)) }}" 
                                         alt="AamarPay Logo" style="max-height: 40px; border: 1px solid #dde2ec; border-radius: 4px; padding: 4px; background: #fff;">
                                    <button type="button" class="btn-delete-logo" onclick="deleteGatewayLogo('aamarpay', this)" style="background:#ff5252; color:#fff; border:none; padding:6px 12px; border-radius:6px; font-size:11px; cursor:pointer; font-weight:bold;">Delete Logo</button>
                                </div>
                            </div>
                        @endif
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>

        {{-- ── BKASH (gateway panel) ── --}}
        <div class="gw-card" id="bkash">
            <div class="gw-card-header">
                <h3>BKash</h3>
                <div class="toggle-wrap">
                    <span class="toggle-label">{{ $bkash && $bkash->status ? 'On' : 'Off' }}</span>
                    <label class="toggle-switch">
                        <input type="checkbox" {{ $bkash && $bkash->status ? 'checked' : '' }}
                            onchange="ajaxToggle('{{ route('admin.bkash.toggle') }}', this)">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="gw-card-body">
                <div class="gw-logo-area">
                    <img src="https://www.bkash.com/sites/default/files/bkash-logo.png" alt="bKash"
                        onerror="this.outerHTML='<span style=font-size:24px;font-weight:800;color:#E2136E>bKash</span>'">
                </div>

                <form action="{{ route('admin.bkash.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="gw-form-group">
                        <label>Mode</label>
                        <select name="mode" class="gw-form-control">
                            <option value="test" {{ ($bkash->mode ?? 'test') == 'test' ? 'selected' : '' }}>Test (Sandbox)</option>
                            <option value="live" {{ ($bkash->mode ?? 'test') == 'live' ? 'selected' : '' }}>Live (Production)</option>
                        </select>
                    </div>
                    <div class="gw-form-group">
                        <label>Base Url <span class="req">*</span></label>
                        <input type="text" name="base_url" class="gw-form-control"
                            value="{{ $bkash->base_url ?? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>App Key <span class="req">*</span></label>
                        <input type="text" name="app_key" class="gw-form-control"
                            value="{{ $bkash->app_key ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Password <span class="req">*</span></label>
                        <input type="text" name="password" class="gw-form-control"
                            value="{{ $bkash->password ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Username <span class="req">*</span></label>
                        <input type="text" name="username" class="gw-form-control"
                            value="{{ $bkash->username ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>App Secret Key <span class="req">*</span></label>
                        <input type="text" name="app_secret_key" class="gw-form-control"
                            value="{{ $bkash->app_secret_key ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Payment Gateway Title <span class="req">*</span></label>
                        <input type="text" name="title" class="gw-form-control"
                            value="{{ $bkash->title ?? 'BKash' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Choose Logo</label>
                        <div class="gw-file-wrap">
                            <span class="gw-file-btn" onclick="document.getElementById('bkash-logo').click()">Choose File</span>
                            <span class="gw-file-name" id="bkash-logo-name">No file chosen</span>
                            <input type="file" id="bkash-logo" name="logo" accept="image/*"
                                onchange="updateFileName(this,'bkash-logo-name')">
                        </div>
                        @if($bkash && $bkash->logo)
                            <div class="current-logo-preview" style="margin-top: 10px;">
                                <span style="display:block; font-size:12px; color:#666; margin-bottom:4px;">Current Logo:</span>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <img src="{{ Str::startsWith($bkash->logo, ['http://', 'https://']) ? $bkash->logo : (Str::startsWith($bkash->logo, ['storage/', 'uploads/']) ? asset($bkash->logo) : asset('storage/' . $bkash->logo)) }}" 
                                         alt="BKash Logo" style="max-height: 40px; border: 1px solid #dde2ec; border-radius: 4px; padding: 4px; background: #fff;">
                                    <button type="button" class="btn-delete-logo" onclick="deleteGatewayLogo('bkash', this)" style="background:#ff5252; color:#fff; border:none; padding:6px 12px; border-radius:6px; font-size:11px; cursor:pointer; font-weight:bold;">Delete Logo</button>
                                </div>
                            </div>
                        @endif
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>
    </div>

    <span class="section-label">BD Payment Management</span>

    <div class="gw-card" id="nagad" style="margin-bottom:20px;">
        <div class="gw-card-header">
            <h3>Nagad</h3>
        </div>
        <div class="gw-card-body">
            <div class="gw-logo-area">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/54/Nagad_Logo.png" alt="Nagad"
                    onerror="this.outerHTML='<span style=font-size:24px;font-weight:800;color:#ef4a4a>Nagad</span>'">
            </div>
            <p style="color:#4a4a4a;line-height:1.6;">Nagad integration is available as a dedicated BD gateway section. Add the gateway configuration here when the Nagad gateway module is ready.</p>
        </div>
    </div>

    <div class="gw-card" id="shurjopay" style="margin-bottom:20px;">
        <div class="gw-card-header">
            <h3>Shurjopay</h3>
        </div>
        <form action="{{ route('admin.shurjopay.update') }}" method="POST">
            @csrf
            <div class="gw-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                    <div class="gw-form-group">
                        <label>User Name <span class="req">*</span></label>
                        <input type="text" name="username" class="gw-form-control"
                            value="{{ $shurjopay->username ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Prefix <span class="req">*</span></label>
                        <input type="text" name="prefix" class="gw-form-control"
                            value="{{ $shurjopay->prefix ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Success Url <span class="req">*</span></label>
                        <input type="text" name="success_url" class="gw-form-control"
                            value="{{ $shurjopay->success_url ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Return Url <span class="req">*</span></label>
                        <input type="text" name="return_url" class="gw-form-control"
                            value="{{ $shurjopay->return_url ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Base Url <span class="req">*</span></label>
                        <input type="text" name="base_url" class="gw-form-control"
                            value="{{ $shurjopay->base_url ?? 'https://sandbox.shurjopayment.com' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Password <span class="req">*</span></label>
                        <input type="text" name="password" class="gw-form-control"
                            value="{{ $shurjopay->password ?? '' }}" required>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:12px;margin-top:8px;">
                    <label style="margin:0;font-size:13px;color:#444;">Status</label>
                    <label class="toggle-switch">
                        <input type="checkbox" name="status" value="1" {{ $shurjopay && $shurjopay->status ? 'checked' : '' }}>
                        <span class="toggle-slider" style="background:#5c6bc0;"></span>
                    </label>
                </div>
            </div>
            <div style="padding:0 24px 20px;">
                <button type="submit" class="btn-submit-indigo">Submit</button>
            </div>
        </form>
    </div>

    <div class="gw-card" id="sslcommerz" style="margin-bottom:20px;">
        <div class="gw-card-header">
            <h3>SSLCommerz</h3>
            <div class="toggle-wrap">
                <span class="toggle-label">{{ $sslcommerz && $sslcommerz->status ? 'On' : 'Off' }}</span>
                <label class="toggle-switch">
                    <input type="checkbox" {{ $sslcommerz && $sslcommerz->status ? 'checked' : '' }}
                        onchange="ajaxToggle('{{ route('admin.sslcommerz.toggle') }}', this)">
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
        <div class="gw-card-body">
            <div class="gw-logo-area">
                <img src="https://securepay.sslcommerz.com/gw/asset/img/sslcommerz-logo.png" alt="SSLCommerz"
                    onerror="this.outerHTML='<span style=font-size:24px;font-weight:800;color:#005c92>SSLCommerz</span>'">
            </div>

            <form action="{{ route('admin.sslcommerz.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="gw-form-group">
                    <label>Mode</label>
                    <select name="mode" class="gw-form-control">
                        <option value="test" {{ ($sslcommerz->mode ?? 'test') == 'test' ? 'selected' : '' }}>Test (Sandbox)</option>
                        <option value="live" {{ ($sslcommerz->mode ?? 'test') == 'live' ? 'selected' : '' }}>Live (Production)</option>
                    </select>
                </div>
                <div class="gw-form-group">
                    <label>Store Id <span class="req">*</span></label>
                    <input type="text" name="store_id" class="gw-form-control"
                        value="{{ $sslcommerz->store_id ?? '' }}" required>
                </div>
                <div class="gw-form-group">
                    <label>Store Password <span class="req">*</span></label>
                    <input type="text" name="store_password" class="gw-form-control"
                        value="{{ $sslcommerz->store_password ?? '' }}" required>
                </div>
                <div class="gw-form-group">
                    <label>Payment Gateway Title <span class="req">*</span></label>
                    <input type="text" name="title" class="gw-form-control"
                        value="{{ $sslcommerz->title ?? 'SSLCommerz' }}" required>
                </div>
                <div class="gw-form-group">
                    <label>Choose Logo</label>
                    <div class="gw-file-wrap">
                        <span class="gw-file-btn" onclick="document.getElementById('sslcommerz-logo').click()">Choose File</span>
                        <span class="gw-file-name" id="sslcommerz-logo-name">No file chosen</span>
                        <input type="file" id="sslcommerz-logo" name="logo" accept="image/*"
                            onchange="updateFileName(this,'sslcommerz-logo-name')">
                    </div>
                    @if($sslcommerz && $sslcommerz->logo)
                        <div class="current-logo-preview" style="margin-top: 10px;">
                            <span style="display:block; font-size:12px; color:#666; margin-bottom:4px;">Current Logo:</span>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <img src="{{ Str::startsWith($sslcommerz->logo, ['http://', 'https://']) ? $sslcommerz->logo : (Str::startsWith($sslcommerz->logo, ['storage/', 'uploads/']) ? asset($sslcommerz->logo) : asset('storage/' . $sslcommerz->logo)) }}" 
                                     alt="SSLCommerz Logo" style="max-height: 40px; border: 1px solid #dde2ec; border-radius: 4px; padding: 4px; background: #fff;">
                                <button type="button" class="btn-delete-logo" onclick="deleteGatewayLogo('sslcommerz', this)" style="background:#ff5252; color:#fff; border:none; padding:6px 12px; border-radius:6px; font-size:11px; cursor:pointer; font-weight:bold;">Delete Logo</button>
                            </div>
                        </div>
                    @endif
                </div>
        </div>
        <div class="gw-card-footer">
                <button type="submit" class="btn-save">Save And Update</button>
            </form>
        </div>
    </div>
</div>

<script>
function updateFileName(input, labelId) {
    var label = document.getElementById(labelId);
    if (label) {
        label.textContent = input.files.length ? input.files[0].name : 'No file chosen';
    }
}

function ajaxToggle(url, checkbox, labelId) {
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (labelId) {
            var el = document.getElementById(labelId);
            if (el) el.textContent = data.status ? 'On' : 'Off';
        }
        var wrap = checkbox.closest('.toggle-wrap');
        if (wrap) {
            var span = wrap.querySelector('.toggle-label');
            if (span) span.textContent = data.status ? 'On' : 'Off';
        }
    })
    .catch(function(err){ console.error('Toggle failed:', err); checkbox.checked = !checkbox.checked; });
}

function deleteGatewayLogo(gatewayKey, btn) {
    if (!confirm('Are you sure you want to delete this logo?')) return;
    fetch('{{ route("admin.gateways.delete-logo", ["gateway" => ":gateway"]) }}'.replace(':gateway', gatewayKey), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            var card = btn.closest('.gw-card') || btn.closest('.gateway-box') || btn.closest('.gw-form-group');
            var preview = card.querySelector('.current-logo-preview');
            if (preview) preview.remove();
            alert('Logo deleted successfully!');
        } else {
            alert('Failed to delete logo: ' + data.message);
        }
    })
    .catch(err => {
        console.error('Delete failed:', err);
        alert('An error occurred while deleting the logo.');
    });
}
</script>
@endsection
