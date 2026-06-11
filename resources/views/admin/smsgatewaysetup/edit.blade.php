@extends('admin.master')

@section('content')

<style>
/* ── SMS Configuration Page Styles ─────────────────────────── */
.sms-config-container {
    padding: 24px;
}

.sms-config-card {
    background: #ffffff;
    border: 1px solid #e8e8e8;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    margin-bottom: 24px;
}

.sms-config-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e8e8e8;
}

.sms-config-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: #1a1a2e;
    margin: 0;
}

.btn-back {
    display: inline-flex; align-items: center; gap: 6px;
    background: #f3f4f6; color: #4b5563 !important;
    border: 1px solid #d1d5db; border-radius: 4px;
    padding: 6px 12px; font-size: 13px; font-weight: 500;
    text-decoration: none; transition: all .2s ease;
}

.btn-back:hover {
    background: #e5e7eb;
}

.sms-config-body {
    padding: 24px;
}

/* ── Form Row Layout ───────────────────────────────────────── */
.sms-form-row {
    display: grid;
    grid-template-columns: 2fr 2fr 1fr;
    gap: 20px;
    margin-bottom: 24px;
}

@media (max-width: 991px) {
    .sms-form-row {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 767px) {
    .sms-form-row {
        grid-template-columns: 1fr;
    }
}

.sms-form-group {
    display: flex;
    flex-direction: column;
}

.sms-form-group label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
}

.sms-form-group label .required-asterisk {
    color: #ef4444;
    margin-left: 2px;
}

.sms-form-control {
    width: 100%;
    height: 40px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    padding: 0 12px;
    font-size: 14px;
    color: #1f2937;
    background-color: #fff;
    outline: none;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    box-sizing: border-box;
}

.sms-form-control:focus {
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.sms-form-control::placeholder {
    color: #9ca3af;
}

/* ── Switches Layout ────────────────────────────────────────── */
.sms-switches-row {
    display: flex;
    flex-wrap: wrap;
    gap: 48px;
    margin-bottom: 28px;
}

@media (max-width: 767px) {
    .sms-switches-row {
        gap: 24px;
    }
}

.sms-switch-group {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
}

.sms-switch-label {
    font-size: 14px;
    font-weight: 500;
    color: #4b5563;
}

/* Custom Sliding Switch style */
.sms-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.sms-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.sms-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: .3s;
    border-radius: 24px;
}

.sms-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}

.sms-switch input:checked + .sms-slider {
    background-color: #10b981;
}

.sms-switch input:checked + .sms-slider:before {
    transform: translateX(26px);
}

/* ── Submit Button ──────────────────────────────────────────── */
.sms-submit-container {
    display: flex;
    justify-content: flex-start;
}

.sms-btn-submit {
    background-color: #0faf96;
    color: #ffffff;
    font-size: 15px;
    font-weight: 500;
    border: none;
    border-radius: 4px;
    padding: 10px 24px;
    cursor: pointer;
    transition: background-color 0.2s ease-in-out;
}

.sms-btn-submit:hover {
    background-color: #0d9480;
}

.sms-btn-submit:active {
    background-color: #0b806e;
}
</style>

<div class="sms-config-container">
    <div class="sms-config-card">
        <div class="sms-config-header">
            <h3>SMS Gateway</h3>
            <a href="{{ route('admin.smsgatewaysetup.index') }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="sms-config-body">
            <form action="{{ route('admin.smsgatewaysetup.update', $gateway->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- URL, API Key, Sender ID Row --}}
                <div class="sms-form-row">
                    <div class="sms-form-group">
                        <label for="url">Url <span class="required-asterisk">*</span></label>
                        <input type="text" 
                               id="url" 
                               name="url" 
                               class="sms-form-control" 
                               placeholder="https://msg.elitbuzz-bd.com/smsapi" 
                               value="{{ old('url', $gateway->url) }}" 
                               required>
                    </div>

                    <div class="sms-form-group">
                        <label for="api_key">API Key <span class="required-asterisk">*</span></label>
                        <input type="text" 
                               id="api_key" 
                               name="api_key" 
                               class="sms-form-control" 
                               placeholder="Your API Key" 
                               value="{{ old('api_key', $gateway->api_key) }}" 
                               required>
                    </div>

                    <div class="sms-form-group">
                        <label for="sender_id">Senderid <span class="required-asterisk">*</span></label>
                        <input type="text" 
                               id="sender_id" 
                               name="sender_id" 
                               class="sms-form-control" 
                               placeholder="Sender ID" 
                               value="{{ old('sender_id', $gateway->sender_id) }}" 
                               required>
                    </div>
                </div>

                {{-- Switches Row --}}
                <div class="sms-switches-row">
                    <div class="sms-switch-group">
                        <span class="sms-switch-label">Status</span>
                        <label class="sms-switch">
                            <input type="checkbox" name="status" value="1" {{ old('status', $gateway->status) ? 'checked' : '' }}>
                            <span class="sms-slider"></span>
                        </label>
                    </div>

                    <div class="sms-switch-group">
                        <span class="sms-switch-label">Order confirm</span>
                        <label class="sms-switch">
                            <input type="checkbox" name="order_confirm" value="1" {{ old('order_confirm', $gateway->order_confirm) ? 'checked' : '' }}>
                            <span class="sms-slider"></span>
                        </label>
                    </div>

                    <div class="sms-switch-group">
                        <span class="sms-switch-label">Forgot password</span>
                        <label class="sms-switch">
                            <input type="checkbox" name="forgot_password" value="1" {{ old('forgot_password', $gateway->forgot_password) ? 'checked' : '' }}>
                            <span class="sms-slider"></span>
                        </label>
                    </div>

                    <div class="sms-switch-group">
                        <span class="sms-switch-label">Password Generator</span>
                        <label class="sms-switch">
                            <input type="checkbox" name="password_generator" value="1" {{ old('password_generator', $gateway->password_generator) ? 'checked' : '' }}>
                            <span class="sms-slider"></span>
                        </label>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="sms-submit-container">
                    <button type="submit" class="sms-btn-submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
