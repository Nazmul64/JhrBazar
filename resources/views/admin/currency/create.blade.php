{{-- resources/views/admin/currency/create.blade.php --}}
@extends('admin.master')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&display=swap');
    .currency-form-wrap * { font-family: 'DM Sans', sans-serif; }

    .page-fade-in {
        animation: pageFadeIn 0.4s ease both;
    }
    @keyframes pageFadeIn {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .form-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 8px 24px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .form-card-header {
        padding: 20px 28px;
        border-bottom: 1px solid #f9fafb;
        display: flex;
        align-items: center;
        gap: 12px;
        background: linear-gradient(135deg, #fff5f8 0%, #fff 60%);
    }

    .header-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #e91e63, #c2185b);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(233,30,99,0.3);
        flex-shrink: 0;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 7px;
        letter-spacing: 0.1px;
    }
    .form-label span { color: #e91e63; margin-left: 2px; }

    .form-control {
        width: 100%;
        border: 1.5px solid #e5e7eb;
        border-radius: 10px;
        padding: 11px 14px;
        font-size: 14px;
        color: #111827;
        font-family: 'DM Sans', sans-serif;
        background: #fafafa;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        outline: none;
        box-sizing: border-box;
    }
    .form-control:focus {
        border-color: #e91e63;
        box-shadow: 0 0 0 3px rgba(233,30,99,0.1);
        background: #fff;
    }
    .form-control.is-invalid {
        border-color: #f43f5e;
        background: #fff5f6;
    }
    .form-control::placeholder {
        color: #b0bec5;
        font-weight: 400;
    }

    .input-hint {
        font-size: 12px;
        color: #9ca3af;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .invalid-feedback {
        font-size: 12px;
        color: #f43f5e;
        margin-top: 5px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .form-section-label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #d1d5db;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #f1f5f9;
    }

    .btn-cancel {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 22px;
        border-radius: 10px;
        border: 1.5px solid #e5e7eb;
        background: #fff;
        font-size: 13.5px;
        font-weight: 600;
        color: #6b7280;
        text-decoration: none;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
        transition: border-color 0.15s, color 0.15s, background 0.15s;
    }
    .btn-cancel:hover {
        border-color: #d1d5db;
        background: #f9fafb;
        color: #374151;
    }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 26px;
        border-radius: 10px;
        border: none;
        background: linear-gradient(135deg, #e91e63, #c2185b);
        color: #fff;
        font-size: 13.5px;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(233,30,99,0.35);
        transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
    }
    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(233,30,99,0.45);
    }
    .btn-submit:active { transform: translateY(0); }
</style>

<div class="currency-form-wrap page-fade-in"
     style="padding:28px 32px; min-height:100vh; background:#f8fafc;">

    {{-- Breadcrumb --}}
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:20px;
                font-size:13px; color:#9ca3af;">
        <a href="{{ route('admin.currencies.index') }}"
           style="color:#9ca3af; text-decoration:none; font-weight:500;
                  transition:color 0.15s;"
           onmouseover="this.style.color='#e91e63'"
           onmouseout="this.style.color='#9ca3af'">
            Currencies
        </a>
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24"><path stroke-linecap="round" d="M9 5l7 7-7 7"/></svg>
        <span style="color:#374151; font-weight:600;">New Currency</span>
    </div>

    {{-- Page Title --}}
    <div style="margin-bottom:24px;">
        <h1 style="font-size:22px; font-weight:700; color:#111827; margin:0 0 4px;">
            New Currency
        </h1>
        <p style="font-size:13px; color:#9ca3af; margin:0;">
            Add a new currency to your store
        </p>
    </div>

    {{-- Form Card --}}
    <div class="form-card" style="max-width:860px;">

        {{-- Card Header --}}
        <div class="form-card-header">
            <div class="header-icon">
                <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2.2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2
                             m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1
                             c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div style="font-size:15px; font-weight:700; color:#111827;">
                    Add New Currency
                </div>
                <div style="font-size:12.5px; color:#9ca3af; margin-top:2px;">
                    Fill in the details below to create a new currency
                </div>
            </div>
        </div>

        {{-- Form Body --}}
        <form action="{{ route('admin.currencies.store') }}" method="POST" novalidate>
            @csrf

            <div style="padding:28px;">

                <div class="form-section-label">Currency Information</div>

                {{-- Row 1 --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">

                    {{-- Name --}}
                    <div>
                        <label class="form-label">
                            Currency Name <span>*</span>
                        </label>
                        <div style="position:relative;">
                            <div style="position:absolute; left:13px; top:50%; transform:translateY(-50%);
                                        color:#d1d5db; pointer-events:none;">
                                <svg width="15" height="15" fill="none" stroke="currentColor"
                                     stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                </svg>
                            </div>
                            <input
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="e.g. USD, BDT, INR"
                                class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                style="padding-left:38px;"
                            >
                        </div>
                        @error('name')
                            <div class="invalid-feedback">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0
                                             1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                          clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Symbol --}}
                    <div>
                        <label class="form-label">
                            Currency Symbol <span>*</span>
                        </label>
                        <div style="position:relative;">
                            <div style="position:absolute; left:13px; top:50%; transform:translateY(-50%);
                                        color:#d1d5db; pointer-events:none; font-size:15px; font-weight:600;">
                                ¤
                            </div>
                            <input
                                type="text"
                                name="symbol"
                                value="{{ old('symbol') }}"
                                placeholder="e.g. $, ৳, ₹"
                                class="form-control {{ $errors->has('symbol') ? 'is-invalid' : '' }}"
                                style="padding-left:38px;"
                            >
                        </div>
                        @error('symbol')
                            <div class="invalid-feedback">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0
                                             1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                          clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                {{-- Row 2 --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    <div>
                        <label class="form-label">
                            Currency Rate (USD = 1) <span>*</span>
                        </label>
                        <div style="position:relative;">
                            <div style="position:absolute; left:0; top:0; bottom:0; width:42px;
                                        display:flex; align-items:center; justify-content:center;
                                        background:#f3f4f6; border-right:1.5px solid #e5e7eb;
                                        border-radius:10px 0 0 10px; font-size:12px;
                                        font-weight:700; color:#9ca3af; letter-spacing:0.3px;">
                                USD
                            </div>
                            <input
                                type="number"
                                name="rate"
                                value="{{ old('rate') }}"
                                placeholder="0.00"
                                step="any"
                                min="0"
                                class="form-control {{ $errors->has('rate') ? 'is-invalid' : '' }}"
                                style="padding-left:52px; border-radius:0 10px 10px 0;
                                       border-left: none;"
                            >
                        </div>
                        @error('rate')
                            <div class="invalid-feedback">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0
                                             1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                          clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="input-hint">
                            <svg width="12" height="12" fill="none" stroke="currentColor"
                                 stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <path stroke-linecap="round" d="M12 16v-4m0-4h.01"/>
                            </svg>
                            Enter the amount in USD (1 USD = your current rate)
                        </div>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div style="padding:18px 28px; border-top:1px solid #f9fafb; background:#fafafa;
                        display:flex; align-items:center; justify-content:space-between;">
                <a href="{{ route('admin.currencies.index') }}" class="btn-cancel">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Cancel
                </a>
                <button type="submit" class="btn-submit">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Create Currency
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
