@extends('admin.master')

@push('styles')
<style>
    :root {
        --brand-primary: #e8174a;
        --brand-primary-light: rgba(232, 23, 74, 0.08);
        --brand-primary-hover: #c9113e;
        --text-dark: #1a1d23;
        --text-muted: #6b7280;
        --border-color: #f0f0f5;
        --surface-bg: #f8f9fc;
        --shadow-sm: 0 1px 4px rgba(0,0,0,0.06), 0 2px 12px rgba(0,0,0,0.04);
        --radius-lg: 14px;
        --radius-md: 10px;
        --radius-sm: 7px;
        --transition: all 0.18s ease;
    }

    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.6rem;
    }
    .page-title { font-size: 1.35rem; font-weight: 700; color: var(--text-dark); letter-spacing: -0.3px; margin:0; }
    .page-subtitle { font-size: 12.5px; color: var(--text-muted); margin: 2px 0 0 0; }

    .breadcrumb-custom { display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--text-muted); margin-bottom:1.6rem; }
    .breadcrumb-custom a { color:var(--brand-primary); text-decoration:none; font-weight:600; }
    .breadcrumb-custom a:hover { text-decoration:underline; }
    .breadcrumb-sep { opacity:.4; }

    .form-card {
        background: #fff;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        max-width: 560px;
    }
    .form-card-header {
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-color);
        background: var(--surface-bg);
        border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .form-card-icon {
        width: 34px; height: 34px;
        background: var(--brand-primary-light);
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        color: var(--brand-primary);
        font-size: 15px;
    }
    .form-card-title { font-size: 14.5px; font-weight: 700; color: var(--text-dark); }

    .form-card-body { padding: 28px 24px; }

    .field-group { margin-bottom: 20px; }
    .field-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 7px;
    }
    .field-label .required { color: var(--brand-primary); margin-left: 2px; }
    .field-hint { font-size: 11.5px; color: var(--text-muted); margin-top: 5px; }

    .field-input {
        width: 100%;
        border: 1.5px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 10px 14px;
        font-size: 13.5px;
        color: var(--text-dark);
        background: #fff;
        transition: border-color .15s ease, box-shadow .15s ease;
        outline: none;
    }
    .field-input:focus {
        border-color: var(--brand-primary);
        box-shadow: 0 0 0 3px rgba(232,23,74,.1);
    }
    .field-input::placeholder { color: #b0b7c3; }
    .field-input.is-invalid { border-color: var(--brand-primary) !important; }
    .field-error-msg { font-size: 12px; color: var(--brand-primary); margin-top: 5px; display: flex; align-items:center; gap:4px; }

    .form-card-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--border-color);
        background: var(--surface-bg);
        border-radius: 0 0 var(--radius-lg) var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }
    .btn-cancel {
        background: transparent;
        border: 1.5px solid var(--border-color);
        color: var(--text-muted);
        border-radius: var(--radius-sm);
        padding: 9px 22px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex; align-items: center; gap:6px;
        transition: var(--transition);
    }
    .btn-cancel:hover { background: #f3f4f6; color: var(--text-dark); border-color: #d1d5db; }
    .btn-submit {
        background: var(--brand-primary);
        border: none;
        color: #fff;
        border-radius: var(--radius-sm);
        padding: 9px 26px;
        font-size: 13px;
        font-weight: 600;
        display: inline-flex; align-items: center; gap:6px;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 2px 10px rgba(232,23,74,.22);
    }
    .btn-submit:hover { background: var(--brand-primary-hover); transform: translateY(-1px); box-shadow: 0 4px 16px rgba(232,23,74,.32); }
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<div class="breadcrumb-custom">
    <a href="{{ route('admin.productbrands.index') }}">Brands</a>
    <span class="breadcrumb-sep"><i class="bi bi-chevron-right" style="font-size:10px;"></i></span>
    <span>Create New Brand</span>
</div>

{{-- Page Header --}}
<div class="page-header">
    <div>
        <h4 class="page-title">Create Brand</h4>
        <p class="page-subtitle">Add a new product brand to the system</p>
    </div>
</div>

{{-- Form Card --}}
<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-icon"><i class="bi bi-tag-fill"></i></div>
        <span class="form-card-title">Brand Information</span>
    </div>

    <form action="{{ route('admin.productbrands.store') }}" method="POST">
        @csrf

        <div class="form-card-body">

            {{-- Name --}}
            <div class="field-group">
                <label class="field-label" for="name">
                    Brand Name <span class="required">*</span>
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       class="field-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                       placeholder="e.g. Samsung, Apple, Nike…"
                       value="{{ old('name') }}"
                       autocomplete="off"
                       autofocus>
                @error('name')
                    <div class="field-error-msg">
                        <i class="bi bi-exclamation-circle"></i> {{ $message }}
                    </div>
                @enderror
                <p class="field-hint">Brand names must be unique. Max 255 characters.</p>
            </div>

        </div>

        <div class="form-card-footer">
            <a href="{{ route('admin.productbrands.index') }}" class="btn-cancel">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn-submit">
                <i class="bi bi-check-lg"></i> Create Brand
            </button>
        </div>
    </form>
</div>

@endsection
