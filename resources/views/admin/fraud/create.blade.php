@extends('admin.master')
@section('title', 'New Fraud Check')

@section('content')
<style>
:root { --fc-primary:#0a0e1a; --fc-card:#1a2235; --fc-border:rgba(255,255,255,.07); --fc-accent:#6366f1; --fc-success:#10b981; --fc-warning:#f59e0b; --fc-danger:#ef4444; --fc-muted:#64748b; --fc-text:#f1f5f9; }
body { background:var(--fc-primary); color:var(--fc-text); }

.fc-wrap { max-width:860px; margin:2rem auto; padding:0 1rem; }
.fc-card  { background:var(--fc-card); border:1px solid var(--fc-border); border-radius:12px; overflow:hidden; margin-bottom:1.5rem; }
.fc-card-header { padding:1rem 1.5rem; border-bottom:1px solid var(--fc-border); font-size:.8rem; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:var(--fc-muted); display:flex; align-items:center; gap:8px; }
.fc-card-body   { padding:1.5rem; }

.fc-grid   { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
.fc-grid.three { grid-template-columns:1fr 1fr 1fr; }
.fc-full   { grid-column:1/-1; }

.fc-label  { display:block; font-size:.72rem; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:var(--fc-muted); margin-bottom:6px; }
.fc-label span { color:var(--fc-danger); }

.fc-control { width:100%; background:rgba(255,255,255,.04); border:1px solid var(--fc-border); color:var(--fc-text); padding:10px 14px; border-radius:8px; font-size:.82rem; outline:none; transition:border-color .2s,box-shadow .2s; }
.fc-control:focus    { border-color:var(--fc-accent); box-shadow:0 0 0 3px rgba(99,102,241,.15); }
.fc-control.invalid  { border-color:var(--fc-danger); }
.invalid-feedback    { color:var(--fc-danger); font-size:.7rem; margin-top:4px; display:block; }
.fc-hint   { font-size:.68rem; color:var(--fc-muted); margin-top:4px; }

.fc-submit { width:100%; padding:14px; background:var(--fc-accent); color:#fff; border:none; border-radius:10px; font-size:.9rem; font-weight:700; cursor:pointer; transition:all .2s; display:flex; align-items:center; justify-content:center; gap:8px; }
.fc-submit:hover { background:#4f46e5; box-shadow:0 0 20px rgba(99,102,241,.4); transform:translateY(-1px); }

.btn-ghost { display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,.05); border:1px solid var(--fc-border); color:var(--fc-text); padding:8px 14px; border-radius:8px; font-size:.78rem; font-weight:500; text-decoration:none; }
.btn-ghost:hover { background:rgba(255,255,255,.1); color:var(--fc-text); }
</style>

<div class="fc-wrap">

    <div style="margin-bottom:2rem">
        <div style="margin-bottom:.75rem">
            <a href="{{ route('admin.fraud.index') }}" class="btn-ghost">← Back to Checks</a>
        </div>
        <div style="font-size:1.3rem;font-weight:800;letter-spacing:-.02em">
            New <span style="color:var(--fc-accent)">Fraud</span> Check
        </div>
        <div style="font-size:.78rem;color:var(--fc-muted);margin-top:4px">
            Run the fraud detection engine on a customer or transaction.
        </div>
    </div>

    @if($errors->any())
        <div style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:var(--fc-danger);padding:1rem 1.25rem;border-radius:8px;margin-bottom:1.5rem;font-size:.82rem;">
            <strong><i class="fas fa-exclamation-circle me-2"></i>Please fix the errors below:</strong>
            <ul style="margin:.5rem 0 0;padding-left:1.5rem">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.fraud.store') }}">
        @csrf

        {{-- Check Type --}}
        <div class="fc-card">
            <div class="fc-card-header"><i class="fas fa-tag" style="color:var(--fc-accent)"></i> Check Type</div>
            <div class="fc-card-body">
                <label class="fc-label">Type <span>*</span></label>
                <select name="type" class="fc-control @error('type') invalid @enderror">
                    <option value="">Select type…</option>
                    @foreach($types as $val => $label)
                        <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('type')<span class="invalid-feedback">{{ $message }}</span>@enderror
                <div class="fc-hint">Select the primary subject of this fraud check.</div>
            </div>
        </div>

        {{-- Customer Info --}}
        <div class="fc-card">
            <div class="fc-card-header"><i class="fas fa-user" style="color:var(--fc-accent)"></i> Customer Information</div>
            <div class="fc-card-body">
                <div class="fc-grid">
                    <div class="fc-full">
                        <label class="fc-label">Full Name</label>
                        <input type="text" name="customer_name" class="fc-control @error('customer_name') invalid @enderror"
                            placeholder="John Doe" value="{{ old('customer_name') }}">
                        @error('customer_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="fc-label">Email Address</label>
                        <input type="email" name="customer_email" class="fc-control @error('customer_email') invalid @enderror"
                            placeholder="john@example.com" value="{{ old('customer_email') }}">
                        @error('customer_email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="fc-label">Phone Number</label>
                        <input type="text" name="customer_phone" class="fc-control @error('customer_phone') invalid @enderror"
                            placeholder="+8801XXXXXXXXX" value="{{ old('customer_phone') }}">
                        @error('customer_phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="fc-label">IP Address</label>
                        <input type="text" name="ip_address" class="fc-control @error('ip_address') invalid @enderror"
                            placeholder="192.168.1.1" value="{{ old('ip_address') }}">
                        @error('ip_address')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="fc-label">Input Value</label>
                        <input type="text" name="input_value" class="fc-control @error('input_value') invalid @enderror"
                            placeholder="Primary value being checked" value="{{ old('input_value') }}">
                        @error('input_value')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Transaction --}}
        <div class="fc-card">
            <div class="fc-card-header"><i class="fas fa-money-bill-wave" style="color:var(--fc-accent)"></i> Transaction (optional)</div>
            <div class="fc-card-body">
                <div class="fc-grid">
                    <div>
                        <label class="fc-label">Amount</label>
                        <input type="number" name="transaction_amount" class="fc-control @error('transaction_amount') invalid @enderror"
                            placeholder="0.00" step="0.01" min="0" value="{{ old('transaction_amount') }}">
                        @error('transaction_amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="fc-label">Currency</label>
                        <select name="transaction_currency" class="fc-control">
                            @foreach(['BDT'=>'BDT – Bangladeshi Taka','USD'=>'USD – US Dollar','EUR'=>'EUR – Euro','GBP'=>'GBP – British Pound'] as $val => $label)
                                <option value="{{ $val }}" {{ old('transaction_currency','BDT') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Device --}}
        <div class="fc-card">
            <div class="fc-card-header"><i class="fas fa-laptop" style="color:var(--fc-accent)"></i> Device (optional)</div>
            <div class="fc-card-body">
                <div class="fc-grid three">
                    <div>
                        <label class="fc-label">Device Type</label>
                        <select name="device_type" class="fc-control">
                            <option value="">Unknown</option>
                            <option value="desktop"  {{ old('device_type') === 'desktop'  ? 'selected' : '' }}>Desktop</option>
                            <option value="mobile"   {{ old('device_type') === 'mobile'   ? 'selected' : '' }}>Mobile</option>
                            <option value="tablet"   {{ old('device_type') === 'tablet'   ? 'selected' : '' }}>Tablet</option>
                        </select>
                    </div>
                    <div>
                        <label class="fc-label">Browser</label>
                        <input type="text" name="browser" class="fc-control" placeholder="Chrome, Firefox…" value="{{ old('browser') }}">
                    </div>
                    <div>
                        <label class="fc-label">Operating System</label>
                        <input type="text" name="os" class="fc-control" placeholder="Windows, macOS…" value="{{ old('os') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="fc-card">
            <div class="fc-card-header"><i class="fas fa-sticky-note" style="color:var(--fc-accent)"></i> Notes</div>
            <div class="fc-card-body">
                <textarea name="notes" class="fc-control" rows="3"
                    placeholder="Any additional context…">{{ old('notes') }}</textarea>
            </div>
        </div>

        <button type="submit" class="fc-submit">
            <i class="fas fa-search-plus"></i> Run Fraud Check
        </button>
    </form>
</div>
@endsection
