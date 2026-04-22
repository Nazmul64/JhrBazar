@extends('admin.master')

@section('content')

<style>
    /* ── Page wrapper ─────────────────────────────────── */
    .otp-page-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.35rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 24px;
    }
    .otp-page-title i { font-size: 1.2rem; color: #1a1a2e; }

    /* ── Card ─────────────────────────────────────────── */
    .otp-card {
        background: #fff;
        border-radius: 14px;
        padding: 28px 30px 30px;
        box-shadow: 0 1px 6px rgba(0,0,0,.07);
    }

    /* ── Section heading inside card ─────────────────── */
    .otp-section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .95rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 20px;
    }
    .otp-section-title i { font-size: .9rem; }

    /* ── Two-column row ───────────────────────────────── */
    .otp-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 14px;
    }
    @media (max-width: 768px) { .otp-row { grid-template-columns: 1fr; } }

    /* ── Toggle item box ──────────────────────────────── */
    .otp-toggle-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8f9fc;
        border: 1px solid #eceef3;
        border-radius: 10px;
        padding: 14px 18px;
        font-size: .88rem;
        font-weight: 500;
        color: #3d3d55;
    }

    /* ── Radio group box ──────────────────────────────── */
    .otp-radio-box {
        background: #f8f9fc;
        border: 1px solid #eceef3;
        border-radius: 10px;
        padding: 14px 18px;
    }
    .otp-radio-box label.group-label {
        display: block;
        font-size: .83rem;
        font-weight: 600;
        color: #6c6c8a;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: .03em;
    }
    .otp-radio-options {
        display: flex;
        gap: 28px;
    }
    .otp-radio-options label {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: .88rem;
        font-weight: 500;
        color: #3d3d55;
        cursor: pointer;
    }
    .otp-radio-options input[type="radio"] {
        accent-color: #e91e7a;
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    /* ── Toggle switch (custom) ───────────────────────── */
    .toggle-switch { position: relative; display: inline-block; width: 46px; height: 25px; flex-shrink: 0; }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute; inset: 0;
        background: #ccc;
        border-radius: 25px;
        cursor: pointer;
        transition: background .25s;
    }
    .toggle-slider::before {
        content: '';
        position: absolute;
        width: 19px; height: 19px;
        left: 3px; top: 3px;
        background: #fff;
        border-radius: 50%;
        transition: transform .25s;
        box-shadow: 0 1px 4px rgba(0,0,0,.2);
    }
    .toggle-switch input:checked + .toggle-slider { background: #e91e7a; }
    .toggle-switch input:checked + .toggle-slider::before { transform: translateX(21px); }

    /* ── Divider ──────────────────────────────────────── */
    .otp-divider { border: none; border-top: 1px solid #eceef3; margin: 22px 0; }

    /* ── Phone Validation heading ─────────────────────── */
    .phone-val-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 16px;
    }

    /* ── Input field ──────────────────────────────────── */
    .otp-input-group label {
        display: block;
        font-size: .83rem;
        font-weight: 600;
        color: #6c6c8a;
        margin-bottom: 7px;
        text-transform: uppercase;
        letter-spacing: .03em;
    }
    .otp-input-group input[type="number"] {
        width: 100%;
        padding: 11px 14px;
        border: 1px solid #e0e3ec;
        border-radius: 8px;
        font-size: .92rem;
        color: #3d3d55;
        background: #fff;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        -moz-appearance: textfield;
    }
    .otp-input-group input[type="number"]:focus {
        border-color: #e91e7a;
        box-shadow: 0 0 0 3px rgba(233,30,122,.08);
    }
    .otp-input-group input[type="number"]::-webkit-inner-spin-button,
    .otp-input-group input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; }

    /* ── Save button ──────────────────────────────────── */
    .btn-save-update {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #e91e7a;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 11px 26px;
        font-size: .92rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s, transform .15s, box-shadow .2s;
        float: right;
        margin-top: 18px;
    }
    .btn-save-update:hover {
        background: #c9165f;
        box-shadow: 0 4px 14px rgba(233,30,122,.35);
        transform: translateY(-1px);
    }
    .btn-save-update:active { transform: translateY(0); }

    /* ── Alert messages ───────────────────────────────── */
    .otp-alert {
        padding: 12px 18px;
        border-radius: 8px;
        font-size: .88rem;
        font-weight: 500;
        margin-bottom: 18px;
    }
    .otp-alert.success { background: #e8f8f1; color: #1a7a4a; border: 1px solid #b2dfcc; }
    .otp-alert.error   { background: #fef0f4; color: #b91c5c; border: 1px solid #f5b8d0; }
</style>

<div class="container-fluid py-2">

    {{-- Page Title --}}
    <h4 class="otp-page-title">
        <i class="fas fa-lock"></i>
        Verification OTP Settings
    </h4>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="otp-alert success">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="otp-alert error">
            <i class="fas fa-exclamation-circle me-1"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('admin.verificationotpsettings.store') }}" method="POST" id="otpSettingsForm">
        @csrf

        <div class="otp-card">

            {{-- ═══════════════════════════════════════════
                 SECTION 1 : Verification
            ═══════════════════════════════════════════ --}}
            <div class="otp-section-title">
                <i class="fas fa-shield-alt"></i> Verification
            </div>

            {{-- Row 1 : two toggles --}}
            <div class="otp-row">
                {{-- Customer Registration OTP Verify --}}
                <div class="otp-toggle-box">
                    <span>Customer Registration OTP Verify</span>
                    <label class="toggle-switch">
                        <input
                            type="checkbox"
                            name="customer_registration_otp_verify"
                            id="customer_registration_otp_verify"
                            class="ajax-toggle"
                            data-field="customer_registration_otp_verify"
                            {{ $setting->customer_registration_otp_verify ? 'checked' : '' }}
                        >
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                {{-- Must Verify Account on Order Placement --}}
                <div class="otp-toggle-box">
                    <span>Must Verify Account on Order Placement</span>
                    <label class="toggle-switch">
                        <input
                            type="checkbox"
                            name="must_verify_account_on_order_placement"
                            id="must_verify_account_on_order_placement"
                            class="ajax-toggle"
                            data-field="must_verify_account_on_order_placement"
                            {{ $setting->must_verify_account_on_order_placement ? 'checked' : '' }}
                        >
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>

            {{-- Row 2 : two radio groups --}}
            <div class="otp-row">
                {{-- Register OTP Send Method --}}
                <div class="otp-radio-box">
                    <label class="group-label">Register OTP Send / Account Verify Method</label>
                    <div class="otp-radio-options">
                        <label>
                            <input
                                type="radio"
                                name="register_otp_send_method"
                                value="phone"
                                {{ $setting->register_otp_send_method === 'phone' ? 'checked' : '' }}
                            > Phone
                        </label>
                        <label>
                            <input
                                type="radio"
                                name="register_otp_send_method"
                                value="email"
                                {{ $setting->register_otp_send_method === 'email' ? 'checked' : '' }}
                            > Email
                        </label>
                    </div>
                </div>

                {{-- Forget Password OTP Send Method --}}
                <div class="otp-radio-box">
                    <label class="group-label">Forget Password OTP Send Method</label>
                    <div class="otp-radio-options">
                        <label>
                            <input
                                type="radio"
                                name="forget_password_otp_send_method"
                                value="phone"
                                {{ $setting->forget_password_otp_send_method === 'phone' ? 'checked' : '' }}
                            > Phone
                        </label>
                        <label>
                            <input
                                type="radio"
                                name="forget_password_otp_send_method"
                                value="email"
                                {{ $setting->forget_password_otp_send_method === 'email' ? 'checked' : '' }}
                            > Email
                        </label>
                    </div>
                </div>
            </div>

            <hr class="otp-divider">

            {{-- ═══════════════════════════════════════════
                 SECTION 2 : Phone Number Validation
            ═══════════════════════════════════════════ --}}
            <div class="phone-val-title">Phone Number Validation</div>

            {{-- Registration Phone Required toggle --}}
            <div class="otp-row" style="grid-template-columns: 1fr 1fr;">
                <div class="otp-toggle-box">
                    <span>Registration Phone Required</span>
                    <label class="toggle-switch">
                        <input
                            type="checkbox"
                            name="registration_phone_required"
                            id="registration_phone_required"
                            class="ajax-toggle"
                            data-field="registration_phone_required"
                            {{ $setting->registration_phone_required ? 'checked' : '' }}
                        >
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div></div>{{-- empty cell to keep single column left --}}
            </div>

            {{-- Min / Max phone length --}}
            <div class="otp-row" style="margin-top:16px;">
                <div class="otp-input-group">
                    <label for="min_phone_length">Minimum Length (without Country Code)</label>
                    <input
                        type="number"
                        id="min_phone_length"
                        name="min_phone_length"
                        value="{{ old('min_phone_length', $setting->min_phone_length) }}"
                        min="1"
                        max="20"
                        required
                    >
                </div>
                <div class="otp-input-group">
                    <label for="max_phone_length">Maximum Length (without Country Code)</label>
                    <input
                        type="number"
                        id="max_phone_length"
                        name="max_phone_length"
                        value="{{ old('max_phone_length', $setting->max_phone_length) }}"
                        min="1"
                        max="20"
                        required
                    >
                </div>
            </div>

            {{-- Save button --}}
            <div style="overflow: hidden; padding-bottom: 4px;">
                <button type="submit" class="btn-save-update">
                    <i class="fas fa-save"></i> Save And Update
                </button>
            </div>

        </div>{{-- /.otp-card --}}
    </form>

</div>{{-- /.container-fluid --}}

{{-- ── AJAX toggle script ──────────────────────────── --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggles = document.querySelectorAll('.ajax-toggle');

    toggles.forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            const field = this.dataset.field;
            const checkbox = this;

            fetch("{{ route('admin.verificationotpsettings.toggle', 1) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ field: field }),
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (!data.success) {
                    // Revert on failure
                    checkbox.checked = !checkbox.checked;
                    alert('Something went wrong. Please try again.');
                }
            })
            .catch(function () {
                checkbox.checked = !checkbox.checked;
                alert('Network error. Please try again.');
            });
        });
    });
});
</script>

@endsection
