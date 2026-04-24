@extends('admin.master')

@section('content')

@php
    $settings = \App\Models\GenaralSetting::first();
    $cur = $settings->default_currency ?? '৳';
@endphp

<style>
:root {
    --accent:    #e7567c;
    --accent-dk: #c93f65;
    --blue:      #4361ee;
    --text:      #1a1f36;
    --muted:     #6b7a99;
    --border:    #e4e9f2;
    --bg:        #f0f2f5;
    --white:     #ffffff;
    --radius:    8px;
    --radius-sm: 5px;
    --shadow:    0 1px 4px rgba(0,0,0,.07);
}
*, *::before, *::after { box-sizing: border-box; }

.sc-page { padding: 24px; background: var(--bg); min-height: 100vh; font-family: 'Segoe UI', system-ui, sans-serif; }

.sc-page-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 22px; flex-wrap: wrap; gap: 12px;
}
.sc-page-title { font-size: 20px; font-weight: 800; color: var(--text); margin: 0; }

.btn-back {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 18px; background: var(--white);
    border: 1.5px solid var(--border); border-radius: var(--radius-sm);
    font-size: 13px; font-weight: 600; color: var(--muted);
    text-decoration: none; transition: all .15s;
}
.btn-back:hover { background: #f1f5f9; color: var(--text); text-decoration: none; }

/* Form Card */
.form-card {
    background: var(--white); border-radius: var(--radius);
    box-shadow: var(--shadow); overflow: hidden;
    max-width: 680px;
}
.form-card-head {
    padding: 16px 24px; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 10px;
}
.form-card-head h4 { font-size: 15px; font-weight: 700; color: var(--text); margin: 0; }
.form-card-body { padding: 28px 24px; }

.form-group { margin-bottom: 20px; }
.form-label {
    display: block; font-size: 13px; font-weight: 600;
    color: var(--text); margin-bottom: 7px;
}
.form-label span { color: var(--accent); }
.form-control {
    width: 100%; height: 44px;
    border: 1.5px solid var(--border); border-radius: var(--radius-sm);
    padding: 0 14px; font-size: 14px; color: var(--text);
    background: #f9fafb; outline: none; font-family: inherit;
    transition: border-color .15s, box-shadow .15s;
}
.form-control:focus {
    border-color: var(--accent); background: var(--white);
    box-shadow: 0 0 0 3px rgba(231,86,124,.1);
}
.form-control.is-invalid { border-color: #dc2626; }
.invalid-feedback { font-size: 12px; color: #dc2626; margin-top: 5px; }

/* Prefix input */
.input-group { display: flex; }
.input-prefix {
    height: 44px; padding: 0 14px;
    background: #f1f5f9; border: 1.5px solid var(--border);
    border-right: none; border-radius: var(--radius-sm) 0 0 var(--radius-sm);
    font-size: 14px; font-weight: 700; color: var(--muted);
    display: flex; align-items: center; white-space: nowrap; flex-shrink: 0;
}
.input-group .form-control {
    border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
}

/* Toggle switch */
.toggle-wrap { display: flex; align-items: center; gap: 12px; }
.toggle-switch {
    position: relative; width: 50px; height: 26px;
    cursor: pointer; display: inline-block;
}
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
    position: absolute; inset: 0;
    background: #d1d5db; border-radius: 26px;
    transition: background .2s;
}
.toggle-slider::after {
    content: ''; position: absolute;
    width: 20px; height: 20px; border-radius: 50%;
    background: #fff; left: 3px; top: 3px;
    transition: transform .2s;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
}
.toggle-switch input:checked + .toggle-slider { background: #22c55e; }
.toggle-switch input:checked + .toggle-slider::after { transform: translateX(24px); }
.toggle-label { font-size: 13px; color: var(--muted); font-weight: 500; }

/* Info box */
.info-box {
    background: #f0f9ff; border: 1px solid #bae6fd;
    border-radius: var(--radius-sm); padding: 12px 16px;
    font-size: 13px; color: #0369a1;
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 20px;
}

/* Submit btn */
.form-footer { padding: 16px 24px; border-top: 1px solid var(--border); display: flex; gap: 12px; }
.btn-submit {
    height: 44px; padding: 0 28px;
    background: linear-gradient(135deg, #e7567c, #c93f65);
    color: #fff; border: none; border-radius: var(--radius-sm);
    font-size: 14px; font-weight: 700; cursor: pointer;
    display: inline-flex; align-items: center; gap: 8px;
    font-family: inherit; transition: opacity .15s;
}
.btn-submit:hover { opacity: .9; }
.btn-cancel {
    height: 44px; padding: 0 22px;
    background: #f1f5f9; color: var(--muted);
    border: 1.5px solid var(--border); border-radius: var(--radius-sm);
    font-size: 14px; font-weight: 600; cursor: pointer;
    text-decoration: none; display: inline-flex; align-items: center; gap: 7px;
    font-family: inherit; transition: background .15s;
}
.btn-cancel:hover { background: #e2e8f0; color: var(--text); text-decoration: none; }
</style>

<div class="sc-page">

    <div class="sc-page-header">
        <h2 class="sc-page-title">
            <i class="bi bi-pencil" style="color:var(--accent);margin-right:6px;"></i>
            Edit Shipping Charge
        </h2>
        <a href="{{ route('admin.shippingcharge.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="form-card">
        <div class="form-card-head">
            <i class="bi bi-pencil-square" style="color:var(--accent);font-size:18px;"></i>
            <h4>Edit: {{ $shippingcharge->area_name }}</h4>
        </div>

        <form action="{{ route('admin.shippingcharge.update', $shippingcharge->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-card-body">

                <div class="info-box">
                    <i class="bi bi-info-circle-fill"></i>
                    Editing shipping charge for area: <strong>{{ $shippingcharge->area_name }}</strong>
                </div>

                {{-- Area Name --}}
                <div class="form-group">
                    <label class="form-label">
                        Area Name <span>*</span>
                    </label>
                    <input type="text"
                           name="area_name"
                           class="form-control {{ $errors->has('area_name') ? 'is-invalid' : '' }}"
                           placeholder="e.g. Dhaka Inside, Chittagong, Sylhet..."
                           value="{{ old('area_name', $shippingcharge->area_name) }}"
                           autocomplete="off">
                    @error('area_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Charge --}}
                <div class="form-group">
                    <label class="form-label">
                        Shipping Charge <span>*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-prefix">{{ $cur }}</span>
                        <input type="number"
                               name="charge"
                               class="form-control {{ $errors->has('charge') ? 'is-invalid' : '' }}"
                               placeholder="0.00"
                               value="{{ old('charge', $shippingcharge->charge) }}"
                               min="0" step="0.01">
                    </div>
                    @error('charge')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Status</label>
                    <div class="toggle-wrap">
                        <label class="toggle-switch">
                            <input type="checkbox" name="status" value="1"
                                   {{ old('status', $shippingcharge->status) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="toggle-label">Active (checked = enabled)</span>
                    </div>
                </div>

            </div>

            <div class="form-footer">
                <button type="submit" class="btn-submit">
                    <i class="bi bi-check-circle"></i> Update Shipping Charge
                </button>
                <a href="{{ route('admin.shippingcharge.index') }}" class="btn-cancel">
                    <i class="bi bi-x"></i> Cancel
                </a>
            </div>
        </form>
    </div>

</div>

@endsection
