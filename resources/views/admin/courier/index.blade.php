@extends('admin.master')

@section('content')
<style>
.gateway-page {
    background: #f4f6f9;
    min-height: 100vh;
    padding: 28px 24px;
    font-family: 'Segoe UI', sans-serif;
}

.page-title {
    font-size: 22px;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 28px;
    letter-spacing: .3px;
}

.gw-card {
    background: #ffffff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.07);
}

.gw-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 22px;
    border-bottom: 1px solid #f0f0f0;
}

.gw-card-header h3 {
    font-size: 14px;
    font-weight: 700;
    letter-spacing: 1px;
    color: #1a1a2e;
    margin: 0;
    text-transform: uppercase;
}

.gw-card-body {
    padding: 24px 24px 20px;
}

.gateway-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.gw-form-group {
    margin-bottom: 16px;
}

.gw-form-group label {
    display: block;
    font-size: 13px;
    color: #444;
    margin-bottom: 6px;
    font-weight: 500;
}

.gw-form-group label .req {
    color: #e91e8c;
    margin-left: 2px;
}

.gw-form-control {
    width: 100%;
    padding: 9px 13px;
    font-size: 13px;
    border: 1px solid #dde2ec;
    border-radius: 6px;
    color: #333;
    background: #fff;
    outline: none;
    transition: border-color .2s;
    box-sizing: border-box;
}

.gw-form-control:focus {
    border-color: #e91e8c;
    box-shadow: 0 0 0 3px rgba(233,30,140,.08);
}

.toggle-switch {
    position: relative;
    width: 46px;
    height: 24px;
    cursor: pointer;
    display: inline-block;
}

.toggle-switch input { display: none; }

.toggle-slider {
    position: absolute;
    inset: 0;
    background: #ccc;
    border-radius: 34px;
    transition: .3s;
}

.toggle-slider:before {
    content: '';
    position: absolute;
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background: #fff;
    border-radius: 50%;
    transition: .3s;
}

.toggle-switch input:checked + .toggle-slider { background: #e91e8c; }
.toggle-switch input:checked + .toggle-slider:before { transform: translateX(22px); }

.btn-submit-teal {
    background: #26c6a6;
    color: #fff;
    border: none;
    padding: 9px 22px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s;
}

.btn-submit-teal:hover { background: #1da88a; }

.gw-alert-info {
    background: #e3f2fd;
    border: 1px solid #90caf9;
    border-radius: 6px;
    padding: 12px 14px;
    font-size: 13px;
    color: #0d47a1;
    margin-bottom: 18px;
    line-height: 1.5;
}

.pathao-hint {
    font-size: 12px;
    color: #888;
    margin-top: 4px;
}

.gw-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #2e7d32;
    color: #fff;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    z-index: 9999;
    display: none;
    box-shadow: 0 4px 16px rgba(0,0,0,.15);
    animation: slideIn .3s ease;
}

@keyframes slideIn {
    from { transform: translateX(100px); opacity: 0; }
    to   { transform: translateX(0);     opacity: 1; }
}

@media(max-width: 900px) {
    .gateway-grid { grid-template-columns: 1fr; }
}
</style>

<div class="gateway-page">
    <h1 class="page-title">Courier Management</h1>

    {{-- Success Toasts --}}
    @if(session('steadfast_success'))
        <div class="gw-toast" style="display:block;">✓ {{ session('steadfast_success') }}</div>
    @endif
    @if(session('pathao_success'))
        <div class="gw-toast" style="display:block;">✓ {{ session('pathao_success') }}</div>
    @endif

    {{-- ── STEADFAST ── --}}
    <div class="gw-card" style="margin-bottom:20px;">
        <div class="gw-card-header">
            <h3>Steadfast Courier</h3>
        </div>
        <form action="{{ route('admin.steadfast.update') }}" method="POST">
            @csrf
            <div class="gw-card-body">
                <div class="gateway-grid">
                    <div class="gw-form-group">
                        <label>API key <span class="req">*</span></label>
                        <input type="text" name="api_key" class="gw-form-control"
                            value="{{ $steadfast->api_key ?? '' }}" placeholder="Enter API Key" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Secret key <span class="req">*</span></label>
                        <input type="text" name="secret_key" class="gw-form-control"
                            value="{{ $steadfast->secret_key ?? '' }}" placeholder="Enter Secret Key" required>
                    </div>
                    <div class="gw-form-group">
                        <label>URL <span class="req">*</span></label>
                        <input type="text" name="url" class="gw-form-control"
                            value="{{ $steadfast->url ?? 'https://portal.steadfast.com.bd/api/v1/create_order' }}" required>
                    </div>
                    <div class="gw-form-group" style="display:flex;align-items:center;gap:14px;margin-top:24px;">
                        <label style="margin:0;">Status</label>
                        <label class="toggle-switch">
                            <input type="checkbox" name="status" value="1" {{ $steadfast && $steadfast->status ? 'checked' : '' }}>
                            <span class="toggle-slider" style="background:#26c6a6;"></span>
                        </label>
                    </div>
                </div>
            </div>
            <div style="padding:0 24px 20px;">
                <button type="submit" class="btn-submit-teal">Submit</button>
            </div>
        </form>
    </div>

    {{-- ── PATHAO ── --}}
    <div class="gw-card" style="margin-bottom:20px;">
        <div class="gw-card-header">
            <h3>Pathao Courier — Add Settings</h3>
        </div>
        <form action="{{ route('admin.pathao.update') }}" method="POST">
            @csrf
            <div class="gw-card-body">
                <div class="gw-alert-info">
                    <span class="info-icon">ℹ</span>
                    Sandbox credentials দিয়ে আগে test করুন। Live এ যেতে base_url পরিবর্তন করুন এবং merchant account এর real credentials দিন।
                </div>
                <div class="gw-form-group">
                    <label>Base URL <span class="req">*</span></label>
                    <input type="text" name="base_url" class="gw-form-control"
                        value="{{ $pathao->base_url ?? 'https://courier-api-sandbox.pathao.com' }}" required>
                    <small class="pathao-hint">Sandbox: https://courier-api-sandbox.pathao.com &nbsp;|&nbsp; Live: https://api-hermes.pathao.com</small>
                </div>
                <div class="gateway-grid">
                    <div class="gw-form-group">
                        <label>Client ID <span class="req">*</span></label>
                        <input type="text" name="client_id" class="gw-form-control"
                            value="{{ $pathao->client_id ?? '' }}" required>
                        <small class="pathao-hint">Pathao merchant dashboard থেকে পাবেন</small>
                    </div>
                    <div class="gw-form-group">
                        <label>Client Secret <span class="req">*</span></label>
                        <input type="text" name="client_secret" class="gw-form-control"
                            value="{{ $pathao->client_secret ?? '' }}" required>
                    </div>
                    <div class="gw-form-group">
                        <label>Username <span class="req">*</span></label>
                        <input type="email" name="username" class="gw-form-control"
                            value="{{ $pathao->username ?? '' }}" placeholder="test@pathao.com" required>
                        <small class="pathao-hint">Pathao merchant account এর email</small>
                    </div>
                    <div class="gw-form-group">
                        <label>Password <span class="req">*</span></label>
                        <input type="password" name="password" class="gw-form-control"
                            value="{{ $pathao->password ?? '' }}" required>
                        <small class="pathao-hint">Pathao merchant account এর password</small>
                    </div>
                    <div class="gw-form-group">
                        <label>Grant Type <span class="req">*</span></label>
                        <input type="text" name="grant_type" class="gw-form-control"
                            value="{{ $pathao->grant_type ?? 'password' }}">
                        <small class="pathao-hint">সাধারণত password হয়</small>
                    </div>
                    <div class="gw-form-group" style="display:flex;align-items:center;gap:14px;margin-top:24px;">
                        <label style="margin:0;">Status</label>
                        <label class="toggle-switch">
                            <input type="checkbox" name="status" value="1" {{ $pathao && $pathao->status ? 'checked' : '' }}>
                            <span class="toggle-slider" style="background:#26c6a6;"></span>
                        </label>
                        <span style="font-size:13px;color:#444;">Active</span>
                    </div>
                </div>
            </div>
            <div style="padding:0 24px 20px;display:flex;gap:12px;">
                <button type="submit" class="btn-submit-teal">💾 Save Settings</button>
            </div>
        </form>
    </div>
</div>

<script>
    setTimeout(function(){
        var toasts = document.querySelectorAll('.gw-toast');
        toasts.forEach(t => t.style.display='none');
    }, 4000);
</script>
@endsection
