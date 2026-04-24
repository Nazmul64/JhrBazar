@extends('admin.master')

@section('content')

<style>
    /* ===== EDIT PROMO CODE PAGE STYLES ===== */
    .promo-form-wrapper {
        padding: 24px;
        background: #f3f4f6;
        min-height: 100vh;
    }

    /* Page Header */
    .promo-form-page-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
    }

    .promo-form-page-header svg {
        width: 20px;
        height: 20px;
        color: #374151;
    }

    .promo-form-page-title {
        font-size: 18px;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }

    /* Form Card */
    .promo-form-card {
        background: #fff;
        border-radius: 10px;
        padding: 28px 32px 32px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        max-width: 100%;
    }

    .promo-form-card-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f3f4f6;
        margin-bottom: 24px;
    }

    .promo-form-card-header svg {
        width: 20px;
        height: 20px;
        color: #374151;
    }

    .promo-form-card-title {
        font-size: 17px;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }

    /* Form Groups */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 13.5px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
    }

    .form-label .required {
        color: #ef4444;
        margin-left: 2px;
    }

    .form-control {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 10px 14px;
        font-size: 14px;
        color: #111827;
        background: #fff;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }

    .form-control:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
        padding-right: 36px;
        cursor: pointer;
    }

    /* Select shops (tag style) */
    .shops-select-container {
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 8px 12px;
        min-height: 44px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
        cursor: text;
        background: #fff;
        transition: border-color 0.2s;
    }

    .shops-select-container:focus-within {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .shop-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f3f4f6;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        padding: 3px 9px;
        font-size: 13px;
        color: #374151;
    }

    .shop-tag-remove {
        cursor: pointer;
        color: #9ca3af;
        font-size: 14px;
        line-height: 1;
        padding: 0;
        background: none;
        border: none;
        transition: color 0.15s;
        font-weight: 600;
    }

    .shop-tag-remove:hover {
        color: #ef4444;
    }

    .shops-input {
        border: none;
        outline: none;
        flex: 1;
        min-width: 100px;
        font-size: 14px;
        color: #111827;
        background: transparent;
    }

    .shops-input::placeholder {
        color: #9ca3af;
    }

    /* Two column grid */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    /* Time input icon */
    .time-input-wrapper {
        position: relative;
    }

    .time-input-wrapper .form-control {
        padding-right: 40px;
    }

    .time-input-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
    }

    .time-input-icon svg {
        width: 18px;
        height: 18px;
    }

    /* Form Footer */
    .promo-form-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 32px;
        padding-top: 20px;
        border-top: 1px solid #f3f4f6;
    }

    .btn-cancel {
        padding: 10px 24px;
        border: 1px solid #d1d5db;
        background: #fff;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.2s;
        display: inline-flex;
        align-items: center;
    }

    .btn-cancel:hover {
        background: #f3f4f6;
        text-decoration: none;
        color: #374151;
    }

    .btn-update {
        padding: 10px 36px;
        background: #ef4444;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        color: #fff;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-update:hover {
        background: #dc2626;
    }

    /* Error messages */
    .text-danger {
        color: #ef4444;
        font-size: 12px;
        margin-top: 4px;
        display: block;
    }

    @media (max-width: 640px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="promo-form-wrapper">

    {{-- Page Header --}}
    <div class="promo-form-page-header">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        <h2 class="promo-form-page-title">Edit Promo Code</h2>
    </div>

    {{-- Form Card --}}
    <div class="promo-form-card">

        <div class="promo-form-card-header">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            <h3 class="promo-form-card-title">Edit Promo Code</h3>
        </div>

        <form action="{{ route('admin.promocode.update', $promocode->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Select Shops --}}
            <div class="form-group">
                <label class="form-label">Select shops</label>
                <div class="shops-select-container" id="shopsContainer">
                    {{-- Existing shop tags --}}
                    @php
                        $existingShops = $promocode->shop_ids_array ?? [];
                    @endphp
                    @foreach($existingShops as $shopId)
                        <span class="shop-tag" id="shop_tag_{{ $shopId }}">
                            <button type="button" class="shop-tag-remove" onclick="removeShopTag('{{ $shopId }}')">×</button>
                            {{ $shopId }}
                        </span>
                    @endforeach
                    <input
                        type="text"
                        class="shops-input"
                        id="shopsInput"
                        placeholder=""
                    >
                </div>
                {{-- Hidden inputs --}}
                <div id="shopsHiddenInputs">
                    @foreach($existingShops as $shopId)
                        <input type="hidden" name="shop_ids[]" value="{{ $shopId }}" id="shop_hidden_{{ $shopId }}">
                    @endforeach
                </div>
            </div>

            {{-- Voucher Code & Discount Type --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Voucher Code <span class="required">*</span></label>
                    <input
                        type="text"
                        name="coupon_code"
                        class="form-control"
                        value="{{ old('coupon_code', $promocode->coupon_code) }}"
                    >
                    @error('coupon_code')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Discount Type <span class="required">*</span></label>
                    <select name="discount_type" class="form-control">
                        <option value="amount"
                            {{ old('discount_type', $promocode->discount_type) == 'amount' ? 'selected' : '' }}>
                            Amount
                        </option>
                        <option value="percentage"
                            {{ old('discount_type', $promocode->discount_type) == 'percentage' ? 'selected' : '' }}>
                            Percentage
                        </option>
                    </select>
                    @error('discount_type')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Discount & Minimum Order Amount --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Discount <span class="required">*</span></label>
                    <input
                        type="number"
                        name="discount"
                        step="0.01"
                        class="form-control"
                        value="{{ old('discount', $promocode->discount) }}"
                    >
                    @error('discount')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Minimum Order Amount <span class="required">*</span></label>
                    <input
                        type="number"
                        name="minimum_order_amount"
                        step="0.01"
                        class="form-control"
                        value="{{ old('minimum_order_amount', $promocode->minimum_order_amount) }}"
                    >
                    @error('minimum_order_amount')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Limit For Single User & Maximum Discount Amount --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Limit For Single User</label>
                    <input
                        type="number"
                        name="limit_for_single_user"
                        class="form-control"
                        placeholder="exm: 5"
                        value="{{ old('limit_for_single_user', $promocode->limit_for_single_user) }}"
                    >
                    @error('limit_for_single_user')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Maximum Discount Amount</label>
                    <input
                        type="number"
                        name="maximum_discount_amount"
                        step="0.01"
                        class="form-control"
                        placeholder="exm: $300"
                        value="{{ old('maximum_discount_amount', $promocode->maximum_discount_amount) }}"
                    >
                    @error('maximum_discount_amount')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Start Date & Start Time --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Start Date <span class="required">*</span></label>
                    <input
                        type="date"
                        name="start_date"
                        class="form-control"
                        value="{{ old('start_date', $promocode->start_date ? $promocode->start_date->format('Y-m-d') : '') }}"
                    >
                    @error('start_date')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Start Time <span class="required">*</span></label>
                    <div class="time-input-wrapper">
                        <input
                            type="time"
                            name="start_time"
                            class="form-control"
                            value="{{ old('start_time', $promocode->start_time) }}"
                        >
                        <span class="time-input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </span>
                    </div>
                    @error('start_time')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Expired Date & Expired Time --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Expired Date <span class="required">*</span></label>
                    <input
                        type="date"
                        name="expired_date"
                        class="form-control"
                        value="{{ old('expired_date', $promocode->expired_date ? $promocode->expired_date->format('Y-m-d') : '') }}"
                    >
                    @error('expired_date')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Expired Time <span class="required">*</span></label>
                    <div class="time-input-wrapper">
                        <input
                            type="time"
                            name="expired_time"
                            class="form-control"
                            value="{{ old('expired_time', $promocode->expired_time) }}"
                        >
                        <span class="time-input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </span>
                    </div>
                    @error('expired_time')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Form Footer --}}
            <div class="promo-form-footer">
                <a href="{{ route('admin.promocode.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-update">Update</button>
            </div>

        </form>
    </div>
</div>

<script>
    // ===== Edit page shops tag input =====
    var selectedShops = @json($promocode->shop_ids_array ?? []);

    var shopsInput = document.getElementById('shopsInput');
    var shopsContainer = document.getElementById('shopsContainer');
    var shopsHiddenInputs = document.getElementById('shopsHiddenInputs');

    shopsInput.addEventListener('keydown', function(e) {
        if ((e.key === 'Enter' || e.key === ',') && this.value.trim()) {
            e.preventDefault();
            addShopTag(this.value.trim());
            this.value = '';
        }
    });

    function addShopTag(name) {
        if (selectedShops.includes(name)) return;
        selectedShops.push(name);

        var tag = document.createElement('span');
        tag.className = 'shop-tag';
        tag.id = 'shop_tag_' + name;
        tag.innerHTML = '<button type="button" class="shop-tag-remove" onclick="removeShopTag(\'' + name + '\')">×</button> ' + name;
        shopsContainer.insertBefore(tag, shopsInput);

        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'shop_ids[]';
        hidden.value = name;
        hidden.id = 'shop_hidden_' + name;
        shopsHiddenInputs.appendChild(hidden);
    }

    function removeShopTag(name) {
        selectedShops = selectedShops.filter(function(s) { return s !== name; });
        var tag = document.getElementById('shop_tag_' + name);
        if (tag) tag.remove();
        var hidden = document.getElementById('shop_hidden_' + name);
        if (hidden) hidden.remove();
    }
</script>

@endsection
