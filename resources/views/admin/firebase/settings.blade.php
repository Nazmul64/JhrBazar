@extends('admin.master')

@section('content')
<style>
    .firebase-settings-page {
        padding: 24px;
        background: #f8fafc;
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }
    .settings-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        margin-bottom: 30px;
        overflow: hidden;
    }
    .settings-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        padding: 24px;
        color: #ffffff;
    }
    .settings-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .settings-header p {
        margin: 5px 0 0 0;
        font-size: 13px;
        opacity: 0.9;
    }
    .settings-body {
        padding: 30px;
    }
    .form-group {
        margin-bottom: 24px;
    }
    .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
        display: block;
    }
    .form-group label span {
        color: #ef4444;
        margin-left: 2px;
    }
    .form-control {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 14px;
        color: #0f172a;
        background-color: #ffffff;
        outline: none;
        transition: all 0.2s ease-in-out;
        box-sizing: border-box;
    }
    .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }
    .textarea-json {
        font-family: 'Courier New', Courier, monospace;
        font-size: 13px;
        background: #f8fafc;
        resize: vertical;
    }
    .btn-submit {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 12px 28px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2);
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(99, 102, 241, 0.3);
    }
    .instructions-card {
        background: #f0fdf4;
        border-left: 4px solid #10b981;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 24px;
    }
    .instructions-card h4 {
        margin-top: 0;
        color: #065f46;
        font-size: 15px;
        font-weight: 700;
    }
    .instructions-card ol {
        margin: 10px 0 0 0;
        padding-left: 20px;
        color: #047857;
        font-size: 13.5px;
        line-height: 1.6;
    }
    .status-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }
    .status-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .4s;
        border-radius: 24px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 16px; width: 16px;
        left: 4px; bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: #10b981;
    }
    input:checked + .slider:before {
        transform: translateX(26px);
    }
</style>

<div class="firebase-settings-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-slate-800 m-0">Firebase Notification Configuration</h1>
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="font-size: 14px; font-weight: 600;">
            <i class="bi bi-clock-history me-1"></i> Sent History
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 8px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 8px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="settings-card">
                <div class="settings-header">
                    <h3>FCM Credentials & Settings</h3>
                    <p>Configure Firebase Cloud Messaging HTTP v1 credentials to trigger real-time device notifications.</p>
                </div>
                <div class="settings-body">
                    <form action="{{ route('admin.firebase.settings.update') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="project_id">Firebase Project ID <span>*</span></label>
                            <input type="text" id="project_id" name="project_id" class="form-control" placeholder="e.g. jhr-bazar-app" value="{{ old('project_id', $setting->project_id ?? '') }}" required>
                            @error('project_id')
                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="api_key">FCM Server Key (Legacy - Optional)</label>
                            <input type="text" id="api_key" name="api_key" class="form-control" placeholder="Enter legacy server key if any" value="{{ old('api_key', $setting->api_key ?? '') }}">
                            @error('api_key')
                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="service_account_json">Service Account JSON Credentials <span>*</span></label>
                            <textarea id="service_account_json" name="service_account_json" class="form-control textarea-json" rows="12" placeholder='{ "type": "service_account", "project_id": "...", ... }' required>{{ old('service_account_json', $setting->service_account_json ?? '') }}</textarea>
                            <small class="text-muted mt-1 d-block">Insert the entire contents of the Service Account key JSON file downloaded from the Google Cloud Console.</small>
                            @error('service_account_json')
                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group d-flex align-items-center gap-3">
                            <label class="m-0" for="status">Active Configuration Status</label>
                            <label class="status-switch">
                                <input type="checkbox" id="status" name="status" value="1" {{ old('status', $setting->status ?? false) ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn-submit">
                                <i class="bi bi-save me-2"></i> Save Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="instructions-card shadow-sm">
                <h4>How to get Service Account credentials?</h4>
                <ol>
                    <li>Go to the <strong>Firebase Console</strong> and choose your project.</li>
                    <li>Click the <strong>Settings (Gear) icon</strong> next to "Project Overview" and choose <strong>Project settings</strong>.</li>
                    <li>Navigate to the <strong>Service accounts</strong> tab.</li>
                    <li>Click <strong>Generate new private key</strong> at the bottom of the page.</li>
                    <li>Confirm by clicking <strong>Generate key</strong>, and a JSON file containing your credentials will be downloaded.</li>
                    <li>Open that JSON file, copy its entire text content, and paste it into the <strong>Service Account JSON Credentials</strong> field on the left.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection
