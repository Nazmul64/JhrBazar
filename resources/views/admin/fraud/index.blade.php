@extends('admin.master')
@section('title', 'Fraud Checks')

@section('content')
<style>
:root {
    --fc-primary:#0a0e1a; --fc-card:#1a2235; --fc-border:rgba(255,255,255,0.07);
    --fc-accent:#6366f1;  --fc-success:#10b981; --fc-warning:#f59e0b;
    --fc-danger:#ef4444;  --fc-muted:#64748b;   --fc-text:#f1f5f9;
}
body { background:var(--fc-primary); color:var(--fc-text); }

.fc-header { padding:1.5rem 2rem; border-bottom:1px solid var(--fc-border); display:flex; align-items:center; justify-content:space-between; }
.fc-title  { font-size:1.2rem; font-weight:700; }
.fc-sub    { font-size:.72rem; color:var(--fc-muted); margin-top:3px; }

.fc-filters { background:var(--fc-card); border-bottom:1px solid var(--fc-border); padding:1rem 2rem; display:flex; gap:.75rem; flex-wrap:wrap; align-items:center; }

.fc-select, .fc-input {
    background:rgba(255,255,255,0.05); border:1px solid var(--fc-border);
    color:var(--fc-text); padding:6px 12px; border-radius:8px; font-size:.78rem; outline:none;
}
.fc-select:focus, .fc-input:focus { border-color:var(--fc-accent); }
.fc-input { min-width:220px; }

.fc-table-wrap { padding:1.5rem 2rem; }
.fc-table { width:100%; border-collapse:collapse; font-size:.8rem; }
.fc-table thead th {
    background:var(--fc-card); color:var(--fc-muted); text-transform:uppercase;
    font-size:.65rem; letter-spacing:.1em; padding:.75rem 1rem;
    text-align:left; border-bottom:1px solid var(--fc-border); white-space:nowrap;
}
.fc-table tbody tr  { border-bottom:1px solid var(--fc-border); transition:background .15s; }
.fc-table tbody tr:hover { background:rgba(255,255,255,0.02); }
.fc-table tbody td  { padding:.85rem 1rem; vertical-align:middle; }

.badge-status { display:inline-block; font-size:.63rem; font-weight:700; padding:3px 9px; border-radius:4px; text-transform:uppercase; letter-spacing:.07em; }
.badge-status.approved { background:rgba(16,185,129,.15); color:var(--fc-success); border:1px solid rgba(16,185,129,.3); }
.badge-status.review   { background:rgba(245,158,11,.15); color:var(--fc-warning); border:1px solid rgba(245,158,11,.3); }
.badge-status.declined { background:rgba(239,68,68,.15);  color:var(--fc-danger);  border:1px solid rgba(239,68,68,.3); }
.badge-status.pending  { background:rgba(56,189,248,.15); color:#38bdf8;           border:1px solid rgba(56,189,248,.3); }

.badge-risk { display:inline-block; font-size:.63rem; font-weight:700; padding:3px 9px; border-radius:4px; text-transform:uppercase; }
.badge-risk.low      { background:rgba(16,185,129,.15); color:var(--fc-success); }
.badge-risk.medium   { background:rgba(245,158,11,.15); color:var(--fc-warning); }
.badge-risk.high     { background:rgba(239,68,68,.15);  color:var(--fc-danger); }
.badge-risk.critical { background:rgba(220,38,38,.25);  color:#fca5a5; }

.score-pill   { display:inline-flex; align-items:center; gap:6px; font-weight:700; }
.score-bar    { width:40px; height:4px; background:rgba(255,255,255,.08); border-radius:2px; overflow:hidden; }
.score-fill   { height:100%; border-radius:2px; }

.flag-chip { display:inline-block; background:rgba(239,68,68,.1); color:#fca5a5; border:1px solid rgba(239,68,68,.2); font-size:.6rem; padding:1px 6px; border-radius:3px; margin:1px; }

.action-btn {
    display:inline-flex; align-items:center; justify-content:center;
    width:28px; height:28px; border-radius:6px; font-size:.75rem;
    text-decoration:none; transition:all .15s; border:1px solid var(--fc-border);
    background:transparent; color:var(--fc-muted); cursor:pointer;
}
.action-btn:hover       { background:rgba(255,255,255,.08); color:var(--fc-text); }
.action-btn.danger:hover{ background:rgba(239,68,68,.15); border-color:var(--fc-danger); color:var(--fc-danger); }

.btn-primary { display:inline-flex; align-items:center; gap:6px; background:var(--fc-accent); color:#fff; padding:8px 16px; border-radius:8px; font-size:.78rem; font-weight:600; text-decoration:none; border:none; cursor:pointer; transition:all .2s; }
.btn-primary:hover { background:#4f46e5; color:#fff; }
.btn-ghost   { display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,.05); border:1px solid var(--fc-border); color:var(--fc-text); padding:8px 14px; border-radius:8px; font-size:.78rem; font-weight:500; text-decoration:none; cursor:pointer; transition:all .2s; }
.btn-ghost:hover { background:rgba(255,255,255,.1); color:var(--fc-text); }

.check-id  { font-family:monospace; font-weight:700; color:var(--fc-accent); font-size:.78rem; }
.empty-state { text-align:center; padding:4rem; color:var(--fc-muted); }
.alert-success { background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.3); color:var(--fc-success); padding:.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:.82rem; }
</style>

{{-- Header --}}
<div class="fc-header">
    <div>
        <div class="fc-title"><i class="fas fa-shield-alt me-2" style="color:var(--fc-accent)"></i>Fraud Checks</div>
        <div class="fc-sub">{{ number_format($stats['total']) }} total · {{ number_format($stats['high_risk']) }} high risk · avg score {{ $stats['avg_score'] }}</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.fraud.export') }}" class="btn-ghost"><i class="fas fa-download"></i> Export CSV</a>
        <a href="{{ route('admin.fraud.create') }}" class="btn-primary"><i class="fas fa-plus"></i> New Check</a>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="fc-filters">
    <input type="text" name="search" class="fc-input" placeholder="🔍  ID, name, email, IP…" value="{{ request('search') }}">

    <select name="status" class="fc-select" onchange="this.form.submit()">
        <option value="">All Status</option>
        @foreach(['approved'=>'Approved','review'=>'In Review','declined'=>'Declined','pending'=>'Pending'] as $val => $label)
        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>

    <select name="risk_level" class="fc-select" onchange="this.form.submit()">
        <option value="">All Risk Levels</option>
        @foreach(['critical'=>'Critical','high'=>'High','medium'=>'Medium','low'=>'Low'] as $val => $label)
        <option value="{{ $val }}" {{ request('risk_level') === $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>

    <select name="type" class="fc-select" onchange="this.form.submit()">
        <option value="">All Types</option>
        @foreach(['identity'=>'Identity','email'=>'Email','phone'=>'Phone','ip'=>'IP','transaction'=>'Transaction'] as $val => $label)
        <option value="{{ $val }}" {{ request('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>

    <input type="date" name="date_from" class="fc-input" style="min-width:auto" value="{{ request('date_from') }}" onchange="this.form.submit()">
    <input type="date" name="date_to"   class="fc-input" style="min-width:auto" value="{{ request('date_to') }}"   onchange="this.form.submit()">

    @if(request()->hasAny(['search','status','risk_level','type','date_from','date_to']))
        <a href="{{ route('admin.fraud.index') }}" class="btn-ghost" style="padding:6px 12px;font-size:.72rem"><i class="fas fa-times"></i> Clear</a>
        <button type="submit" class="btn-primary" style="padding:6px 14px;font-size:.72rem"><i class="fas fa-search"></i> Search</button>
    @else
        <button type="submit" class="btn-primary" style="padding:6px 14px;font-size:.72rem"><i class="fas fa-search"></i> Search</button>
    @endif
</form>

{{-- Table --}}
<div class="fc-table-wrap">

    @if(session('success'))
        <div class="alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif

    <form id="bulk-form" method="POST" action="{{ route('admin.fraud.bulk-action') }}">
        @csrf

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem">
            <div style="font-size:.75rem;color:var(--fc-muted)">
                Showing {{ $checks->firstItem() }}–{{ $checks->lastItem() }} of {{ $checks->total() }} results
            </div>
            <div class="d-flex gap-2 align-items-center" id="bulk-actions" style="display:none!important">
                <span style="font-size:.72rem;color:var(--fc-muted)" id="selected-count"></span>
                <select name="action" class="fc-select">
                    <option value="approve">Approve Selected</option>
                    <option value="decline">Decline Selected</option>
                    <option value="delete">Delete Selected</option>
                </select>
                <button type="submit" class="btn-ghost" style="padding:6px 12px;font-size:.72rem"
                    onclick="return confirm('Apply bulk action?')">Apply</button>
            </div>
        </div>

        <table class="fc-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all" style="accent-color:var(--fc-accent)"></th>
                    <th>Check ID</th>
                    <th>Type</th>
                    <th>Customer</th>
                    <th>IP / Country</th>
                    <th>Risk Score</th>
                    <th>Risk Level</th>
                    <th>Status</th>
                    <th>Flags</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($checks as $check)
                <tr>
                    <td><input type="checkbox" name="ids[]" value="{{ $check->id }}" class="row-check" style="accent-color:var(--fc-accent)"></td>
                    <td><span class="check-id">{{ $check->check_id }}</span></td>
                    <td><span style="font-size:.7rem;text-transform:uppercase;letter-spacing:.08em;color:var(--fc-muted)">{{ $check->type }}</span></td>
                    <td>
                        <div style="font-size:.8rem;font-weight:500">{{ $check->customer_name ?: '—' }}</div>
                        <div style="font-size:.68rem;color:var(--fc-muted)">{{ $check->customer_email ?: $check->input_value }}</div>
                    </td>
                    <td>
                        <div style="font-size:.78rem;font-family:monospace">{{ $check->ip_address ?: '—' }}</div>
                        <div style="font-size:.68rem;color:var(--fc-muted)">{{ $check->country ?: '—' }}</div>
                    </td>
                    <td>
                        @php
                            $color = $check->risk_score >= 80 ? 'var(--fc-danger)' : ($check->risk_score >= 60 ? 'var(--fc-warning)' : ($check->risk_score >= 40 ? '#f59e0b' : 'var(--fc-success)'));
                        @endphp
                        <div class="score-pill">
                            <span style="color:{{ $color }};font-size:.8rem">{{ $check->risk_score }}</span>
                            <div class="score-bar">
                                <div class="score-fill" style="width:{{ $check->risk_score }}%;background:{{ $color }}"></div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge-risk {{ $check->risk_level }}">{{ strtoupper($check->risk_level) }}</span></td>
                    <td><span class="badge-status {{ $check->status }}">{{ ucfirst($check->status) }}</span></td>
                    <td>
                        @if(!empty($check->flags))
                            @foreach(array_slice($check->flags, 0, 2) as $flag)
                                <span class="flag-chip">{{ str_replace('_',' ',$flag) }}</span>
                            @endforeach
                            @if(count($check->flags) > 2)
                                <span class="flag-chip">+{{ count($check->flags)-2 }}</span>
                            @endif
                        @else
                            <span style="color:var(--fc-muted);font-size:.72rem">none</span>
                        @endif
                    </td>
                    <td style="font-size:.72rem;color:var(--fc-muted);white-space:nowrap">
                        {{ $check->created_at->format('d M Y') }}<br>
                        <span style="font-size:.65rem">{{ $check->created_at->format('H:i') }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.fraud.show', $check) }}" class="action-btn" title="View"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.fraud.edit', $check) }}" class="action-btn" title="Edit"><i class="fas fa-edit"></i></a>
                            <form method="POST" action="{{ route('admin.fraud.destroy', $check) }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn danger" title="Delete"
                                    onclick="return confirm('Delete this check?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11">
                        <div class="empty-state">
                            <i class="fas fa-search" style="color:var(--fc-accent)"></i>
                            <div>No fraud checks found</div>
                            <div style="font-size:.78rem;margin-top:.5rem">
                                <a href="{{ route('admin.fraud.create') }}" style="color:var(--fc-accent)">Run your first check</a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </form>

    <div style="margin-top:1.5rem">{{ $checks->links() }}</div>
</div>

<script>
document.getElementById('select-all').addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
    toggleBulk();
});
document.querySelectorAll('.row-check').forEach(cb => cb.addEventListener('change', toggleBulk));

function toggleBulk() {
    const checked = document.querySelectorAll('.row-check:checked');
    const bulk    = document.getElementById('bulk-actions');
    const counter = document.getElementById('selected-count');
    bulk.style.display = checked.length ? 'flex' : 'none';
    counter.textContent = checked.length + ' selected';
}
</script>
@endsection
