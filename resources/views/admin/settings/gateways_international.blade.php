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
    <h2 class="page-title">International Payment Gateways</h2>

    <div class="gateway-grid">
        <div class="gw-card" id="stripe">
            <div class="gw-card-header">
                <h3>Stripe</h3>
                <div class="toggle-wrap">
                    <span class="toggle-label">{{ $stripe && $stripe->status ? 'On' : 'Off' }}</span>
                    <label class="toggle-switch">
                        <input type="checkbox" id="toggle-stripe" {{ $stripe && $stripe->status ? 'checked' : '' }}
                            onchange="ajaxToggle('{{ route('admin.stripe.toggle') }}', this, 'toggle-stripe-label')">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="gw-card-body">
                <div class="gw-logo-area">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/ba/Stripe_Logo%2C_revised_2016.svg" alt="Stripe">
                </div>
                <form action="{{ route('admin.stripe.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="gw-form-group">
                        <label>Mode</label>
                        <input type="text" name="mode" class="gw-form-control" value="{{ $stripe->mode ?? 'Test' }}">
                    </div>
                    <div class="gw-form-group">
                        <label>Secret Key <span class="req">*</span></label>
                        <input type="text" name="secret_key" class="gw-form-control" value="{{ $stripe->secret_key ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Published Key <span class="req">*</span></label>
                        <input type="text" name="published_key" class="gw-form-control" value="{{ $stripe->published_key ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Payment Gateway Title <span class="req">*</span></label>
                        <input type="text" name="title" class="gw-form-control" value="{{ $stripe->title ?? 'Stripe' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Choose Logo</label>
                        <div class="gw-file-wrap">
                            <span class="gw-file-btn" onclick="document.getElementById('stripe-logo').click()">Choose File</span>
                            <span class="gw-file-name" id="stripe-logo-name">No file chosen</span>
                            <input type="file" id="stripe-logo" name="logo" accept="image/*" onchange="updateFileName(this,'stripe-logo-name')">
                        </div>
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>

        <div class="gw-card" id="paypal">
            <div class="gw-card-header">
                <h3>PayPal</h3>
                <div class="toggle-wrap">
                    <span class="toggle-label">{{ $paypal && $paypal->status ? 'On' : 'Off' }}</span>
                    <label class="toggle-switch">
                        <input type="checkbox" id="toggle-paypal" {{ $paypal && $paypal->status ? 'checked' : '' }}
                            onchange="ajaxToggle('{{ route('admin.paypal.toggle') }}', this, 'toggle-paypal-label')">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="gw-card-body">
                <div class="gw-logo-area">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/a4/Paypal_2014_logo.png" alt="PayPal">
                </div>
                <form action="{{ route('admin.paypal.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="gw-form-group">
                        <label>Mode</label>
                        <input type="text" name="mode" class="gw-form-control" value="{{ $paypal->mode ?? 'Test' }}">
                    </div>
                    <div class="gw-form-group">
                        <label>Client Id <span class="req">*</span></label>
                        <input type="text" name="client_id" class="gw-form-control" value="{{ $paypal->client_id ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Client Secret <span class="req">*</span></label>
                        <input type="text" name="client_secret" class="gw-form-control" value="{{ $paypal->client_secret ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Payment Gateway Title <span class="req">*</span></label>
                        <input type="text" name="title" class="gw-form-control" value="{{ $paypal->title ?? 'PayPal' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Choose Logo</label>
                        <div class="gw-file-wrap">
                            <span class="gw-file-btn" onclick="document.getElementById('paypal-logo').click()">Choose File</span>
                            <span class="gw-file-name" id="paypal-logo-name">No file chosen</span>
                            <input type="file" id="paypal-logo" name="logo" accept="image/*" onchange="updateFileName(this,'paypal-logo-name')">
                        </div>
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>
    </div>

    <div class="gateway-grid">
        <div class="gw-card" id="razorpay">
            <div class="gw-card-header">
                <h3>Razorpay</h3>
                <div class="toggle-wrap">
                    <span class="toggle-label">{{ $razorpay && $razorpay->status ? 'On' : 'Off' }}</span>
                    <label class="toggle-switch">
                        <input type="checkbox" {{ $razorpay && $razorpay->status ? 'checked' : '' }}
                            onchange="ajaxToggle('{{ route('admin.razorpay.toggle') }}', this)">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="gw-card-body">
                <div class="gw-logo-area">
                    <img src="https://razorpay.com/assets/razorpay-logo.svg" alt="Razorpay"
                        onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/8/89/Razorpay_logo.svg'">
                </div>
                <form action="{{ route('admin.razorpay.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="gw-form-group">
                        <label>Mode</label>
                        <input type="text" name="mode" class="gw-form-control" value="{{ $razorpay->mode ?? 'Test' }}">
                    </div>
                    <div class="gw-form-group">
                        <label>Key <span class="req">*</span></label>
                        <input type="text" name="key" class="gw-form-control" value="{{ $razorpay->key ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Secret <span class="req">*</span></label>
                        <input type="text" name="secret" class="gw-form-control" value="{{ $razorpay->secret ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Payment Gateway Title <span class="req">*</span></label>
                        <input type="text" name="title" class="gw-form-control" value="{{ $razorpay->title ?? 'Razorpay' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Choose Logo</label>
                        <div class="gw-file-wrap">
                            <span class="gw-file-btn" onclick="document.getElementById('razorpay-logo').click()">Choose File</span>
                            <span class="gw-file-name" id="razorpay-logo-name">No file chosen</span>
                            <input type="file" id="razorpay-logo" name="logo" accept="image/*" onchange="updateFileName(this,'razorpay-logo-name')">
                        </div>
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>

        <div class="gw-card" id="paystack">
            <div class="gw-card-header">
                <h3>Paystack</h3>
                <div class="toggle-wrap">
                    <span class="toggle-label">{{ $paystack && $paystack->status ? 'On' : 'Off' }}</span>
                    <label class="toggle-switch">
                        <input type="checkbox" {{ $paystack && $paystack->status ? 'checked' : '' }}
                            onchange="ajaxToggle('{{ route('admin.paystack.toggle') }}', this)">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="gw-card-body">
                <div class="gw-logo-area">
                    <img src="https://website-v3-assets.s3.amazonaws.com/assets/img/hero/Paystack-mark-white-twitter.png"
                        style="filter:invert(1)" alt="Paystack"
                        onerror="this.outerHTML='<span style=font-size:24px;font-weight:800;color:#00C3F7>paystack</span>'">
                </div>
                <form action="{{ route('admin.paystack.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="gw-form-group">
                        <label>Mode</label>
                        <input type="text" name="mode" class="gw-form-control" value="{{ $paystack->mode ?? 'Test' }}">
                    </div>
                    <div class="gw-form-group">
                        <label>Public Key <span class="req">*</span></label>
                        <input type="text" name="public_key" class="gw-form-control" value="{{ $paystack->public_key ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Secret Key <span class="req">*</span></label>
                        <input type="text" name="secret_key" class="gw-form-control" value="{{ $paystack->secret_key ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Machant Email <span class="req">*</span></label>
                        <input type="email" name="merchant_email" class="gw-form-control" value="{{ $paystack->merchant_email ?? '' }}" placeholder="Machant Email">
                    </div>
                    <div class="gw-form-group">
                        <label>Payment Gateway Title <span class="req">*</span></label>
                        <input type="text" name="title" class="gw-form-control" value="{{ $paystack->title ?? 'Paystack' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Choose Logo</label>
                        <div class="gw-file-wrap">
                            <span class="gw-file-btn" onclick="document.getElementById('paystack-logo').click()">Choose File</span>
                            <span class="gw-file-name" id="paystack-logo-name">No file chosen</span>
                            <input type="file" id="paystack-logo" name="logo" accept="image/*" onchange="updateFileName(this,'paystack-logo-name')">
                        </div>
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>
    </div>

    <div class="gateway-grid">
        <div class="gw-card" id="paytabs">
            <div class="gw-card-header">
                <h3>PayTabs</h3>
                <div class="toggle-wrap">
                    <span class="toggle-label">{{ $paytabs && $paytabs->status ? 'On' : 'Off' }}</span>
                    <label class="toggle-switch">
                        <input type="checkbox" {{ $paytabs && $paytabs->status ? 'checked' : '' }}
                            onchange="ajaxToggle('{{ route('admin.paytabs.toggle') }}', this)">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="gw-card-body">
                <div class="gw-logo-area">
                    <img src="https://www.paytabs.com/wp-content/themes/paytabs/images/paytabs-logo.svg" alt="PayTabs"
                        onerror="this.outerHTML='<span style=font-size:22px;font-weight:800;color:#1B63B4>PayTabs</span>'">
                </div>
                <form action="{{ route('admin.paytabs.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="gw-form-group">
                        <label>Mode</label>
                        <input type="text" name="mode" class="gw-form-control" value="{{ $paytabs->mode ?? 'Test' }}">
                    </div>
                    <div class="gw-form-group">
                        <label>Base Url <span class="req">*</span></label>
                        <input type="text" name="base_url" class="gw-form-control" value="{{ $paytabs->base_url ?? 'https://secure-global.paytabs.com' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Currency <span class="req">*</span></label>
                        <input type="text" name="currency" class="gw-form-control" value="{{ $paytabs->currency ?? 'USD' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Profile Id <span class="req">*</span></label>
                        <input type="text" name="profile_id" class="gw-form-control" value="{{ $paytabs->profile_id ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Server Key <span class="req">*</span></label>
                        <input type="text" name="server_key" class="gw-form-control" value="{{ $paytabs->server_key ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Payment Gateway Title <span class="req">*</span></label>
                        <input type="text" name="title" class="gw-form-control" value="{{ $paytabs->title ?? 'PayTabs' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Choose Logo</label>
                        <div class="gw-file-wrap">
                            <span class="gw-file-btn" onclick="document.getElementById('paytabs-logo').click()">Choose File</span>
                            <span class="gw-file-name" id="paytabs-logo-name">No file chosen</span>
                            <input type="file" id="paytabs-logo" name="logo" accept="image/*" onchange="updateFileName(this,'paytabs-logo-name')">
                        </div>
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>

        <div class="gw-card" id="qicard">
            <div class="gw-card-header">
                <h3>QiCard</h3>
                <div class="toggle-wrap">
                    <span class="toggle-label">{{ $qicard && $qicard->status ? 'On' : 'Off' }}</span>
                    <label class="toggle-switch">
                        <input type="checkbox" {{ $qicard && $qicard->status ? 'checked' : '' }}
                            onchange="ajaxToggle('{{ route('admin.qicard.toggle') }}', this)">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="gw-card-body">
                <div class="gw-logo-area">
                    <img src="https://qicard.com.iq/wp-content/uploads/2021/01/logo-02.png" alt="QiCard"
                        onerror="this.outerHTML='<span style=font-size:22px;font-weight:800;color:#F9A825>QiCard</span>'">
                </div>
                <form action="{{ route('admin.qicard.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="gw-form-group">
                        <label>Mode</label>
                        <input type="text" name="mode" class="gw-form-control" value="{{ $qicard->mode ?? 'Test' }}">
                    </div>
                    <div class="gw-form-group">
                        <label>Currency <span class="req">*</span></label>
                        <input type="text" name="currency" class="gw-form-control" value="{{ $qicard->currency ?? 'IQD' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Password <span class="req">*</span></label>
                        <input type="text" name="password" class="gw-form-control" value="{{ $qicard->password ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Username <span class="req">*</span></label>
                        <input type="text" name="username" class="gw-form-control" value="{{ $qicard->username ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>TerminalId <span class="req">*</span></label>
                        <input type="text" name="terminal_id" class="gw-form-control" value="{{ $qicard->terminal_id ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Payment Gateway Title <span class="req">*</span></label>
                        <input type="text" name="title" class="gw-form-control" value="{{ $qicard->title ?? 'QiCard' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Choose Logo</label>
                        <div class="gw-file-wrap">
                            <span class="gw-file-btn" onclick="document.getElementById('qicard-logo').click()">Choose File</span>
                            <span class="gw-file-name" id="qicard-logo-name">No file chosen</span>
                            <input type="file" id="qicard-logo" name="logo" accept="image/*" onchange="updateFileName(this,'qicard-logo-name')">
                        </div>
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>
    </div>

    <div class="gateway-grid single">
        <div class="gw-card" id="jazzcash">
            <div class="gw-card-header">
                <h3>JazzCash</h3>
                <div class="toggle-wrap">
                    <span class="toggle-label">{{ $jazzcash && $jazzcash->status ? 'On' : 'Off' }}</span>
                    <label class="toggle-switch">
                        <input type="checkbox" {{ $jazzcash && $jazzcash->status ? 'checked' : '' }}
                            onchange="ajaxToggle('{{ route('admin.jazzcash.toggle') }}', this)">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
            <div class="gw-card-body">
                <div class="gw-logo-area">
                    <img src="https://jazzcash.com.pk/wp-content/uploads/2020/03/Jazz-Cash-Logo-01.png" alt="JazzCash"
                        onerror="this.outerHTML='<span style=font-size:22px;font-weight:800;color:#CC0000>JazzCash</span>'">
                </div>
                <div class="gw-alert-warning">
                    You have to setup this return URL in your JazzCash merchant account dashboard:
                    <strong>{{ url('/payment/callback-success') }}</strong>
                </div>
                <form action="{{ route('admin.jazzcash.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="gw-form-group">
                        <label>Mode</label>
                        <select name="mode" class="gw-form-control">
                            <option value="test" {{ ($jazzcash->mode ?? 'test') == 'test' ? 'selected' : '' }}>Test</option>
                            <option value="live" {{ ($jazzcash->mode ?? '') == 'live' ? 'selected' : '' }}>Live</option>
                        </select>
                    </div>
                    <div class="gw-form-group">
                        <label>Base Url <span class="req">*</span></label>
                        <input type="text" name="base_url" class="gw-form-control"
                            value="{{ $jazzcash->base_url ?? 'https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Password <span class="req">*</span></label>
                        <input type="text" name="password" class="gw-form-control" value="{{ $jazzcash->password ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Merchant Id <span class="req">*</span></label>
                        <input type="text" name="merchant_id" class="gw-form-control" value="{{ $jazzcash->merchant_id ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Integrity Salt <span class="req">*</span></label>
                        <input type="text" name="integrity_salt" class="gw-form-control" value="{{ $jazzcash->integrity_salt ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Payment Gateway Title <span class="req">*</span></label>
                        <input type="text" name="title" class="gw-form-control" value="{{ $jazzcash->title ?? 'JazzCash' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Choose Logo</label>
                        <div class="gw-file-wrap">
                            <span class="gw-file-btn" onclick="document.getElementById('jazzcash-logo').click()">Choose File</span>
                            <span class="gw-file-name" id="jazzcash-logo-name">No file chosen</span>
                            <input type="file" id="jazzcash-logo" name="logo" accept="image/*" onchange="updateFileName(this,'jazzcash-logo-name')">
                        </div>
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
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
</script>
@endsection
