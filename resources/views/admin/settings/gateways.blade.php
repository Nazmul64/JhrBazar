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
.gw-form-group label {
    display: block;
    font-size: 13px;
    color: #444;
    margin-bottom: 6px;
    font-weight: 500;
}
.gw-form-group label .req {
    color: #e91e8c;
    margin-left: 2px;
}
.gw-form-control {
    width: 100%;
    padding: 9px 13px;
    font-size: 13px;
    border: 1px solid #dde2ec;
    border-radius: 6px;
    color: #333;
    background: #fff;
    outline: none;
    transition: border-color .2s;
    box-sizing: border-box;
}
.gw-form-control:focus {
    border-color: #e91e8c;
    box-shadow: 0 0 0 3px rgba(233,30,140,.08);
}

/* ── File Input ── */
.gw-file-wrap {
    display: flex;
    align-items: center;
    border: 1px solid #dde2ec;
    border-radius: 6px;
    overflow: hidden;
}
.gw-file-btn {
    padding: 8px 14px;
    background: #f0f0f0;
    font-size: 13px;
    color: #555;
    border-right: 1px solid #dde2ec;
    white-space: nowrap;
    cursor: pointer;
}
.gw-file-name {
    padding: 8px 12px;
    font-size: 13px;
    color: #888;
}
.gw-file-wrap input[type="file"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

/* ── Save Button ── */
.gw-card-footer {
    padding: 0 24px 20px;
    display: flex;
    justify-content: flex-end;
}
.btn-save {
    background: #e91e8c;
    color: #fff;
    border: none;
    padding: 10px 26px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s, transform .1s;
}
.btn-save:hover { background: #c91278; }
.btn-save:active { transform: scale(.97); }

/* ── Submit (teal) ── */
.btn-submit-teal {
    background: #26c6a6;
    color: #fff;
    border: none;
    padding: 9px 22px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s;
}
.btn-submit-teal:hover { background: #1da88a; }

/* ── Submit (indigo - shurjopay) ── */
.btn-submit-indigo {
    background: #5c6bc0;
    color: #fff;
    border: none;
    padding: 9px 22px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s;
}
.btn-submit-indigo:hover { background: #4752b2; }

/* ── Alert boxes ── */
.gw-alert-warning {
    background: #fff8e1;
    border: 1px solid #ffe082;
    border-radius: 6px;
    padding: 12px 14px;
    font-size: 13px;
    color: #6d4c00;
    margin-bottom: 18px;
    line-height: 1.5;
}
.gw-alert-info {
    background: #e3f2fd;
    border: 1px solid #90caf9;
    border-radius: 6px;
    padding: 12px 14px;
    font-size: 13px;
    color: #0d47a1;
    margin-bottom: 18px;
    line-height: 1.5;
}
.gw-alert-info .info-icon {
    margin-right: 6px;
}

/* ── Success toast ── */
.gw-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #2e7d32;
    color: #fff;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    z-index: 9999;
    display: none;
    box-shadow: 0 4px 16px rgba(0,0,0,.15);
    animation: slideIn .3s ease;
}
@keyframes slideIn {
    from { transform: translateX(100px); opacity: 0; }
    to   { transform: translateX(0);     opacity: 1; }
}

/* ── Section Divider Label ── */
.section-label {
    font-size: 16px;
    font-weight: 700;
    color: #1a1a2e;
    margin: 30px 0 14px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e91e8c;
    display: inline-block;
}

/* ── Pathao Info ── */
.pathao-hint {
    font-size: 12px;
    color: #888;
    margin-top: 4px;
}

/* ─── Responsive ─── */
@media(max-width: 900px) {
    .gateway-grid { grid-template-columns: 1fr; }
}
</style>

<div class="gateway-page">

    {{-- ══════════════════════════════════════ --}}
    {{-- PAGE TITLE                            --}}
    {{-- ══════════════════════════════════════ --}}
    <h1 class="page-title">Payment Gateways</h1>

    {{-- ═══════════════════ SUCCESS TOASTS ════════════════════ --}}
    @foreach(['stripe_success','paypal_success','razorpay_success','paystack_success',
              'aamarpay_success','bkash_success','paytabs_success','qicard_success',
              'jazzcash_success','steadfast_success','bkash_payment_success',
              'shurjopay_success','pathao_success','sms_success'] as $key)
        @if(session($key))
            <div class="gw-toast" id="toast-{{ $loop->index }}" style="display:block;">
                ✓ {{ session($key) }}
            </div>
            <script>
                setTimeout(function(){
                    var el = document.getElementById('toast-{{ $loop->index }}');
                    if(el) el.style.display='none';
                }, 4000);
            </script>
        @endif
    @endforeach

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{--  ROW 1 : STRIPE  +  PAYPAL                               --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="gateway-grid">

        {{-- ── STRIPE ── --}}
        <div class="gw-card">
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
                        <input type="text" name="mode" class="gw-form-control"
                            value="{{ $stripe->mode ?? 'Test' }}">
                    </div>
                    <div class="gw-form-group">
                        <label>Secret Key <span class="req">*</span></label>
                        <input type="text" name="secret_key" class="gw-form-control"
                            value="{{ $stripe->secret_key ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Published Key <span class="req">*</span></label>
                        <input type="text" name="published_key" class="gw-form-control"
                            value="{{ $stripe->published_key ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Payment Gateway Title <span class="req">*</span></label>
                        <input type="text" name="title" class="gw-form-control"
                            value="{{ $stripe->title ?? 'Stripe' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Choose Logo</label>
                        <div class="gw-file-wrap">
                            <span class="gw-file-btn" onclick="document.getElementById('stripe-logo').click()">Choose File</span>
                            <span class="gw-file-name" id="stripe-logo-name">No file chosen</span>
                            <input type="file" id="stripe-logo" name="logo" accept="image/*"
                                onchange="updateFileName(this,'stripe-logo-name')">
                        </div>
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>

        {{-- ── PAYPAL ── --}}
        <div class="gw-card">
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
                        <input type="text" name="mode" class="gw-form-control"
                            value="{{ $paypal->mode ?? 'Test' }}">
                    </div>
                    <div class="gw-form-group">
                        <label>Client Id <span class="req">*</span></label>
                        <input type="text" name="client_id" class="gw-form-control"
                            value="{{ $paypal->client_id ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Client Secret <span class="req">*</span></label>
                        <input type="text" name="client_secret" class="gw-form-control"
                            value="{{ $paypal->client_secret ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Payment Gateway Title <span class="req">*</span></label>
                        <input type="text" name="title" class="gw-form-control"
                            value="{{ $paypal->title ?? 'PayPal' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Choose Logo</label>
                        <div class="gw-file-wrap">
                            <span class="gw-file-btn" onclick="document.getElementById('paypal-logo').click()">Choose File</span>
                            <span class="gw-file-name" id="paypal-logo-name">No file chosen</span>
                            <input type="file" id="paypal-logo" name="logo" accept="image/*"
                                onchange="updateFileName(this,'paypal-logo-name')">
                        </div>
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>
    </div>{{-- end stripe+paypal grid --}}


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{--  ROW 2 : RAZORPAY  +  PAYSTACK                           --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="gateway-grid">

        {{-- ── RAZORPAY ── --}}
        <div class="gw-card">
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
                        <input type="text" name="mode" class="gw-form-control"
                            value="{{ $razorpay->mode ?? 'Test' }}">
                    </div>
                    <div class="gw-form-group">
                        <label>Key <span class="req">*</span></label>
                        <input type="text" name="key" class="gw-form-control"
                            value="{{ $razorpay->key ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Secret <span class="req">*</span></label>
                        <input type="text" name="secret" class="gw-form-control"
                            value="{{ $razorpay->secret ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Payment Gateway Title <span class="req">*</span></label>
                        <input type="text" name="title" class="gw-form-control"
                            value="{{ $razorpay->title ?? 'Razorpay' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Choose Logo</label>
                        <div class="gw-file-wrap">
                            <span class="gw-file-btn" onclick="document.getElementById('razorpay-logo').click()">Choose File</span>
                            <span class="gw-file-name" id="razorpay-logo-name">No file chosen</span>
                            <input type="file" id="razorpay-logo" name="logo" accept="image/*"
                                onchange="updateFileName(this,'razorpay-logo-name')">
                        </div>
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>

        {{-- ── PAYSTACK ── --}}
        <div class="gw-card">
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
                        <input type="text" name="mode" class="gw-form-control"
                            value="{{ $paystack->mode ?? 'Test' }}">
                    </div>
                    <div class="gw-form-group">
                        <label>Public Key <span class="req">*</span></label>
                        <input type="text" name="public_key" class="gw-form-control"
                            value="{{ $paystack->public_key ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Secret Key <span class="req">*</span></label>
                        <input type="text" name="secret_key" class="gw-form-control"
                            value="{{ $paystack->secret_key ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Machant Email <span class="req">*</span></label>
                        <input type="email" name="merchant_email" class="gw-form-control"
                            value="{{ $paystack->merchant_email ?? '' }}" placeholder="Machant Email">
                    </div>
                    <div class="gw-form-group">
                        <label>Payment Gateway Title <span class="req">*</span></label>
                        <input type="text" name="title" class="gw-form-control"
                            value="{{ $paystack->title ?? 'Paystack' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Choose Logo</label>
                        <div class="gw-file-wrap">
                            <span class="gw-file-btn" onclick="document.getElementById('paystack-logo').click()">Choose File</span>
                            <span class="gw-file-name" id="paystack-logo-name">No file chosen</span>
                            <input type="file" id="paystack-logo" name="logo" accept="image/*"
                                onchange="updateFileName(this,'paystack-logo-name')">
                        </div>
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>
    </div>{{-- end razorpay+paystack grid --}}


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{--  ROW 3 : AAMARPAY  +  BKASH (gateway)                   --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="gateway-grid">

        {{-- ── AAMARPAY ── --}}
        <div class="gw-card">
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
                        <input type="text" name="mode" class="gw-form-control"
                            value="{{ $aamarpay->mode ?? 'Test' }}">
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
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>

        {{-- ── BKASH (gateway panel) ── --}}
        <div class="gw-card">
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
                        <input type="text" name="mode" class="gw-form-control"
                            value="{{ $bkash->mode ?? 'Test' }}">
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
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>
    </div>{{-- end aamarpay+bkash grid --}}


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{--  ROW 4 : PAYTABS  +  QICARD                              --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="gateway-grid">

        {{-- ── PAYTABS ── --}}
        <div class="gw-card">
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
                        <input type="text" name="mode" class="gw-form-control"
                            value="{{ $paytabs->mode ?? 'Test' }}">
                    </div>
                    <div class="gw-form-group">
                        <label>Base Url <span class="req">*</span></label>
                        <input type="text" name="base_url" class="gw-form-control"
                            value="{{ $paytabs->base_url ?? 'https://secure-global.paytabs.com' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Currency <span class="req">*</span></label>
                        <input type="text" name="currency" class="gw-form-control"
                            value="{{ $paytabs->currency ?? 'USD' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Profile Id <span class="req">*</span></label>
                        <input type="text" name="profile_id" class="gw-form-control"
                            value="{{ $paytabs->profile_id ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Server Key <span class="req">*</span></label>
                        <input type="text" name="server_key" class="gw-form-control"
                            value="{{ $paytabs->server_key ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Payment Gateway Title <span class="req">*</span></label>
                        <input type="text" name="title" class="gw-form-control"
                            value="{{ $paytabs->title ?? 'PayTabs' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Choose Logo</label>
                        <div class="gw-file-wrap">
                            <span class="gw-file-btn" onclick="document.getElementById('paytabs-logo').click()">Choose File</span>
                            <span class="gw-file-name" id="paytabs-logo-name">No file chosen</span>
                            <input type="file" id="paytabs-logo" name="logo" accept="image/*"
                                onchange="updateFileName(this,'paytabs-logo-name')">
                        </div>
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>

        {{-- ── QICARD ── --}}
        <div class="gw-card">
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
                        <input type="text" name="mode" class="gw-form-control"
                            value="{{ $qicard->mode ?? 'Test' }}">
                    </div>
                    <div class="gw-form-group">
                        <label>Currency <span class="req">*</span></label>
                        <input type="text" name="currency" class="gw-form-control"
                            value="{{ $qicard->currency ?? 'IQD' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Password <span class="req">*</span></label>
                        <input type="text" name="password" class="gw-form-control"
                            value="{{ $qicard->password ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Username <span class="req">*</span></label>
                        <input type="text" name="username" class="gw-form-control"
                            value="{{ $qicard->username ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>TerminalId <span class="req">*</span></label>
                        <input type="text" name="terminal_id" class="gw-form-control"
                            value="{{ $qicard->terminal_id ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Payment Gateway Title <span class="req">*</span></label>
                        <input type="text" name="title" class="gw-form-control"
                            value="{{ $qicard->title ?? 'QiCard' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Choose Logo</label>
                        <div class="gw-file-wrap">
                            <span class="gw-file-btn" onclick="document.getElementById('qicard-logo').click()">Choose File</span>
                            <span class="gw-file-name" id="qicard-logo-name">No file chosen</span>
                            <input type="file" id="qicard-logo" name="logo" accept="image/*"
                                onchange="updateFileName(this,'qicard-logo-name')">
                        </div>
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>
    </div>{{-- end paytabs+qicard grid --}}


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{--  ROW 5 : JAZZCASH  (single column)                       --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="gateway-grid single">
        <div class="gw-card">
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
                        <input type="text" name="password" class="gw-form-control"
                            value="{{ $jazzcash->password ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Merchant Id <span class="req">*</span></label>
                        <input type="text" name="merchant_id" class="gw-form-control"
                            value="{{ $jazzcash->merchant_id ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Integrity Salt <span class="req">*</span></label>
                        <input type="text" name="integrity_salt" class="gw-form-control"
                            value="{{ $jazzcash->integrity_salt ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Payment Gateway Title <span class="req">*</span></label>
                        <input type="text" name="title" class="gw-form-control"
                            value="{{ $jazzcash->title ?? 'JazzCash' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Choose Logo</label>
                        <div class="gw-file-wrap">
                            <span class="gw-file-btn" onclick="document.getElementById('jazzcash-logo').click()">Choose File</span>
                            <span class="gw-file-name" id="jazzcash-logo-name">No file chosen</span>
                            <input type="file" id="jazzcash-logo" name="logo" accept="image/*"
                                onchange="updateFileName(this,'jazzcash-logo-name')">
                        </div>
                    </div>
            </div>
            <div class="gw-card-footer">
                    <button type="submit" class="btn-save">Save And Update</button>
                </form>
            </div>
        </div>
    </div>{{-- end jazzcash single --}}


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{--  COURIER SECTION                                          --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <span class="section-label">Courier Services</span>

    {{-- ── STEADFAST ── --}}
    <div class="gw-card" style="margin-bottom:20px;">
        <div class="gw-card-header">
            <h3>Steadfast Courier</h3>
        </div>
        <form action="{{ route('admin.steadfast.update') }}" method="POST">
            @csrf
            <div class="gw-card-body">
                <div class="gateway-grid">
                    <div class="gw-form-group">
                        <label>API key <span class="req">*</span></label>
                        <input type="text" name="api_key" class="gw-form-control"
                            value="{{ $steadfast->api_key ?? '' }}" placeholder="Enter API Key" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Secret key <span class="req">*</span></label>
                        <input type="text" name="secret_key" class="gw-form-control"
                            value="{{ $steadfast->secret_key ?? '' }}" placeholder="Enter Secret Key" required>
                    </div>
                    <div class="gw-form-group">
                        <label>URL <span class="req">*</span></label>
                        <input type="text" name="url" class="gw-form-control"
                            value="{{ $steadfast->url ?? 'https://portal.steadfast.com.bd/api/v1/create_order' }}" required>
                    </div>
                    <div class="gw-form-group" style="display:flex;align-items:center;gap:14px;margin-top:24px;">
                        <label style="margin:0;">Status</label>
                        <label class="toggle-switch">
                            <input type="checkbox" name="status" value="1" {{ $steadfast && $steadfast->status ? 'checked' : '' }}>
                            <span class="toggle-slider" style="background:#26c6a6;"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div style="padding:0 24px 20px;">
                <button type="submit" class="btn-submit-teal">Submit</button>
            </div>
        </form>
    </div>

    {{-- ── PATHAO ── --}}
    <div class="gw-card" style="margin-bottom:20px;">
        <div class="gw-card-header">
            <h3>Pathao Courier — Add Settings</h3>
            <a href="{{ route('admin.settings.gateways') }}" class="btn-save" style="background:#6c757d;font-size:13px;padding:7px 16px;text-decoration:none;">← Back</a>
        </div>
        <form action="{{ route('admin.pathao.update') }}" method="POST">
            @csrf
            <div class="gw-card-body">
                <div class="gw-alert-info">
                    <span class="info-icon">ℹ</span>
                    Sandbox credentials দিয়ে আগে test করুন। Live এ যেতে base_url পরিবর্তন করুন এবং merchant account এর real credentials দিন।
                </div>
                <div class="gw-form-group">
                    <label>Base URL <span class="req">*</span></label>
                    <input type="text" name="base_url" class="gw-form-control"
                        value="{{ $pathao->base_url ?? 'https://courier-api-sandbox.pathao.com' }}" required>
                    <small class="pathao-hint">Sandbox: https://courier-api-sandbox.pathao.com &nbsp;|&nbsp; Live: https://api-hermes.pathao.com</small>
                </div>
                <div class="gateway-grid">
                    <div class="gw-form-group">
                        <label>Client ID <span class="req">*</span></label>
                        <input type="text" name="client_id" class="gw-form-control"
                            value="{{ $pathao->client_id ?? '' }}" required>
                        <small class="pathao-hint">Pathao merchant dashboard থেকে পাবেন</small>
                    </div>
                    <div class="gw-form-group">
                        <label>Client Secret <span class="req">*</span></label>
                        <input type="text" name="client_secret" class="gw-form-control"
                            value="{{ $pathao->client_secret ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Username <span class="req">*</span></label>
                        <input type="email" name="username" class="gw-form-control"
                            value="{{ $pathao->username ?? '' }}" placeholder="test@pathao.com" required>
                        <small class="pathao-hint">Pathao merchant account এর email</small>
                    </div>
                    <div class="gw-form-group">
                        <label>Password <span class="req">*</span></label>
                        <input type="password" name="password" class="gw-form-control"
                            value="{{ $pathao->password ?? '' }}" required>
                        <small class="pathao-hint">Pathao merchant account এর password</small>
                    </div>
                    <div class="gw-form-group">
                        <label>Grant Type <span class="req">*</span></label>
                        <input type="text" name="grant_type" class="gw-form-control"
                            value="{{ $pathao->grant_type ?? 'password' }}">
                        <small class="pathao-hint">সাধারণত password হয়</small>
                    </div>
                    <div class="gw-form-group" style="display:flex;align-items:center;gap:14px;margin-top:24px;">
                        <label style="margin:0;">Status</label>
                        <label class="toggle-switch">
                            <input type="checkbox" name="status" value="1" {{ $pathao && $pathao->status ? 'checked' : '' }}>
                            <span class="toggle-slider" style="background:#26c6a6;"></span>
                        </label>
                        <span style="font-size:13px;color:#444;">Active</span>
                    </div>
                </div>
            </div>
            <div style="padding:0 24px 20px;display:flex;gap:12px;">
                <button type="submit" class="btn-submit-teal">💾 Save Settings</button>
                <a href="{{ route('admin.settings.gateways') }}" class="btn-save" style="background:#6c757d;text-decoration:none;display:inline-flex;align-items:center;">✕ Cancel</a>
            </div>
        </form>
    </div>


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{--  BD PAYMENT SECTION                                       --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <span class="section-label">BD Payment Gateways</span>

    {{-- ── BKASH PAYMENT ── --}}
    <div class="gw-card" style="margin-bottom:20px;">
        <div class="gw-card-header">
            <h3>Bkash</h3>
        </div>
        <form action="{{ route('admin.bkash-pay.update') }}" method="POST">
            @csrf
            <div class="gw-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                    <div class="gw-form-group">
                        <label>User Name <span class="req">*</span></label>
                        <input type="text" name="username" class="gw-form-control"
                            value="{{ $bkashPay->username ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>App Key <span class="req">*</span></label>
                        <input type="text" name="app_key" class="gw-form-control"
                            value="{{ $bkashPay->app_key ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>App Secret <span class="req">*</span></label>
                        <input type="text" name="app_secret" class="gw-form-control"
                            value="{{ $bkashPay->app_secret ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Base Url <span class="req">*</span></label>
                        <input type="text" name="base_url" class="gw-form-control"
                            value="{{ $bkashPay->base_url ?? 'https://tokenized.pay.bka.sh/v1.2.0-beta' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Password <span class="req">*</span></label>
                        <input type="text" name="password" class="gw-form-control"
                            value="{{ $bkashPay->password ?? '' }}" required>
                    </div>
                    <div class="gw-form-group" style="display:flex;align-items:center;gap:12px;margin-top:24px;">
                        <label style="margin:0;">Status</label>
                        <label class="toggle-switch">
                            <input type="checkbox" name="status" value="1" {{ $bkashPay && $bkashPay->status ? 'checked' : '' }}>
                            <span class="toggle-slider" style="background:#26c6a6;"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div style="padding:0 24px 20px;">
                <button type="submit" class="btn-submit-teal">Submit</button>
            </div>
        </form>
    </div>

    {{-- ── SHURJOPAY ── --}}
    <div class="gw-card" style="margin-bottom:20px;">
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


    {{-- ══════════════════════════════════════════════════════════ --}}
    {{--  SMS GATEWAY                                              --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <span class="section-label">SMS Gateway</span>

    <div class="gw-card" style="margin-bottom:40px;">
        <div class="gw-card-header">
            <h3>SMS Gateway</h3>
        </div>
        <form action="{{ route('admin.sms.update') }}" method="POST">
            @csrf
            <div class="gw-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                    <div class="gw-form-group">
                        <label>Url <span class="req">*</span></label>
                        <input type="text" name="url" class="gw-form-control"
                            value="{{ $sms->url ?? 'https://msg.elitbuzz-bd.com/smsapi' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>API Key <span class="req">*</span></label>
                        <input type="text" name="api_key" class="gw-form-control"
                            value="{{ $sms->api_key ?? '' }}" placeholder="Your API Key">
                    </div>
                    <div class="gw-form-group">
                        <label>Senderid <span class="req">*</span></label>
                        <input type="text" name="sender_id" class="gw-form-control"
                            value="{{ $sms->sender_id ?? '' }}">
                    </div>
                </div>

                {{-- Toggle row --}}
                <div style="display:flex;gap:40px;align-items:center;margin-top:8px;flex-wrap:wrap;">
                    <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                        <span style="font-size:13px;color:#444;">Status</span>
                        <label class="toggle-switch">
                            <input type="checkbox" name="status" value="1" {{ $sms && $sms->status ? 'checked' : '' }}>
                            <span class="toggle-slider" style="background:#26c6a6;"></span>
                        </label>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                        <span style="font-size:13px;color:#444;">Order confirm</span>
                        <label class="toggle-switch">
                            <input type="checkbox" name="order_confirm" value="1" {{ $sms && $sms->order_confirm ? 'checked' : '' }}>
                            <span class="toggle-slider" style="background:#26c6a6;"></span>
                        </label>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                        <span style="font-size:13px;color:#444;">Forgot password</span>
                        <label class="toggle-switch">
                            <input type="checkbox" name="forgot_password" value="1" {{ $sms && $sms->forgot_password ? 'checked' : '' }}>
                            <span class="toggle-slider" style="background:#26c6a6;"></span>
                        </label>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                        <span style="font-size:13px;color:#444;">Password Generator</span>
                        <label class="toggle-switch">
                            <input type="checkbox" name="password_generator" value="1" {{ $sms && $sms->password_generator ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div style="padding:0 24px 20px;">
                <button type="submit" class="btn-submit-teal">Submit</button>
            </div>
        </form>
    </div>

</div>{{-- end gateway-page --}}

<script>
/* ─── File name display ─── */
function updateFileName(input, labelId) {
    var label = document.getElementById(labelId);
    if (label) {
        label.textContent = input.files.length ? input.files[0].name : 'No file chosen';
    }
}

/* ─── AJAX Toggle ─── */
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
        // update sibling label span
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
