@extends('admin.master')

@section('content')
<style>
    .dos-wrapper { padding: 10px 0; }
    .dos-page-title { font-size: 22px; font-weight: 700; color: #1a1a2e; margin-bottom: 20px; }
    .dos-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
    .dos-card-header { background: #c0392b; padding: 16px 24px; }
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
    .alert-success-custom { background: #d4f5ec; color: #0d9e6e; border-left: 4px solid #0d9e6e; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500; }
    .alert-error-custom { background: #fde8e8; color: #e53e3e; border-left: 4px solid #e53e3e; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500; }
    .dos-info-row { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
    .dos-info-badge { background: #f0f0ff; border: 1px solid #d0ccff; border-radius: 8px; padding: 8px 16px; font-size: 13px; color: #5a52cc; font-weight: 500; }
    .dos-edit-btn { background: #6c63ff; color: #fff; border: none; padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s; }
    .dos-edit-btn:hover { background: #5a52cc; color: #fff; }
</style>

<div class="dos-wrapper">

    <div class="dos-page-title">Duplicate Order Settings</div>

    @if(session('success'))
        <div class="alert-success-custom"><i class="bi bi-check-circle me-1"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error-custom"><i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}</div>
    @endif

    <div class="dos-card">
        <div class="dos-card-header">
            <h5><i class="bi bi-gear-fill me-2"></i>Current Settings</h5>
        </div>
        <div class="dos-card-body">

            {{-- Current values summary --}}
            <div class="dos-info-row">
                <span class="dos-info-badge">
                    <i class="bi bi-{{ $setting->allow_duplicate_orders ? 'check-circle-fill text-success' : 'x-circle-fill text-danger' }} me-1"></i>
                    Duplicate Orders: {{ $setting->allow_duplicate_orders ? 'Allowed' : 'Blocked' }}
                </span>
                <span class="dos-info-badge">
                    <i class="bi bi-funnel me-1"></i> {{ $setting->duplicate_check_type }}
                </span>
                <span class="dos-info-badge">
                    <i class="bi bi-clock me-1"></i> Time Limit: {{ $setting->duplicate_time_limit }} Hour(s)
                </span>
            </div>

            {{-- Quick toggle --}}
            <form action="{{ route('admin.duplicateordersetting.toggleStatus', $setting->id) }}" method="POST" style="display:inline; margin-bottom: 16px;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn-update" style="background: {{ $setting->allow_duplicate_orders ? '#e53e3e' : '#20c997' }}; margin-bottom: 20px;">
                    <i class="bi bi-toggle-{{ $setting->allow_duplicate_orders ? 'on' : 'off' }}"></i>
                    {{ $setting->allow_duplicate_orders ? 'Disable Duplicates' : 'Enable Duplicates' }}
                </button>
            </form>

            {{-- Edit button --}}
            <div>
                <a href="{{ route('admin.duplicateordersetting.edit', $setting->id) }}" class="dos-edit-btn">
                    <i class="bi bi-pencil-square"></i> Edit Settings
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
