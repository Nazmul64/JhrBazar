@extends('admin.master')

@section('content')
<style>
    /* (same styles as above - copy from index) */
    .dos-wrapper { padding: 10px 0; }
    .dos-page-title { font-size: 22px; font-weight: 700; color: #1a1a2e; margin-bottom: 20px; }
    .dos-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
    .dos-card-header { background: #c0392b; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; }
    .dos-card-header h5 { margin: 0; font-size: 15px; font-weight: 700; color: #fff; }
    .dos-card-body { padding: 28px 28px 32px; }
    .dos-check-group { display: flex; align-items: center; gap: 10px; margin-bottom: 26px; }
    .dos-check-group input[type="checkbox"] { width: 17px; height: 17px; accent-color: #6c63ff; cursor: pointer; }
    .dos-check-group label { font-size: 14px; font-weight: 500; color: #333; cursor: pointer; margin: 0; }
    .dos-form-group { margin-bottom: 22px; }
    .dos-form-label { display: block; font-size: 13px; font-weight: 500; color: #555; margin-bottom: 8px; }
    .dos-form-control { width: 100%; padding: 11px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; color: #333; outline: none; transition: border 0.2s; box-sizing: border-box; background: #fff; font-family: inherit; }
    .dos-form-control:focus { border-color: #6c63ff; box-shadow: 0 0 0 3px rgba(108,99,255,0.10); }
    .dos-form-control.is-invalid { border-color: #e53e3e; }
    .invalid-feedback-custom { color: #e53e3e; font-size: 12px; margin-top: 5px; display: block; }
    select.dos-form-control { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23888' d='M6 8L1 3h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; padding-right: 36px; }
    textarea.dos-form-control { resize: vertical; min-height: 100px; line-height: 1.6; }
    .btn-update { background: #20c997; color: #fff; border: none; padding: 11px 26px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.2s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-update:hover { background: #17a97f; }
    .btn-back { background: #6c757d; color: #fff; border: none; padding: 11px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: background 0.2s; }
    .btn-back:hover { background: #545b62; color: #fff; }
    .alert-error-custom { background: #fde8e8; color: #e53e3e; border-left: 4px solid #e53e3e; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
</style>

<div class="dos-wrapper">

    <div class="dos-page-title">Edit Duplicate Order Settings</div>

    @if($errors->any())
        <div class="alert-error-custom">
            <i class="bi bi-exclamation-circle me-1"></i>
            Please fix the errors below.
        </div>
    @endif

    <div class="dos-card">
        <div class="dos-card-header">
            <h5><i class="bi bi-pencil-square me-2"></i>Edit Settings</h5>
            <a href="{{ route('admin.duplicateordersetting.index') }}" class="btn-back" style="padding: 6px 14px; font-size: 13px;">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <div class="dos-card-body">
            <form action="{{ route('admin.duplicateordersetting.update', $setting->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Allow Duplicate Orders --}}
                <div class="dos-check-group">
                    <input
                        type="checkbox"
                        name="allow_duplicate_orders"
                        id="allow_duplicate_orders"
                        {{ old('allow_duplicate_orders', $setting->allow_duplicate_orders) ? 'checked' : '' }}
                    />
                    <label for="allow_duplicate_orders">Allow Duplicate Orders</label>
                </div>

                {{-- Duplicate Check Type --}}
                <div class="dos-form-group">
                    <label class="dos-form-label" for="duplicate_check_type">Duplicate Check Type</label>
                    <select name="duplicate_check_type" id="duplicate_check_type"
                        class="dos-form-control @error('duplicate_check_type') is-invalid @enderror">
                        @foreach(['Product + IP + Phone', 'Product + Phone', 'Product + IP', 'Phone Only', 'IP Only'] as $type)
                            <option value="{{ $type }}"
                                {{ old('duplicate_check_type', $setting->duplicate_check_type) === $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                    @error('duplicate_check_type')
                        <span class="invalid-feedback-custom">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Duplicate Time Limit --}}
                <div class="dos-form-group">
                    <label class="dos-form-label" for="duplicate_time_limit">Duplicate Time Limit (Hours)</label>
                    <input
                        type="number"
                        name="duplicate_time_limit"
                        id="duplicate_time_limit"
                        class="dos-form-control @error('duplicate_time_limit') is-invalid @enderror"
                        value="{{ old('duplicate_time_limit', $setting->duplicate_time_limit) }}"
                        min="1" max="9999"
                        placeholder="e.g. 1"
                    />
                    @error('duplicate_time_limit')
                        <span class="invalid-feedback-custom">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Duplicate Check Message --}}
                <div class="dos-form-group">
                    <label class="dos-form-label" for="duplicate_check_message">Duplicate Check Message</label>
                    <textarea
                        name="duplicate_check_message"
                        id="duplicate_check_message"
                        class="dos-form-control @error('duplicate_check_message') is-invalid @enderror"
                        placeholder="Message shown when duplicate order is detected..."
                    >{{ old('duplicate_check_message', $setting->duplicate_check_message) }}</textarea>
                    @error('duplicate_check_message')
                        <span class="invalid-feedback-custom">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Buttons --}}
                <div style="display: flex; gap: 12px; margin-top: 8px;">
                    <button type="submit" class="btn-update">
                        <i class="bi bi-floppy"></i> Update Settings
                    </button>
                    <a href="{{ route('admin.duplicateordersetting.index') }}" class="btn-back">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
