@extends('admin.master')

@section('content')

<style>
/* ── Page Wrapper ───────────────────────────────────── */
.sms-config-wrapper {
    padding: 24px;
    background: #f8f9fa;
    min-height: 100vh;
}

/* ── Page Header ────────────────────────────────────── */
.sms-page-title {
    font-size: 22px;
    font-weight: 600;
    color: #1a1a2e;
    margin-bottom: 4px;
}

.sms-notice-badge {
    display: inline-block;
    background: #6c2bd9;
    color: #fff;
    font-size: 11px;
    font-weight: 500;
    padding: 3px 12px;
    border-radius: 4px;
    margin-bottom: 24px;
}

/* ── Grid Layout ────────────────────────────────────── */
.sms-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

@media (max-width: 900px) {
    .sms-grid { grid-template-columns: 1fr; }
}

/* ── Card ────────────────────────────────────────────── */
.sms-card {
    background: #fff;
    border-radius: 4px;
    border: 1px solid #e8e8e8;
    overflow: hidden;
}

/* ── Card Header (logo area) ─────────────────────────── */
.sms-card-header {
    padding: 20px 24px 16px;
    border-bottom: 1px solid #e8e8e8;
    display: flex;
    align-items: center;
    min-height: 72px;
}

.sms-logo-twilio {
    display: flex;
    align-items: center;
    gap: 10px;
}
.sms-logo-twilio .logo-icon {
    width: 38px;
    height: 38px;
    background: #f22f46;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.sms-logo-twilio .logo-icon svg { width: 22px; height: 22px; }
.sms-logo-twilio .logo-text {
    font-size: 26px;
    font-weight: 700;
    color: #f22f46;
    letter-spacing: -1px;
    font-family: 'Georgia', serif;
}

.sms-logo-telesign {
    display: flex;
    align-items: center;
    gap: 10px;
}
.sms-logo-telesign .logo-icon svg { width: 32px; height: 32px; }
.sms-logo-telesign .logo-text {
    font-size: 22px;
    font-weight: 600;
    color: #1a3a6b;
}

.sms-logo-nexmo {
    display: flex;
    align-items: center;
}
.sms-logo-nexmo .logo-text {
    font-size: 30px;
    font-weight: 700;
    color: #00a4d3;
    letter-spacing: -1px;
}
.sms-logo-nexmo .logo-circle {
    display: inline-block;
    width: 14px;
    height: 14px;
    border: 3px solid #00a4d3;
    border-radius: 50%;
    margin-left: 2px;
    vertical-align: middle;
    position: relative;
    top: -2px;
}

.sms-logo-messagebird {
    display: flex;
    align-items: center;
    gap: 10px;
}
.sms-logo-messagebird .logo-icon svg { width: 28px; height: 28px; }
.sms-logo-messagebird .logo-text {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
}

/* ── Card Body ───────────────────────────────────────── */
.sms-card-body {
    padding: 20px 24px 24px;
}

/* ── Active / Inactive Radio ─────────────────────────── */
.sms-radio-group {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 18px;
}

.sms-radio-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: #555;
    cursor: pointer;
    user-select: none;
}

.sms-radio-label input[type="radio"] {
    width: 16px;
    height: 16px;
    accent-color: #2563eb;
    cursor: pointer;
}

/* ── Form Input ──────────────────────────────────────── */
.sms-input {
    width: 100%;
    height: 44px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    padding: 0 14px;
    font-size: 14px;
    color: #374151;
    background: #fff;
    outline: none;
    transition: border-color 0.2s;
    margin-bottom: 12px;
    box-sizing: border-box;
}

.sms-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
}

.sms-input::placeholder { color: #9ca3af; }

/* ── Update Button ───────────────────────────────────── */
.sms-btn-update {
    background: #e91e63;
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 10px 22px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
    margin-top: 4px;
}
.sms-btn-update:hover { background: #c2185b; }

/* ── Alert ───────────────────────────────────────────── */
.sms-alert {
    padding: 10px 16px;
    border-radius: 4px;
    margin-bottom: 20px;
    font-size: 14px;
}
.sms-alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.sms-alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
</style>

<div class="sms-config-wrapper">

    {{-- ── Page Title ── --}}
    <h1 class="sms-page-title">SMS Configuration</h1>
    <span class="sms-notice-badge">You can active only one provider at a time</span>

    {{-- ── Flash Messages ── --}}
    @if(session('success'))
        <div class="sms-alert sms-alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="sms-alert sms-alert-error">{{ session('error') }}</div>
    @endif

    {{-- ══════════════════════════════════════════════════════
         2-Column Grid
    ══════════════════════════════════════════════════════ --}}
    <div class="sms-grid">

        {{-- ╔══════════════════════════════════════╗
             ║  1. TWILIO                           ║
             ╚══════════════════════════════════════╝ --}}
        <div class="sms-card" id="twilio">
            {{-- Header --}}
            <div class="sms-card-header">
                <div class="sms-logo-twilio">
                    <div class="logo-icon">
                        <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="10" cy="10" r="3" fill="white"/>
                            <circle cx="22" cy="10" r="3" fill="white"/>
                            <circle cx="10" cy="22" r="3" fill="white"/>
                            <circle cx="22" cy="22" r="3" fill="white"/>
                        </svg>
                    </div>
                    <span class="logo-text">twilio</span>
                </div>
            </div>

            {{-- Body --}}
            <div class="sms-card-body">
                {{-- Active/Inactive Toggle --}}
                <form action="{{ route('admin.twilio.toggle') }}" method="POST" id="twilio-toggle-form">
                    @csrf
                    <div class="sms-radio-group">
                        <label class="sms-radio-label">
                            <input type="radio" name="is_active" value="1"
                                   {{ optional($twilio)->is_active ? 'checked' : '' }}
                                   onchange="document.getElementById('twilio-toggle-form').submit()">
                            Active
                        </label>
                        <label class="sms-radio-label">
                            <input type="radio" name="is_active" value="0"
                                   {{ !optional($twilio)->is_active ? 'checked' : '' }}
                                   onchange="document.getElementById('twilio-toggle-form').submit()">
                            Inactive
                        </label>
                    </div>
                </form>

                {{-- Update Form --}}
                <form action="{{ route('admin.twilio.update') }}" method="POST">
                    @csrf
                    <input type="text"
                           name="twilio_sid"
                           class="sms-input"
                           placeholder="Twilio SID"
                           value="{{ optional($twilio)->twilio_sid }}">

                    <input type="text"
                           name="twilio_token"
                           class="sms-input"
                           placeholder="Twilio Token"
                           value="{{ optional($twilio)->twilio_token }}">

                    <input type="text"
                           name="twilio_from"
                           class="sms-input"
                           placeholder="Twilio From"
                           value="{{ optional($twilio)->twilio_from }}">

                    <button type="submit" class="sms-btn-update">Update</button>
                </form>
            </div>
        </div>{{-- /twilio --}}


        {{-- ╔══════════════════════════════════════╗
             ║  2. TELESIGN                         ║
             ╚══════════════════════════════════════╝ --}}
        <div class="sms-card" id="telesign">
            {{-- Header --}}
            <div class="sms-card-header">
                <div class="sms-logo-telesign">
                    <div class="logo-icon">
                        {{-- Telesign spiral logo --}}
                        <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 4C11.163 4 4 11.163 4 20s7.163 16 16 16 16-7.163 16-16S28.837 4 20 4zm0 24a8 8 0 110-16 8 8 0 010 16z" fill="#1a3a6b"/>
                            <path d="M20 10c-5.523 0-10 4.477-10 10s4.477 10 10 10 10-4.477 10-10S25.523 10 20 10zm0 16a6 6 0 110-12 6 6 0 010 12z" fill="#1a3a6b" opacity="0.4"/>
                        </svg>
                    </div>
                    <span class="logo-text">telesign</span>
                </div>
            </div>

            {{-- Body --}}
            <div class="sms-card-body">
                <form action="{{ route('admin.telesign.toggle') }}" method="POST" id="telesign-toggle-form">
                    @csrf
                    <div class="sms-radio-group">
                        <label class="sms-radio-label">
                            <input type="radio" name="is_active" value="1"
                                   {{ optional($telesign)->is_active ? 'checked' : '' }}
                                   onchange="document.getElementById('telesign-toggle-form').submit()">
                            Active
                        </label>
                        <label class="sms-radio-label">
                            <input type="radio" name="is_active" value="0"
                                   {{ !optional($telesign)->is_active ? 'checked' : '' }}
                                   onchange="document.getElementById('telesign-toggle-form').submit()">
                            Inactive
                        </label>
                    </div>
                </form>

                <form action="{{ route('admin.telesign.update') }}" method="POST">
                    @csrf
                    <input type="text"
                           name="customer_id"
                           class="sms-input"
                           placeholder="Customer ID"
                           value="{{ optional($telesign)->customer_id }}">

                    <input type="text"
                           name="api_key"
                           class="sms-input"
                           placeholder="API KEY"
                           value="{{ optional($telesign)->api_key }}">

                    <button type="submit" class="sms-btn-update">Update</button>
                </form>
            </div>
        </div>{{-- /telesign --}}


        {{-- ╔══════════════════════════════════════╗
             ║  3. NEXMO                            ║
             ╚══════════════════════════════════════╝ --}}
        <div class="sms-card" id="nexmo">
            {{-- Header --}}
            <div class="sms-card-header">
                <div class="sms-logo-nexmo">
                    <span class="logo-text">nexm<span style="font-size:26px;">o</span></span>
                    <span class="logo-circle"></span>
                </div>
            </div>

            {{-- Body --}}
            <div class="sms-card-body">
                <form action="{{ route('admin.nexmo.toggle') }}" method="POST" id="nexmo-toggle-form">
                    @csrf
                    <div class="sms-radio-group">
                        <label class="sms-radio-label">
                            <input type="radio" name="is_active" value="1"
                                   {{ optional($nexmo)->is_active ? 'checked' : '' }}
                                   onchange="document.getElementById('nexmo-toggle-form').submit()">
                            Active
                        </label>
                        <label class="sms-radio-label">
                            <input type="radio" name="is_active" value="0"
                                   {{ !optional($nexmo)->is_active ? 'checked' : '' }}
                                   onchange="document.getElementById('nexmo-toggle-form').submit()">
                            Inactive
                        </label>
                    </div>
                </form>

                <form action="{{ route('admin.nexmo.update') }}" method="POST">
                    @csrf
                    <input type="text"
                           name="nexmo_key"
                           class="sms-input"
                           placeholder="Nexmo Key"
                           value="{{ optional($nexmo)->nexmo_key }}">

                    <input type="text"
                           name="nexmo_secret"
                           class="sms-input"
                           placeholder="Nexmo Secret"
                           value="{{ optional($nexmo)->nexmo_secret }}">

                    <input type="text"
                           name="nexmo_from"
                           class="sms-input"
                           placeholder="Nexmo From"
                           value="{{ optional($nexmo)->nexmo_from }}">

                    <button type="submit" class="sms-btn-update">Update</button>
                </form>
            </div>
        </div>{{-- /nexmo --}}


        {{-- ╔══════════════════════════════════════╗
             ║  4. MESSAGEBIRD                      ║
             ╚══════════════════════════════════════╝ --}}
        <div class="sms-card" id="messagebird">
            {{-- Header --}}
            <div class="sms-card-header">
                <div class="sms-logo-messagebird">
                    <div class="logo-icon">
                        {{-- MessageBird bird icon --}}
                        <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 28 C10 20, 18 14, 28 16 C22 18, 18 22, 20 28 Z" fill="#2c6ecf"/>
                            <path d="M20 28 C18 22, 22 18, 28 16 C30 20, 28 26, 22 30 Z" fill="#1a4fa0"/>
                            <circle cx="26" cy="14" r="3" fill="#2c6ecf"/>
                            <path d="M26 11 L32 8 L30 13 Z" fill="#4a90e2"/>
                        </svg>
                    </div>
                    <span class="logo-text">MessageBird</span>
                </div>
            </div>

            {{-- Body --}}
            <div class="sms-card-body">
                <form action="{{ route('admin.messagebird.toggle') }}" method="POST" id="messagebird-toggle-form">
                    @csrf
                    <div class="sms-radio-group">
                        <label class="sms-radio-label">
                            <input type="radio" name="is_active" value="1"
                                   {{ optional($messagebird)->is_active ? 'checked' : '' }}
                                   onchange="document.getElementById('messagebird-toggle-form').submit()">
                            Active
                        </label>
                        <label class="sms-radio-label">
                            <input type="radio" name="is_active" value="0"
                                   {{ !optional($messagebird)->is_active ? 'checked' : '' }}
                                   onchange="document.getElementById('messagebird-toggle-form').submit()">
                            Inactive
                        </label>
                    </div>
                </form>

                <form action="{{ route('admin.messagebird.update') }}" method="POST">
                    @csrf
                    <input type="text"
                           name="api_key"
                           class="sms-input"
                           placeholder="API Key"
                           value="{{ optional($messagebird)->api_key }}">

                    <input type="text"
                           name="from"
                           class="sms-input"
                           placeholder="From"
                           value="{{ optional($messagebird)->from }}">

                    <button type="submit" class="sms-btn-update">Update</button>
                </form>
            </div>
        </div>{{-- /messagebird --}}

    </div>{{-- /.sms-grid --}}
</div>{{-- /.sms-config-wrapper --}}

@endsection
