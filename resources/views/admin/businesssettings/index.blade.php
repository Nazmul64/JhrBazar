@extends('admin.master')

@section('content')

<style>
/* ── Page ─────────────────────────────────────────── */
.bs-page { padding: 24px; background: #f4f5f7; min-height: 100vh; }
.bs-title { font-size: 1.25rem; font-weight: 700; color: #1a1a2e; display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }

/* ── Tabs ─────────────────────────────────────────── */
.bs-tabs { display: flex; gap: 8px; margin-bottom: 24px; }
.bs-tab {
    padding: 8px 22px; border-radius: 8px; border: 1.5px solid #dee2e6;
    background: #fff; color: #495057; font-size: .875rem; font-weight: 500;
    cursor: pointer; transition: all .2s;
}
.bs-tab.active { background: #f84b7a; border-color: #f84b7a; color: #fff; }
.bs-tab:hover:not(.active) { border-color: #f84b7a; color: #f84b7a; }

/* ── Tab Panes ────────────────────────────────────── */
.bs-pane { display: none; }
.bs-pane.show { display: block; }

/* ── Section Card ─────────────────────────────────── */
.bs-card {
    background: #fff; border-radius: 12px; border: 1px solid #e9ecef;
    margin-bottom: 20px; overflow: hidden;
}
.bs-card-header {
    padding: 14px 20px; border-bottom: 1px solid #e9ecef;
    font-size: .875rem; font-weight: 600; color: #343a40;
    display: flex; align-items: center; gap: 8px;
}
.bs-card-body { padding: 24px 20px; }

/* ── Form Grid ────────────────────────────────────── */
.form-grid-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 20px; }
.form-grid-2 { display: grid; grid-template-columns: repeat(2,1fr); gap: 20px; }
.form-group label { font-size: .8125rem; font-weight: 500; color: #495057; margin-bottom: 6px; display: block; }
.form-control-bs {
    width: 100%; padding: 9px 13px; border: 1.5px solid #dee2e6; border-radius: 8px;
    font-size: .875rem; color: #212529; background: #fff; outline: none; transition: border .2s;
}
.form-control-bs:focus { border-color: #f84b7a; }
.input-group-bs { display: flex; }
.input-group-bs .form-control-bs { border-right: none; border-radius: 8px 0 0 8px; }
.input-group-bs .input-addon {
    padding: 9px 13px; background: #f8f9fa; border: 1.5px solid #dee2e6;
    border-left: none; border-radius: 0 8px 8px 0; font-size: .875rem; color: #6c757d;
}

/* ── Radio Group ──────────────────────────────────── */
.radio-group { display: flex; gap: 24px; align-items: center; padding-top: 4px; }
.radio-label { display: flex; align-items: center; gap: 7px; font-size: .875rem; color: #495057; cursor: pointer; }
.radio-label input[type=radio] { accent-color: #f84b7a; width: 16px; height: 16px; cursor: pointer; }

/* ── Toggle Switch ────────────────────────────────── */
.toggle-wrap { position: relative; display: inline-block; width: 44px; height: 24px; }
.toggle-wrap input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
    position: absolute; inset: 0; border-radius: 24px;
    background: #ced4da; cursor: pointer; transition: .3s;
}
.toggle-slider:before {
    content: ''; position: absolute; width: 18px; height: 18px; border-radius: 50%;
    background: #fff; left: 3px; bottom: 3px; transition: .3s;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.toggle-wrap input:checked + .toggle-slider { background: #f84b7a; }
.toggle-wrap input:checked + .toggle-slider:before { transform: translateX(20px); }

/* ── Payment / Commission Cards ───────────────────── */
.method-cards { display: flex; gap: 16px; flex-wrap: wrap; }
.method-card {
    border: 1.5px solid #dee2e6; border-radius: 10px; padding: 16px 20px;
    min-width: 220px; position: relative; background: #fff; transition: border .2s;
}
.method-card.checked { border-color: #28a745; }
.method-card-check {
    position: absolute; top: 10px; right: 10px;
    width: 20px; height: 20px; background: #28a745; border-radius: 4px;
    display: flex; align-items: center; justify-content: center; opacity: 0;
}
.method-card.checked .method-card-check { opacity: 1; }
.method-card-icon { font-size: 1.75rem; margin-bottom: 6px; }
.method-card-name { font-size: .9rem; font-weight: 600; color: #212529; margin-bottom: 10px; }
.method-enable { font-size: .8125rem; color: #6c757d; display: flex; align-items: center; gap: 8px; }

/* ── Checkbox ─────────────────────────────────────── */
.check-label { display: flex; align-items: center; gap: 8px; font-size: .875rem; color: #495057; cursor: pointer; }
.check-label input[type=checkbox] { accent-color: #0d6efd; width: 16px; height: 16px; cursor: pointer; }
.check-hint { font-size: .8rem; color: #0d6efd; margin-left: 4px; }

/* ── Withdraw Notes ───────────────────────────────── */
.withdraw-notes { font-size: .875rem; color: #343a40; line-height: 1.7; }
.withdraw-notes p { font-weight: 700; margin-bottom: 4px; }
.withdraw-notes ul { margin-left: 20px; margin-bottom: 14px; color: #495057; }

/* ── Save Button ──────────────────────────────────── */
.save-bar { display: flex; justify-content: flex-end; padding: 16px 0 4px; }
.btn-save {
    background: #f84b7a; color: #fff; border: none; border-radius: 8px;
    padding: 11px 32px; font-size: .9rem; font-weight: 600; cursor: pointer; transition: background .2s;
}
.btn-save:hover { background: #e0305e; }

/* ── Responsive ───────────────────────────────────── */
@media(max-width:768px) {
    .form-grid-3,.form-grid-2 { grid-template-columns: 1fr; }
    .method-cards { flex-direction: column; }
}
</style>

<div class="bs-page">

    {{-- Page Title --}}
    <div class="bs-title">
        <i class="bi bi-bar-chart-steps"></i> Business Settings
    </div>

    {{-- ── Tabs ──────────────────────────────────── --}}
    <div class="bs-tabs">
        <button class="bs-tab active" data-tab="basic-info">Basic Info</button>
        <button class="bs-tab"        data-tab="shops">Shops</button>
        <button class="bs-tab"        data-tab="withdraw">Withdraw</button>
    </div>

    @php $setting = $setting ?? null; @endphp

    <form method="POST"
          action="{{ $setting ? route('admin.businesssettings.update', $setting->id) : route('admin.businesssettings.store') }}">
        @csrf
        @if($setting) @method('PUT') @endif

        {{-- ════════════ TAB: Basic Info ════════════ --}}
        <div class="bs-pane show" id="tab-basic-info">

            {{-- Business Information --}}
            <div class="bs-card">
                <div class="bs-card-header">
                    <i class="bi bi-briefcase"></i> Business Information
                </div>
                <div class="bs-card-body">

                    <div class="form-grid-3" style="margin-bottom:20px;">
                        <div class="form-group">
                            <label>Company Name</label>
                            <input type="text" name="company_name" class="form-control-bs"
                                   placeholder="Enter Company Name / Business Name"
                                   value="{{ old('company_name', $setting?->company_name) }}">
                        </div>
                        <div class="form-group">
                            <label>Company Email</label>
                            <input type="email" name="company_email" class="form-control-bs"
                                   placeholder="support@example.com"
                                   value="{{ old('company_email', $setting?->company_email) }}">
                        </div>
                        <div class="form-group">
                            <label>Company Phone</label>
                            <input type="text" name="company_phone" class="form-control-bs"
                                   placeholder="01711257498"
                                   value="{{ old('company_phone', $setting?->company_phone) }}">
                        </div>
                    </div>

                    <div class="form-grid-3" style="margin-bottom:20px;">
                        <div class="form-group">
                            <label>Business Model</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="business_model" value="single_shop"
                                        {{ old('business_model', $setting?->business_model) === 'single_shop' ? 'checked' : '' }}>
                                    Single Shop
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="business_model" value="multi_shop"
                                        {{ old('business_model', $setting?->business_model ?? 'multi_shop') === 'multi_shop' ? 'checked' : '' }}>
                                    Multi Shop
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Currency Position</label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="currency_position" value="left"
                                        {{ old('currency_position', $setting?->currency_position ?? 'left') === 'left' ? 'checked' : '' }}>
                                    ($) Left
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="currency_position" value="right"
                                        {{ old('currency_position', $setting?->currency_position) === 'right' ? 'checked' : '' }}>
                                    Right ($)
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Time Zone</label>
                            <select name="timezone" class="form-control-bs">
                                @php
                                    $zones = [
                                        'UTC/GMT +00:00 - UTC',
                                        'UTC/GMT +05:30 - Asia/Kolkata',
                                        'UTC/GMT +06:00 - Asia/Dhaka',
                                        'UTC/GMT +07:00 - Asia/Bangkok',
                                        'UTC/GMT +08:00 - Asia/Singapore',
                                        'UTC/GMT -05:00 - America/New_York',
                                        'UTC/GMT -08:00 - America/Los_Angeles',
                                    ];
                                    $current = old('timezone', $setting?->timezone ?? 'UTC/GMT +06:00 - Asia/Dhaka');
                                @endphp
                                @foreach($zones as $zone)
                                    <option value="{{ $zone }}" {{ $current === $zone ? 'selected' : '' }}>
                                        {{ $zone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="max-width:340px;">
                        <div class="form-group">
                            <label>Return Order Within Days</label>
                            <input type="number" name="return_order_within_days" class="form-control-bs"
                                   min="0" value="{{ old('return_order_within_days', $setting?->return_order_within_days ?? 3) }}">
                        </div>
                    </div>

                </div>
            </div>

            {{-- Payment Method Setup --}}
            <div class="bs-card">
                <div class="bs-card-header">
                    <i class="bi bi-credit-card-2-front"></i> Payment Method Setup
                </div>
                <div class="bs-card-body">
                    <div class="method-cards">

                        {{-- Cash on Delivery --}}
                        <div class="method-card {{ ($setting?->cash_on_delivery ?? 1) ? 'checked' : '' }}"
                             id="card-cod">
                            <div class="method-card-check">
                                <i class="bi bi-check" style="color:#fff;font-size:.75rem;"></i>
                            </div>
                            <div class="method-card-icon">💰</div>
                            <div class="method-card-name">Cash on Delivery</div>
                            <div class="method-enable">
                                Enable
                                <label class="toggle-wrap">
                                    <input type="checkbox" name="cash_on_delivery" id="tog-cod"
                                           {{ ($setting?->cash_on_delivery ?? 1) ? 'checked' : '' }}
                                           onchange="syncCard(this,'card-cod')">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        {{-- Online Payment --}}
                        <div class="method-card {{ ($setting?->online_payment ?? 1) ? 'checked' : '' }}"
                             id="card-op">
                            <div class="method-card-check">
                                <i class="bi bi-check" style="color:#fff;font-size:.75rem;"></i>
                            </div>
                            <div class="method-card-icon">💳</div>
                            <div class="method-card-name">Online Payment</div>
                            <div class="method-enable">
                                Enable
                                <label class="toggle-wrap">
                                    <input type="checkbox" name="online_payment" id="tog-op"
                                           {{ ($setting?->online_payment ?? 1) ? 'checked' : '' }}
                                           onchange="syncCard(this,'card-op')">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>{{-- /tab-basic-info --}}

        {{-- ════════════ TAB: Shops ════════════ --}}
        <div class="bs-pane" id="tab-shops">

            <div class="bs-card">
                <div class="bs-card-header">
                    <i class="bi bi-shop"></i> Shop Setup
                </div>
                <div class="bs-card-body">

                    {{-- Commission & Subscription cards --}}
                    <div class="method-cards" style="margin-bottom:24px;">

                        <div class="method-card {{ ($setting?->commission_enabled ?? 1) ? 'checked' : '' }}"
                             id="card-comm">
                            <div class="method-card-check">
                                <i class="bi bi-check" style="color:#fff;font-size:.75rem;"></i>
                            </div>
                            <div class="method-card-icon">💰</div>
                            <div class="method-card-name">Commission</div>
                            <div class="method-enable">
                                Enable
                                <label class="toggle-wrap">
                                    <input type="checkbox" name="commission_enabled"
                                           {{ ($setting?->commission_enabled ?? 1) ? 'checked' : '' }}
                                           onchange="syncCard(this,'card-comm')">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="method-card {{ ($setting?->subscription_enabled ?? 0) ? 'checked' : '' }}"
                             id="card-sub">
                            <div class="method-card-check">
                                <i class="bi bi-check" style="color:#fff;font-size:.75rem;"></i>
                            </div>
                            <div class="method-card-icon">💳</div>
                            <div class="method-card-name">Subscription</div>
                            <div class="method-enable">
                                {{ ($setting?->subscription_enabled ?? 0) ? 'Enable' : 'Disable' }}
                                <label class="toggle-wrap">
                                    <input type="checkbox" name="subscription_enabled"
                                           {{ ($setting?->subscription_enabled ?? 0) ? 'checked' : '' }}
                                           onchange="syncCard(this,'card-sub')">
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                    </div>

                    {{-- Commission fields --}}
                    <div class="form-grid-3" style="margin-bottom:20px;">
                        <div class="form-group">
                            <label>Commission</label>
                            <div class="input-group-bs">
                                <input type="number" name="commission" class="form-control-bs"
                                       step="0.01" min="0"
                                       value="{{ old('commission', $setting?->commission ?? 10) }}">
                                <span class="input-addon">$</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Commission Type</label>
                            <select name="commission_type" class="form-control-bs">
                                <option value="fixed"
                                    {{ old('commission_type', $setting?->commission_type ?? 'fixed') === 'fixed' ? 'selected' : '' }}>
                                    Fixed Amount
                                </option>
                                <option value="percentage"
                                    {{ old('commission_type', $setting?->commission_type) === 'percentage' ? 'selected' : '' }}>
                                    Percentage
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Commission Charge</label>
                            <select name="commission_charge" class="form-control-bs">
                                <option value="per_order"
                                    {{ old('commission_charge', $setting?->commission_charge ?? 'per_order') === 'per_order' ? 'selected' : '' }}>
                                    Per Order
                                </option>
                                <option value="per_item"
                                    {{ old('commission_charge', $setting?->commission_charge) === 'per_item' ? 'selected' : '' }}>
                                    Per Item
                                </option>
                            </select>
                        </div>
                    </div>

                    {{-- POS & Shop Registration toggles --}}
                    <div class="form-grid-2">
                        <div style="display:flex;align-items:center;gap:16px;">
                            <span style="font-size:.875rem;color:#495057;">Enable POS in Shop Panel</span>
                            <label class="toggle-wrap">
                                <input type="checkbox" name="pos_in_shop_panel"
                                       {{ ($setting?->pos_in_shop_panel ?? 1) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div style="display:flex;align-items:center;gap:16px;">
                            <span style="font-size:.875rem;color:#495057;">Shop Registration</span>
                            <label class="toggle-wrap">
                                <input type="checkbox" name="shop_registration"
                                       {{ ($setting?->shop_registration ?? 1) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Need Product Approval --}}
            <div class="bs-card">
                <div class="bs-card-header">
                    <i class="bi bi-box-seam"></i> Need Product Approval
                </div>
                <div class="bs-card-body" style="display:flex;gap:28px;flex-wrap:wrap;">
                    <label class="check-label">
                        <input type="checkbox" name="need_product_approval"
                               {{ ($setting?->need_product_approval ?? 1) ? 'checked' : '' }}>
                        Need Product Approval
                    </label>
                    <label class="check-label">
                        <input type="checkbox" name="update_product_approval"
                               {{ ($setting?->update_product_approval ?? 1) ? 'checked' : '' }}>
                        Update Product Approval
                        <span class="check-hint">(when shop update any filed of product it will be needed to approve)</span>
                    </label>
                </div>
            </div>

        </div>{{-- /tab-shops --}}

        {{-- ════════════ TAB: Withdraw ════════════ --}}
        <div class="bs-pane" id="tab-withdraw">

            <div class="bs-card">
                <div class="bs-card-header">
                    <i class="bi bi-wallet2"></i> Withdraw Setup
                </div>
                <div class="bs-card-body">
                    <div class="form-grid-3">
                        <div class="form-group">
                            <label>Min Withdraw Amount</label>
                            <input type="number" name="min_withdraw_amount" class="form-control-bs"
                                   step="0.01" min="0"
                                   value="{{ old('min_withdraw_amount', $setting?->min_withdraw_amount ?? 0) }}">
                        </div>
                        <div class="form-group">
                            <label>Max Withdraw Amount</label>
                            <input type="number" name="max_withdraw_amount" class="form-control-bs"
                                   step="0.01" min="0"
                                   value="{{ old('max_withdraw_amount', $setting?->max_withdraw_amount) }}">
                        </div>
                        <div class="form-group">
                            <label>Min Day Withdraw Request</label>
                            <div class="input-group-bs">
                                <input type="number" name="min_day_withdraw_request" class="form-control-bs"
                                       placeholder="Enter min day" min="0"
                                       value="{{ old('min_day_withdraw_request', $setting?->min_day_withdraw_request) }}">
                                <span class="input-addon">Days</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Withdrawal Notes --}}
            <div class="bs-card">
                <div class="bs-card-header">
                    <i class="bi bi-wallet2"></i> Withdrawal Notes
                </div>
                <div class="bs-card-body">
                    <div class="withdraw-notes">
                        <p>Minimum Withdrawal Amount:</p>
                        <ul>
                            <li>Enter the minimum amount that can be withdrawn. This value must be a numerical figure.</li>
                            <li>Example: If the minimum withdrawal amount is set to $10, users cannot withdraw any amount less than $10.</li>
                        </ul>
                        <p>Maximum Withdrawal Amount:</p>
                        <ul>
                            <li>Enter the maximum amount that can be withdrawn at a time. This value must be a numerical figure.</li>
                            <li>Example: If the maximum withdrawal amount is set to $1,000, users cannot withdraw more than $1,000 in a single transaction.</li>
                        </ul>
                        <p>Minimum Days Between Withdrawal Requests:</p>
                        <ul>
                            <li>Specify the minimum number of days required between withdrawal requests. This value should be an integer.</li>
                            <li>Example: If set to 7 days, after a seller sends a withdrawal request, they must wait at least 7 days before sending another request.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>{{-- /tab-withdraw --}}

        {{-- Save Button --}}
        <div class="save-bar">
            <button type="submit" class="btn-save">Save And Update</button>
        </div>

    </form>
</div>

{{-- ── JavaScript ────────────────────────────────── --}}
<script>
(function(){
    // Tab switching
    document.querySelectorAll('.bs-tab').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.bs-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.bs-pane').forEach(p => p.classList.remove('show'));
            this.classList.add('active');
            document.getElementById('tab-' + this.dataset.tab).classList.add('show');
        });
    });

    // Sync green border on method cards
    window.syncCard = function(toggle, cardId){
        var card = document.getElementById(cardId);
        if(!card) return;
        if(toggle.checked){
            card.classList.add('checked');
        } else {
            card.classList.remove('checked');
        }
    };
})();
</script>

@endsection
