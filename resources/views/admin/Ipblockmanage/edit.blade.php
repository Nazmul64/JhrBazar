@extends('admin.master')

@section('content')
<style>
    .ibm-wrapper { padding: 10px 0; }
    .ibm-page-title { font-size: 22px; font-weight: 700; color: #1a1a2e; margin-bottom: 20px; }
    .ibm-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
    .ibm-card-header { background: #c0392b; padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; }
    .ibm-card-header h5 { margin: 0; font-size: 15px; font-weight: 700; color: #fff; }
    .ibm-card-body { padding: 28px; }
    .ibm-form-group { margin-bottom: 22px; }
    .ibm-form-label { display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px; }
    .ibm-form-label span { color: #e53e3e; }
    .ibm-form-control { width: 100%; padding: 11px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; color: #333; outline: none; box-sizing: border-box; font-family: inherit; transition: border 0.2s, box-shadow 0.2s; background: #fff; }
    .ibm-form-control:focus { border-color: #20c997; box-shadow: 0 0 0 3px rgba(32,201,151,0.12); }
    .ibm-form-control.is-invalid { border-color: #e53e3e; }
    textarea.ibm-form-control { min-height: 110px; resize: vertical; line-height: 1.6; }
    .invalid-feedback-custom { color: #e53e3e; font-size: 12px; margin-top: 5px; display: block; }
    .btn-update { background: #20c997; color: #fff; border: none; padding: 11px 26px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.2s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-update:hover { background: #17a97f; }
    .btn-back { background: #6c757d; color: #fff; border: none; padding: 11px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: background 0.2s; }
    .btn-back:hover { background: #545b62; color: #fff; }
    .alert-error-custom { background: #fde8e8; color: #e53e3e; border-left: 4px solid #e53e3e; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
</style>

<div class="ibm-wrapper">

    <div class="ibm-page-title">Edit IP Block</div>

    @if($errors->any())
        <div class="alert-error-custom">
            <i class="bi bi-exclamation-circle me-1"></i> Please fix the errors below.
        </div>
    @endif

    <div class="ibm-card">
        <div class="ibm-card-header">
            <h5><i class="bi bi-pencil-square me-2"></i>Edit IP Block — {{ $ipblock->ip_address }}</h5>
            <a href="{{ route('admin.Ipblockmanage.index') }}" class="btn-back" style="padding: 6px 14px; font-size: 13px;">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <div class="ibm-card-body">
            <form action="{{ route('admin.Ipblockmanage.update', $ipblock->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="ibm-form-group">
                    <label class="ibm-form-label" for="ip_address">IP No <span>*</span></label>
                    <input
                        type="text"
                        name="ip_address"
                        id="ip_address"
                        class="ibm-form-control @error('ip_address') is-invalid @enderror"
                        value="{{ old('ip_address', $ipblock->ip_address) }}"
                        placeholder="e.g. 192.168.1.1"
                    />
                    @error('ip_address')
                        <span class="invalid-feedback-custom">{{ $message }}</span>
                    @enderror
                </div>

                <div class="ibm-form-group">
                    <label class="ibm-form-label" for="reason">Reason <span>*</span></label>
                    <textarea
                        name="reason"
                        id="reason"
                        class="ibm-form-control @error('reason') is-invalid @enderror"
                        placeholder="Enter reason for blocking this IP..."
                    >{{ old('reason', $ipblock->reason) }}</textarea>
                    @error('reason')
                        <span class="invalid-feedback-custom">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: flex; gap: 12px; margin-top: 8px;">
                    <button type="submit" class="btn-update">
                        <i class="bi bi-floppy"></i> Update
                    </button>
                    <a href="{{ route('admin.Ipblockmanage.index') }}" class="btn-back">
                        <i class="bi bi-x-circle"></i> Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
