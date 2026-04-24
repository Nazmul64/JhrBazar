@extends('admin.master')

@section('content')

<style>
    /* ===== PAGE WRAPPER ===== */
    .promo-form-wrapper {
        padding: 24px;
        background: #f3f4f6;
        min-height: 100vh;
    }

    .promo-form-page-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
    }

    .promo-form-page-header svg { width: 20px; height: 20px; color: #374151; }

    .promo-form-page-title {
        font-size: 18px;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }

    /* ===== CARD ===== */
    .promo-form-card {
        background: #fff;
        border-radius: 10px;
        padding: 28px 32px 32px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }

    .promo-form-card-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f3f4f6;
        margin-bottom: 24px;
    }

    .promo-form-card-header svg { width: 20px; height: 20px; color: #374151; }

    .promo-form-card-title {
        font-size: 17px;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }

    /* ===== FORM ELEMENTS ===== */
    .form-group { margin-bottom: 20px; }

    .form-label {
        display: block;
        font-size: 13.5px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
    }

    .form-label .req { color: #ef4444; margin-left: 2px; }

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
        box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
    }

    .form-control::placeholder { color: #9ca3af; }

    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px;
        padding-right: 36px;
        cursor: pointer;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    /* ===== CUSTOM SHOP DROPDOWN ===== */
    .shop-dropdown-wrapper {
        position: relative;
        user-select: none;
    }

    .shop-selected-box {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 8px 14px;
        min-height: 44px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
        cursor: text;
        background: #fff;
        box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .shop-selected-box.open {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
        border-radius: 6px 6px 0 0;
    }

    /* Selected tag inside box */
    .shop-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f3f4f6;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        padding: 3px 8px;
        font-size: 13px;
        color: #374151;
        white-space: nowrap;
    }

    .shop-tag-remove {
        cursor: pointer;
        color: #9ca3af;
        font-weight: 700;
        font-size: 14px;
        line-height: 1;
        border: none;
        background: none;
        padding: 0 1px;
        transition: color 0.15s;
    }

    .shop-tag-remove:hover { color: #ef4444; }

    /* Search input inside box */
    .shop-search-input {
        border: none;
        outline: none;
        flex: 1;
        min-width: 100px;
        font-size: 14px;
        color: #111827;
        background: transparent;
        padding: 2px 0;
    }

    .shop-search-input::placeholder { color: #9ca3af; }

    /* Dropdown list */
    .shop-dropdown-list {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #d1d5db;
        border-top: none;
        border-radius: 0 0 6px 6px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        z-index: 999;
        max-height: 220px;
        overflow-y: auto;
    }

    .shop-dropdown-list.open { display: block; }

    .shop-dropdown-item {
        padding: 10px 14px;
        font-size: 14px;
        color: #374151;
        cursor: pointer;
        transition: background 0.15s;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .shop-dropdown-item:hover { background: #f9fafb; }

    .shop-dropdown-item.selected {
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 500;
    }

    .shop-dropdown-item.highlighted { background: #dbeafe; }

    .shop-dropdown-empty {
        padding: 12px 14px;
        font-size: 13px;
        color: #9ca3af;
        text-align: center;
    }

    /* Checkmark icon in dropdown */
    .shop-item-check {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        color: #3b82f6;
        visibility: hidden;
    }

    .shop-dropdown-item.selected .shop-item-check { visibility: visible; }

    /* ===== CUSTOM TIME DROPDOWN ===== */
    .time-dropdown-wrapper {
        position: relative;
        user-select: none;
    }

    .time-selected-box {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 10px 40px 10px 14px;
        font-size: 14px;
        color: #111827;
        background: #fff;
        cursor: pointer;
        box-sizing: border-box;
        transition: border-color 0.2s, box-shadow 0.2s;
        display: flex;
        align-items: center;
        min-height: 44px;
    }

    .time-selected-box.placeholder-shown { color: #9ca3af; }

    .time-selected-box.open {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
        border-radius: 6px 6px 0 0;
    }

    .time-clock-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
    }

    .time-clock-icon svg { width: 18px; height: 18px; display: block; }

    .time-dropdown-list {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #d1d5db;
        border-top: none;
        border-radius: 0 0 6px 6px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        z-index: 998;
        max-height: 200px;
        overflow-y: auto;
    }

    .time-dropdown-list.open { display: block; }

    .time-dropdown-item {
        padding: 9px 14px;
        font-size: 14px;
        color: #374151;
        cursor: pointer;
        transition: background 0.12s;
    }

    .time-dropdown-item:hover { background: #f9fafb; }
    .time-dropdown-item.selected { background: #eff6ff; color: #1d4ed8; font-weight: 500; }

    /* Scrollbar for dropdowns */
    .shop-dropdown-list::-webkit-scrollbar,
    .time-dropdown-list::-webkit-scrollbar { width: 4px; }

    .shop-dropdown-list::-webkit-scrollbar-thumb,
    .time-dropdown-list::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
    }

    /* ===== FOOTER ===== */
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
        display: inline-flex;
        align-items: center;
        transition: background 0.2s;
    }

    .btn-cancel:hover { background: #f3f4f6; color: #374151; text-decoration: none; }

    .btn-submit {
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

    .btn-submit:hover { background: #dc2626; }

    .text-danger { color: #ef4444; font-size: 12px; margin-top: 4px; display: block; }

    @media (max-width: 640px) { .form-row { grid-template-columns: 1fr; } }
</style>

<div class="promo-form-wrapper">

    {{-- Page Header --}}
    <div class="promo-form-page-header">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="16"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        <h2 class="promo-form-page-title">Add New Promo Code</h2>
    </div>

    <div class="promo-form-card">

        <div class="promo-form-card-header">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="16"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            <h3 class="promo-form-card-title">Add New Promo Code</h3>
        </div>

        <form action="{{ route('admin.promocode.store') }}" method="POST" id="promoForm">
            @csrf

            {{-- ===== SELECT SHOPS (Searchable Multi-select Dropdown) ===== --}}
            <div class="form-group">
                <label class="form-label">Select shops</label>

                <div class="shop-dropdown-wrapper" id="shopDropdown">
                    {{-- Selected tags + search input --}}
                    <div class="shop-selected-box" id="shopSelectedBox" onclick="toggleShopDropdown(event)">
                        <input
                            type="text"
                            class="shop-search-input"
                            id="shopSearchInput"
                            placeholder="Select shops"
                            autocomplete="off"
                        >
                    </div>

                    {{-- Dropdown list --}}
                    <div class="shop-dropdown-list" id="shopDropdownList">
                        @forelse($shops as $shop)
                            <div
                                class="shop-dropdown-item"
                                data-id="{{ $shop->id }}"
                                data-name="{{ $shop->name }}"
                                onclick="toggleShopSelection(this)"
                            >
                                <svg class="shop-item-check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2.5"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                {{ $shop->name }}
                            </div>
                        @empty
                            <div class="shop-dropdown-empty">No shops available</div>
                        @endforelse
                    </div>
                </div>

                {{-- Hidden inputs will be injected by JS --}}
                <div id="shopHiddenInputs"></div>

                @error('shop_ids') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            {{-- Coupon Code & Discount Type --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Coupon Code <span class="req">*</span></label>
                    <input type="text" name="coupon_code" class="form-control"
                           placeholder="Coupon code" value="{{ old('coupon_code') }}">
                    @error('coupon_code') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Discount Type <span class="req">*</span></label>
                    <select name="discount_type" class="form-control">
                        <option value="amount"      {{ old('discount_type','amount') == 'amount'      ? 'selected' : '' }}>Amount</option>
                        <option value="percentage"  {{ old('discount_type')          == 'percentage'  ? 'selected' : '' }}>Percentage</option>
                    </select>
                    @error('discount_type') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Discount & Minimum Order Amount --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Discount <span class="req">*</span></label>
                    <input type="number" name="discount" step="0.01" class="form-control"
                           placeholder="Discount" value="{{ old('discount') }}">
                    @error('discount') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Minimum Order Amount <span class="req">*</span></label>
                    <input type="number" name="minimum_order_amount" step="0.01" class="form-control"
                           placeholder="Minimum Order Amount" value="{{ old('minimum_order_amount') }}">
                    @error('minimum_order_amount') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Limit For Single User & Maximum Discount Amount --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Limit For Single User</label>
                    <input type="number" name="limit_for_single_user" class="form-control"
                           placeholder="exm: 5" value="{{ old('limit_for_single_user') }}">
                    @error('limit_for_single_user') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Maximum Discount Amount</label>
                    <input type="number" name="maximum_discount_amount" step="0.01" class="form-control"
                           placeholder="exm: $300" value="{{ old('maximum_discount_amount') }}">
                    @error('maximum_discount_amount') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Start Date & Start Time --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Start Date <span class="req">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                    @error('start_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Start Time <span class="req">*</span></label>

                    {{-- Custom Time Dropdown --}}
                    <div class="time-dropdown-wrapper" id="startTimeDropdown">
                        <div class="time-selected-box placeholder-shown" id="startTimeBox"
                             onclick="toggleTimeDropdown('startTime')">
                            <span id="startTimeDisplay">--:-- --</span>
                        </div>
                        <span class="time-clock-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </span>
                        <div class="time-dropdown-list" id="startTimeList"></div>
                    </div>
                    <input type="hidden" name="start_time" id="startTimeHidden" value="{{ old('start_time') }}">
                    @error('start_time') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Expired Date & Expired Time --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Expired Date <span class="req">*</span></label>
                    <input type="date" name="expired_date" class="form-control" value="{{ old('expired_date') }}">
                    @error('expired_date') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Expired Time <span class="req">*</span></label>

                    <div class="time-dropdown-wrapper" id="expiredTimeDropdown">
                        <div class="time-selected-box placeholder-shown" id="expiredTimeBox"
                             onclick="toggleTimeDropdown('expiredTime')">
                            <span id="expiredTimeDisplay">--:-- --</span>
                        </div>
                        <span class="time-clock-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </span>
                        <div class="time-dropdown-list" id="expiredTimeList"></div>
                    </div>
                    <input type="hidden" name="expired_time" id="expiredTimeHidden" value="{{ old('expired_time') }}">
                    @error('expired_time') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Footer --}}
            <div class="promo-form-footer">
                <a href="{{ route('admin.promocode.index') }}" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit">Submit</button>
            </div>

        </form>
    </div>
</div>

<script>
/* =======================================================
   1. SHOP SEARCHABLE MULTI-SELECT DROPDOWN
======================================================= */
var selectedShopIds   = [];
var selectedShopNames = [];

function toggleShopDropdown(e) {
    var list = document.getElementById('shopDropdownList');
    var box  = document.getElementById('shopSelectedBox');
    var isOpen = list.classList.contains('open');

    if (isOpen) {
        closeShopDropdown();
    } else {
        list.classList.add('open');
        box.classList.add('open');
        document.getElementById('shopSearchInput').focus();
    }
}

function closeShopDropdown() {
    document.getElementById('shopDropdownList').classList.remove('open');
    document.getElementById('shopSelectedBox').classList.remove('open');
}

function toggleShopSelection(el) {
    var id   = el.dataset.id;
    var name = el.dataset.name;

    if (el.classList.contains('selected')) {
        // Deselect
        el.classList.remove('selected');
        selectedShopIds   = selectedShopIds.filter(function(v) { return v !== id; });
        selectedShopNames = selectedShopNames.filter(function(v) { return v !== name; });
        removeShopTag(id);
        removeShopHidden(id);
    } else {
        // Select
        el.classList.add('selected');
        selectedShopIds.push(id);
        selectedShopNames.push(name);
        addShopTag(id, name);
        addShopHidden(id);
    }

    document.getElementById('shopSearchInput').value = '';
    filterShopItems('');
    document.getElementById('shopSearchInput').focus();
}

function addShopTag(id, name) {
    var input = document.getElementById('shopSearchInput');
    var box   = document.getElementById('shopSelectedBox');

    var tag = document.createElement('span');
    tag.className = 'shop-tag';
    tag.id = 'tag_' + id;
    tag.innerHTML =
        '<button type="button" class="shop-tag-remove" onclick="deselectShop(\'' + id + '\', \'' + name.replace(/'/g, "\\'") + '\')">×</button>' +
        name;
    box.insertBefore(tag, input);
}

function removeShopTag(id) {
    var tag = document.getElementById('tag_' + id);
    if (tag) tag.remove();
}

function addShopHidden(id) {
    var div   = document.getElementById('shopHiddenInputs');
    var input = document.createElement('input');
    input.type  = 'hidden';
    input.name  = 'shop_ids[]';
    input.value = id;
    input.id    = 'hidden_' + id;
    div.appendChild(input);
}

function removeShopHidden(id) {
    var el = document.getElementById('hidden_' + id);
    if (el) el.remove();
}

function deselectShop(id, name) {
    // Remove tag + hidden input
    removeShopTag(id);
    removeShopHidden(id);
    selectedShopIds   = selectedShopIds.filter(function(v) { return v !== id; });
    selectedShopNames = selectedShopNames.filter(function(v) { return v !== name; });

    // Unmark in dropdown list
    var items = document.querySelectorAll('#shopDropdownList .shop-dropdown-item');
    items.forEach(function(item) {
        if (item.dataset.id === id) item.classList.remove('selected');
    });
}

// Search filter
document.getElementById('shopSearchInput').addEventListener('input', function() {
    var list = document.getElementById('shopDropdownList');
    list.classList.add('open');
    document.getElementById('shopSelectedBox').classList.add('open');
    filterShopItems(this.value.trim().toLowerCase());
});

function filterShopItems(q) {
    var items   = document.querySelectorAll('#shopDropdownList .shop-dropdown-item');
    var empty   = document.querySelector('#shopDropdownList .shop-dropdown-empty');
    var visible = 0;

    items.forEach(function(item) {
        var name = item.dataset.name.toLowerCase();
        if (name.includes(q)) {
            item.style.display = '';
            visible++;
        } else {
            item.style.display = 'none';
        }
    });

    if (empty) empty.style.display = visible === 0 ? '' : 'none';
}

// Click outside → close
document.addEventListener('click', function(e) {
    var dropdown = document.getElementById('shopDropdown');
    if (!dropdown.contains(e.target)) closeShopDropdown();
});

/* =======================================================
   2. TIME DROPDOWN (30-min intervals, 12h AM/PM)
======================================================= */
function buildTimeOptions() {
    var options = [];
    for (var h = 0; h < 24; h++) {
        for (var m = 0; m < 60; m += 30) {
            var period  = h < 12 ? 'AM' : 'PM';
            var display_h = h % 12;
            if (display_h === 0) display_h = 12;
            var hh = String(display_h).padStart(2, '0');
            var mm = String(m).padStart(2, '0');
            var val_h = String(h).padStart(2, '0');

            options.push({
                label : hh + ':' + mm + ' ' + period,
                value : val_h + ':' + mm + ':00'       // 24h for backend
            });
        }
    }
    return options;
}

var TIME_OPTIONS = buildTimeOptions();

function buildTimeList(listId, dropdownKey, selectedValue) {
    var list = document.getElementById(listId);
    list.innerHTML = '';
    TIME_OPTIONS.forEach(function(opt) {
        var item = document.createElement('div');
        item.className = 'time-dropdown-item' + (opt.value === selectedValue ? ' selected' : '');
        item.textContent = opt.label;
        item.dataset.value = opt.value;
        item.dataset.label = opt.label;
        item.onclick = function() { selectTime(dropdownKey, opt.value, opt.label); };
        list.appendChild(item);
    });
}

function toggleTimeDropdown(key) {
    var listId  = key + 'List';
    var boxId   = key + 'Box';
    var list    = document.getElementById(listId);
    var box     = document.getElementById(boxId);
    var isOpen  = list.classList.contains('open');

    // Close all time dropdowns first
    document.querySelectorAll('.time-dropdown-list').forEach(function(l) { l.classList.remove('open'); });
    document.querySelectorAll('.time-selected-box').forEach(function(b) { b.classList.remove('open'); });

    if (!isOpen) {
        list.classList.add('open');
        box.classList.add('open');

        // Scroll to selected item
        var selected = list.querySelector('.selected');
        if (selected) selected.scrollIntoView({ block: 'nearest' });
    }
}

function selectTime(key, value, label) {
    document.getElementById(key + 'Hidden').value  = value;
    document.getElementById(key + 'Display').textContent = label;

    var box = document.getElementById(key + 'Box');
    box.classList.remove('placeholder-shown');

    // Mark selected in list
    var list = document.getElementById(key + 'List');
    list.querySelectorAll('.time-dropdown-item').forEach(function(item) {
        item.classList.toggle('selected', item.dataset.value === value);
    });

    // Close
    list.classList.remove('open');
    box.classList.remove('open');
}

// Click outside → close time dropdowns
document.addEventListener('click', function(e) {
    ['startTime', 'expiredTime'].forEach(function(key) {
        var wrapper = document.getElementById(key + 'Dropdown');
        if (wrapper && !wrapper.contains(e.target)) {
            document.getElementById(key + 'List').classList.remove('open');
            document.getElementById(key + 'Box').classList.remove('open');
        }
    });
});

// Build lists on page load
document.addEventListener('DOMContentLoaded', function() {
    var oldStart   = '{{ old("start_time") }}';
    var oldExpired = '{{ old("expired_time") }}';

    buildTimeList('startTimeList',   'startTime',   oldStart   || '');
    buildTimeList('expiredTimeList', 'expiredTime', oldExpired || '');

    // Restore old values
    if (oldStart) {
        var found = TIME_OPTIONS.find(function(o) { return o.value === oldStart; });
        if (found) {
            document.getElementById('startTimeDisplay').textContent = found.label;
            document.getElementById('startTimeBox').classList.remove('placeholder-shown');
        }
    }
    if (oldExpired) {
        var foundE = TIME_OPTIONS.find(function(o) { return o.value === oldExpired; });
        if (foundE) {
            document.getElementById('expiredTimeDisplay').textContent = foundE.label;
            document.getElementById('expiredTimeBox').classList.remove('placeholder-shown');
        }
    }
});
</script>

@endsection
