@extends('admin.master')

@section('content')
<style>
    .notification-create-page {
        padding: 24px;
        background: #f8fafc;
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }
    .compose-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .compose-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        padding: 24px;
        color: #ffffff;
    }
    .compose-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .compose-header p {
        margin: 5px 0 0 0;
        font-size: 13px;
        opacity: 0.9;
    }
    .compose-body {
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
    .btn-send {
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
    .btn-send:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(99, 102, 241, 0.3);
    }
    .btn-cancel {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 12px 28px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-block;
        text-align: center;
    }
    .btn-cancel:hover {
        background: #e2e8f0;
        color: #334155;
    }
    .preview-card {
        border-radius: 12px;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        padding: 20px;
    }
    .preview-phone {
        width: 260px;
        background: #0f172a;
        border-radius: 36px;
        padding: 45px 12px;
        margin: 0 auto;
        box-shadow: 0 20px 40px -15px rgba(0,0,0,0.5);
        border: 4px solid #334155;
        position: relative;
    }
    .preview-phone::before {
        content: '';
        position: absolute;
        top: 15px; left: 50%;
        transform: translateX(-50%);
        width: 60px; height: 15px;
        background: #334155;
        border-radius: 10px;
    }
    .preview-notification {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 14px;
        padding: 12px;
        color: #000;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .preview-header {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        color: #666;
        margin-bottom: 6px;
    }
    .preview-logo {
        width: 14px; height: 14px;
        background: #4f46e5;
        border-radius: 3px;
        display: inline-block;
    }
    .preview-title {
        font-weight: bold;
        font-size: 13px;
        margin-bottom: 2px;
        word-break: break-word;
    }
    .preview-body {
        font-size: 12px;
        color: #333;
        line-height: 1.3;
        word-break: break-word;
    }
</style>

<div class="notification-create-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-slate-800 m-0">Compose Push Notification</h1>
        <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary rounded-pill px-4" style="font-size: 14px; font-weight: 600;">
            <i class="bi bi-arrow-left me-1"></i> Back to History
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 8px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-7">
            <div class="compose-card">
                <div class="compose-header">
                    <h3>Notification Content</h3>
                    <p>Draft your message and target selection below.</p>
                </div>
                <div class="compose-body">
                    <form action="{{ route('admin.notifications.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="target_type">Recipient Audience <span>*</span></label>
                            <select id="target_type" name="target_type" class="form-control" onchange="toggleCustomerSelect(this.value)" required>
                                <option value="all" {{ old('target_type') === 'all' ? 'selected' : '' }}>Send to All Registered Customers</option>
                                <option value="single" {{ old('target_type') === 'single' ? 'selected' : '' }}>Send to Specific Customer</option>
                            </select>
                        </div>

                        <div class="form-group" id="customer_select_group" style="display: {{ old('target_type') === 'single' ? 'block' : 'none' }};">
                            <label for="user_id">Select Customer <span>*</span></label>
                            <select id="user_id" name="user_id" class="form-control">
                                <option value="">-- Search / Choose Customer --</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('user_id') == $customer->id ? 'selected' : '' }} data-has-token="{{ !empty($customer->fcm_token) ? 'true' : 'false' }}">
                                        {{ $customer->name }} ({{ $customer->phone ?? 'No Phone' }}) {{ empty($customer->fcm_token) ? '[No Token]' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger mt-1 d-none" id="token_warning"><i class="bi bi-exclamation-triangle"></i> This user does not have a registered device token. Notification cannot be delivered.</small>
                            @error('user_id')
                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="title">Notification Title <span>*</span></label>
                            <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Flash Sale Alert! ⚡" value="{{ old('title') }}" onkeyup="updatePreviewTitle(this.value)" required>
                            @error('title')
                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="body">Notification Description/Body <span>*</span></label>
                            <textarea id="body" name="body" class="form-control" rows="4" placeholder="Type your message description here..." onkeyup="updatePreviewBody(this.value)" required>{{ old('body') }}</textarea>
                            @error('body')
                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="image_url">Notification Banner Image URL (Optional)</label>
                            <input type="url" id="image_url" name="image_url" class="form-control" placeholder="https://example.com/banner.png" value="{{ old('image_url') }}">
                            <small class="text-muted mt-1 d-block">Rich notification banner URL to be displayed on modern devices.</small>
                            @error('image_url')
                                <small class="text-danger mt-1 d-block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn-send">
                                <i class="bi bi-send-fill me-2"></i> Send Notification
                            </button>
                            <a href="{{ route('admin.notifications.index') }}" class="btn-cancel">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <h5 class="fw-bold text-slate-800 mb-3"><i class="bi bi-phone"></i> Real-time Device Preview</h5>
            <div class="preview-card shadow-sm text-center">
                <div class="preview-phone">
                    <div class="preview-notification text-start">
                        <div class="preview-header">
                            <span class="preview-logo"></span>
                            <span>JHR BAZAR</span>
                            <span class="ms-auto">now</span>
                        </div>
                        <div class="preview-title" id="p_title">FCM Message Title</div>
                        <div class="preview-body" id="p_body">This is a mock representation of the push notification on a customer's phone screen.</div>
                    </div>
                </div>
                <small class="text-muted d-block mt-3">Notifications will appear as heads-up alerts on customer lockscreeen/status-bar when their device registers a valid token.</small>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleCustomerSelect(val) {
        const group = document.getElementById('customer_select_group');
        const select = document.getElementById('user_id');
        if (val === 'single') {
            group.style.display = 'block';
            select.required = true;
        } else {
            group.style.display = 'none';
            select.required = false;
            select.value = '';
            document.getElementById('token_warning').classList.add('d-none');
        }
    }

    document.getElementById('user_id').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const warning = document.getElementById('token_warning');
        if (option.value && option.getAttribute('data-has-token') === 'false') {
            warning.classList.remove('d-none');
        } else {
            warning.classList.add('d-none');
        }
    });

    function updatePreviewTitle(val) {
        document.getElementById('p_title').innerText = val || 'FCM Message Title';
    }

    function updatePreviewBody(val) {
        document.getElementById('p_body').innerText = val || 'This is a mock representation of the push notification on a customer\'s phone screen.';
    }

    // Run on init
    window.addEventListener('DOMContentLoaded', () => {
        const target = document.getElementById('target_type').value;
        toggleCustomerSelect(target);
        updatePreviewTitle(document.getElementById('title').value);
        updatePreviewBody(document.getElementById('body').value);
    });
</script>
@endsection
