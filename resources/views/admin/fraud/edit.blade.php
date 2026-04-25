@extends('admin.master')
@section('title', 'Edit ' . $fraudCheck->check_id)

@section('content')
<style>
:root { --fc-primary:#0a0e1a; --fc-card:#1a2235; --fc-border:rgba(255,255,255,.07); --fc-accent:#6366f1; --fc-success:#10b981; --fc-warning:#f59e0b; --fc-danger:#ef4444; --fc-muted:#64748b; --fc-text:#f1f5f9; }
body { background:var(--fc-primary); color:var(--fc-text); }
.fc-wrap { max-width:720px; margin:2rem auto; padding:0 1rem; }
.fc-card { background:var(--fc-card); border:1px solid var(--fc-border); border-radius:12px; overflow:hidden; margin-bottom:1.5rem; }
.fc-card-hdr { padding:1rem 1.5rem; border-bottom:1px solid var(--fc-border); font-size:.8rem; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:var(--fc-muted); }
.fc-card-body { padding:1.5rem; }
.fc-label { display:block; font-size:.72rem; font-weight:600; text-transform:uppercase; letter-spacing:.08em; color:var(--fc-muted); margin-bottom:6px; }
.fc-label span { color:var(--fc-danger); }
.fc-control { width:100%; background:rgba(255,255,255,.04); border:1px solid var(--fc-border); color:var(--fc-text); padding:10px 14px; border-radius:8px; font-size:.82rem; outline:none; transition:border-color .2s, box-shadow .2s; }
.fc-control:focus { border-color:var(--fc-accent); box-shadow:0 0 0 3px rgba(99,102,241,.15); }
.fc-hint { font-size:.68rem; color:var(--fc-muted); margin-top:4px; }
.fc-submit { width:100%; padding:14px; background:var(--fc-accent); color:#fff; border:none; border-radius:10px; font-size:.9rem; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:all .2s; }
.fc-submit:hover { background:#4f46e5; transform:translateY(-1px); }
.btn-ghost { display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,.05); border:1px solid var(--fc-border); color:var(--fc-text); padding:8px 14px; border-radius:8px; font-size:.78rem; text-decoration:none; }
.btn-ghost:hover { background:rgba(255,255,255,.1); color:var(--fc-text); }

.risk-summary { display:flex; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap; }
.risk-card    { background:var(--fc-card); border:1px solid var(--fc-border); border-radius:10px; padding:.85rem 1.25rem; flex:1; min-width:130px; }
.risk-card .label { font-size:.65rem; color:var(--fc-muted); text-transform:uppercase; letter-spacing:.08em; margin-bottom:4px; }
.risk-card .value { font-size:1.1rem; font-weight:700; }
</style>

<div class="fc-wrap">

    <div style="margin-bottom:2rem">
        <div style="margin-bottom:.75rem">
            <a href="{{ route('admin.fraud.show', $fraudCheck) }}" class="btn-ghost">← Back to {{ $fraudCheck->check_id }}</a>
        </div>
        <div style="font-size:1.3rem;font-weight:800;letter-spacing:-.02em">
            Edit <span style="color:var(--fc-accent)">{{ $fraudCheck->check_id }}</span>
        </div>
        <div style="font-size:.78rem;color:var(--fc-muted);margin-top:4px">Update the decision and investigation notes.</div>
    </div>

    {{-- Quick Summary --}}
    <div class="risk-summary">
        @php
            $sc = $fraudCheck->risk_score;
            $color = $sc >= 80 ? 'var(--fc-danger)' : ($sc >= 60 ? 'var(--fc-warning)' : ($sc >= 40 ? '#f59e0b' : 'var(--fc-success)'));
        @endphp
        <div class="risk-card">
            <div class="label">Risk Score</div>
            <div class="value" style="color:{{ $color }}">{{ $sc }}/100</div>
        </div>
        <div class="risk-card">
            <div class="label">Risk Level</div>
            <div class="value">{{ strtoupper($fraudCheck->risk_level) }}</div>
        </div>
        <div class="risk-card">
            <div class="label">Current Status</div>
            <div class="value">{{ ucfirst($fraudCheck->status) }}</div>
        </div>
        <div class="risk-card">
            <div class="label">Flags</div>
            <div class="value">{{ count($fraudCheck->flags ?? []) }}</div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.fraud.update', $fraudCheck) }}">
        @csrf @method('PUT')

        <div class="fc-card">
            <div class="fc-card-hdr">Decision</div>
            <div class="fc-card-body">
                <div style="margin-bottom:1.25rem">
                    <label class="fc-label">Status <span>*</span></label>
                    <select name="status" class="fc-control">
                        @foreach(['approved'=>'✓ Approved','review'=>'⏳ In Review','declined'=>'✗ Declined','pending'=>'… Pending'] as $val => $label)
                            <option value="{{ $val }}" {{ (old('status',$fraudCheck->status)) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="fc-hint">Changing status will mark you as the reviewer and timestamp the review.</div>
                </div>

                <div>
                    <label class="fc-label">Investigation Notes</label>
                    <textarea name="notes" class="fc-control" rows="5"
                        placeholder="Add investigation notes, evidence, or reasoning…">{{ old('notes', $fraudCheck->notes) }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="fc-submit">
            <i class="fas fa-save"></i> Update Decision
        </button>
    </form>
</div>
@endsection
