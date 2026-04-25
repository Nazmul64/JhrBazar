@extends('admin.master')

@section('content')

<style>
    .gtm-wrapper {
        padding: 10px 0;
    }

    .gtm-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
    }

    .gtm-header h2 {
        font-size: 24px;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0;
    }

    .btn-manage {
        background: #6c63ff;
        color: #fff;
        border: none;
        padding: 10px 22px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.2s;
    }

    .btn-manage:hover {
        background: #574fd6;
        color: #fff;
    }

    .gtm-form-card {
        background: #fff;
        border-radius: 14px;
        padding: 36px 40px;
        box-shadow: 0 2px 14px rgba(0,0,0,0.07);
        max-width: 860px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-size: 13.5px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .form-label span.required {
        color: #e53e3e;
        margin-left: 2px;
    }

    .form-control-custom {
        width: 100%;
        padding: 11px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        color: #333;
        outline: none;
        transition: border 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
        background: #fff;
    }

    .form-control-custom:focus {
        border-color: #6c63ff;
        box-shadow: 0 0 0 3px rgba(108, 99, 255, 0.1);
    }

    .form-control-custom.is-invalid {
        border-color: #e53e3e;
    }

    .invalid-feedback {
        color: #e53e3e;
        font-size: 12px;
        margin-top: 5px;
        display: block;
    }

    /* Toggle Switch */
    .toggle-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 28px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background: #ccc;
        border-radius: 28px;
        transition: 0.3s;
    }

    .toggle-slider:before {
        content: "";
        position: absolute;
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: 0.3s;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
    }

    .toggle-switch input:checked + .toggle-slider {
        background: #20c997;
    }

    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }

    .toggle-label-text {
        font-size: 13.5px;
        color: #555;
        font-weight: 500;
    }

    /* Submit button */
    .btn-submit {
        background: #20c997;
        color: #fff;
        border: none;
        padding: 11px 30px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        margin-top: 6px;
    }

    .btn-submit:hover {
        background: #17a97f;
    }

    @media (max-width: 600px) {
        .gtm-form-card { padding: 22px 16px; }
        .gtm-header h2 { font-size: 18px; }
    }
</style>

<div class="gtm-wrapper">

    {{-- Header --}}
    <div class="gtm-header">
        <h2>Tag Manager Edit</h2>
        <a href="{{ route('admin.googletagmanager.index') }}" class="btn-manage">Manage</a>
    </div>

    {{-- Form Card --}}
    <div class="gtm-form-card">
        <form action="{{ route('admin.googletagmanager.update', $tagmanager->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Tag Manager ID --}}
            <div class="form-group">
                <label class="form-label">
                    Tag Manager ID <span class="required">*</span>
                </label>
                <input
                    type="text"
                    name="tag_manager_id"
                    class="form-control-custom @error('tag_manager_id') is-invalid @enderror"
                    value="{{ old('tag_manager_id', $tagmanager->tag_manager_id) }}"
                    placeholder="e.g. GTM-XXXXXXX"
                    autocomplete="off"
                />
                @error('tag_manager_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            {{-- Status --}}
            <div class="form-group">
                <label class="form-label">Status</label>
                <div class="toggle-wrapper">
                    <label class="toggle-switch">
                        <input type="checkbox" name="status" {{ old('status', $tagmanager->status) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label-text">Active</span>
                </div>
            </div>

            {{-- Submit --}}
            <div class="form-group" style="margin-bottom:0;">
                <button type="submit" class="btn-submit">Submit</button>
            </div>

        </form>
    </div>

</div>

@endsection
