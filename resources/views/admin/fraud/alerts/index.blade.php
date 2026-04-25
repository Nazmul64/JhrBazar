@extends('admin.master')
@section('title', 'Fraud Alerts')

@section('content')
<style>
:root {
    --fc-primary:#0a0e1a; --fc-card:#1a2235; --fc-border:rgba(255,255,255,0.07);
    --fc-accent:#6366f1; --fc-success:#10b981; --fc-warning:#f59e0b;
    --fc-danger:#ef4444; --fc-critical:#dc2626; --fc-muted:#64748b; --fc-text:#f1f5f9;
}
body { background: var(--fc-primary); color: var(--fc-text); }

.fc-page-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--fc-border);
    display: flex; align-items: center; justify-content: space-between;
}
.fc-page-title { font-size: 1.2rem; font-weight: 700; }

.stat-mini-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: .75rem;
    padding: 1.25rem 2rem;
}
.stat-mini {
    background: var(--fc-card);
    border: 1px solid var(--fc-border);
    border-radius: 10px;
    padding: 1rem 1.25rem;
}
.stat-mini-label { font-size: .65rem; color: var(--fc-muted); text-transform: uppercase; letter-spacing: .1em; margin-bottom: .3rem; }
.stat-mini-value { font-size: 1.6rem; font-weight: 800; line-height: 1; }

.fc-filters {
    background: var(--fc-card);
    border-bottom: 1px solid var(--fc-border);
    padding: 1rem 2rem;
    display: flex; gap: .75rem; flex-wrap: wrap; align-items: center;
}
.fc-select, .fc-input {
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--fc-border);
    color: var(--fc-text);
    padding: 6px 12px;
    border-radius: 8px;
    font-size: .78rem;
    outline: none;
}
.fc-select:focus, .fc-input:focus { border-color: var(--fc-accent); }

.fc-table-wrap { padding: 1.5rem 2rem; }
.fc-table { width: 100%; border-collapse: collapse; font-size: .8rem; }
.fc-table thead th {
    background: var(--fc-card);
    color: var(--fc-muted);
    text-transform: uppercase;
    font-size: .65rem; letter-spacing: .1em;
    padding: .75rem 1rem;
    text-align: left;
    border-bottom: 1px solid var(--fc-border);
    white-space: nowrap;
}
.fc-table tbody tr { border-bottom: 1px solid var(--fc-border); transition: background .15s; }
.fc-table tbody tr:hover { background: rgba(255,255,255,0.02); }
.fc-table tbody td { padding: .85rem 1rem; vertical-align: middle; }

.alert-id { font-family:monospace; font-weight:700; color:var(--fc-accent); font-size:.78rem; }

.severity-dot {
    display: inline-block;
    width: 8px; height: 8px;
    border-radius: 50%;
    margin-right: 6px;
    flex-shrink: 0;
}
.severity-dot.critical { background: var(--fc-critical); box-shadow: 0 0 6px var(--fc-critical); }
.severity-dot.warning  { background: var(--fc-warning); }
.severity-dot.info     { background: #38bdf8; }

.badge-severity {
    display: inline-flex; align-items: center;
    font-size: .65rem; font-weight: 700;
    padding: 3px 10px;
    border-radius: 4px;
    text-transform: uppercase; letter-spacing: .07em;
}
.badge-severity.critical { background: rgba(220,38,38,.2); color: #fca5a5; border: 1px solid rgba(220,38,38,.4); }
.badge-severity.warning  { background: rgba(245,158,11,.15); color: var(--fc-warning); border: 1px solid rgba(245,158,11,.3); }
.badge-severity.info     { background: rgba(56,189,248,.15); color: #38bdf8; border: 1px solid rgba(56,189,248,.3); }

.badge-alert-status {
    display: inline-block;
    font-size: .63rem; font-weight: 700;
    padding: 3px 9px;
    border-radius: 4px;
    text-transform: uppercase;
}
.badge-alert-status.open           { background:rgba(239,68,68,.15);  color:var(--fc-danger); border:1px solid rgba(239,68,68,.3); }
.badge-alert-status.investigating  { background:rgba(245,158,11,.15); color:var(--fc-warning); border:1px solid rgba(245,158,11,.3); }
.badge-alert-status.resolved       { background:rgba(16,185,129,.15); color:var(--fc-success); border:1px solid rgba(16,185,129,.3); }
.badge-alert-status.false_positive { background:rgba(100,116,139,.15); color:var(--fc-muted); border:1px solid rgba(100,116,139,.2); }

.action-btn {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px;
    border-radius: 6px; font-size: .75rem;
    text-decoration: none; transition: all .15s;
    border: 1px solid var(--fc-border);
    background: transparent; color: var(--fc-muted);
    cursor: pointer;
}
.action-btn:hover { background: rgba(255,255,255,0.08); color: var(--fc-text); }

.btn-fc-ghost {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,.05);
    border: 1px solid var(--fc-border);
    color: var(--fc-text);
    padding: 8px 14px; border-radius: 8px;
    font-size: .78rem; font-weight: 500;
    text-decoration: none; cursor: pointer;
    transition: all .2s;
}
.btn-fc-ghost:hover { background: rgba(255,255,255,.1); color: var(--fc-text); }

.resolve-form select, .resolve-form button {
    font-size: .72rem;
    border-radius: 6px;
}
.resolve-form select {
    background: rgba(255,255,255,.05);
    border: 1px solid var(--fc-border);
    color: var(--fc-text);
    padding: 4px 8px; outline: none;
}
.resolve-form button {
    background: rgba(16,185,129,.1);
    border: 1px solid rgba(16,185,129,.3);
    color: var(--fc-success);
    padding: 4px 10px; cursor: pointer;
    transition: all .15s;
}
.resolve-form button:hover { background: rgba(16,185,129,.2); }

.empty-state { text-align: center; padding: 4rem; color: var(--fc-muted); }
.empty-state i { font-size: 2.5rem; margin-bottom: 1rem; display: block; }
</style>

{{-- Page Header --}}
<div class="fc-page-header">
    <div>
        <div class="fc-page-title">
            <i class="fas fa-bell me-2" style="color:var(--fc-critical)"></i>Fraud Alerts
        </div>
        <div style="font-size:.72rem; color:var(--fc-muted); margin-top:3px">
            {{ $stats['open'] }} open alerts · {{ $stats['critical'] }} critical
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.fraud.dashboard') }}" class="btn-fc-ghost">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('admin.fraud.index') }}" class="btn-fc-ghost">
            <i class="fas fa-shield-alt"></i> Checks
        </a>
    </div>
</div>

{{-- Mini Stats --}}
<div class="stat-mini-grid">
    <div class="stat-mini">
        <div class="stat-mini-label">Total</div>
        <div class="stat-mini-value">{{ $stats['total'] }}</div>
    </div>
    <div class="stat-mini" style="border-color:rgba(239,68,68,.3)">
        <div class="stat-mini-label">Open</div>
        <div class="stat-mini-value" style="color:var(--fc-danger)">{{ $stats['open'] }}</div>
    </div>
    <div class="stat-mini" style="border-color:rgba(220,38,38,.4)">
        <div class="stat-mini-label">Critical</div>
        <div class="stat-mini-value" style="color:#fca5a5">{{ $stats['critical'] }}</div>
    </div>
    <div class="stat-mini" style="border-color:rgba(245,158,11,.3)">
        <div class="stat-mini-label">Warning</div>
        <div class="stat-mini-value" style="color:var(--fc-warning)">{{ $stats['warning'] }}</div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="fc-filters">
    <select name="status" class="fc-select" onchange="this.form.submit()">
        <option value="">All Status</option>
        <option value="open"           {{ request('status') === 'open'           ? 'selected' : '' }}>Open</option>
        <option value="investigating"  {{ request('status') === 'investigating'  ? 'selected' : '' }}>Investigating</option>
        <option value="resolved"       {{ request('status') === 'resolved'       ? 'selected' : '' }}>Resolved</option>
        <option value="false_positive" {{ request('status') === 'false_positive' ? 'selected' : '' }}>False Positive</option>
    </select>

    <select name="severity" class="fc-select" onchange="this.form.submit()">
        <option value="">All Severity</option>
        <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
        <option value="warning"  {{ request('severity') === 'warning'  ? 'selected' : '' }}>Warning</option>
        <option value="info"     {{ request('severity') === 'info'     ? 'selected' : '' }}>Info</option>
    </select>

    @if(request()->hasAny(['status','severity']))
    <a href="{{ route('admin.fraud.alerts.index') }}" class="btn-fc-ghost" style="padding:6px 12px; font-size:.72rem;">
        <i class="fas fa-times"></i> Clear
    </a>
    @endif
</form>

{{-- Table --}}
<div class="fc-table-wrap">
    @if(session('success'))
    <div style="background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.3); color:var(--fc-success); padding:.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:.82rem;">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
    </div>
    @endif

    <table class="fc-table">
        <thead>
            <tr>
                <th>Alert ID</th>
                <th>Severity</th>
                <th>Title</th>
                <th>Check</th>
                <th>Type</th>
                <th>Assigned</th>
                <th>Status</th>
                <th>Created</th>
                <th>Resolve</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alerts as $alert)
            <tr>
                <td><span class="alert-id">{{ $alert->alert_id }}</span></td>
                <td>
                    <span class="badge-severity {{ $alert->severity }}">
                        <span class="severity-dot {{ $alert->severity }}"></span>
                        {{ ucfirst($alert->severity) }}
                    </span>
                </td>
                <td>
                    <div style="font-size:.8rem; font-weight:500; max-width:260px">
                        {{ Str::limit($alert->title, 55) }}
                    </div>
                    @if($alert->description)
                    <div style="font-size:.68rem; color:var(--fc-muted); margin-top:2px">
                        {{ Str::limit($alert->description, 60) }}
                    </div>
                    @endif
                </td>
                <td>
                    @if($alert->fraudCheck)
                    <a href="{{ route('admin.fraud.show', $alert->fraudCheck) }}"
                        style="font-family:monospace; font-size:.72rem; color:var(--fc-accent); text-decoration:none">
                        {{ $alert->fraudCheck->check_id }}
                    </a>
                    @else
                    <span style="color:var(--fc-muted); font-size:.72rem">—</span>
                    @endif
                </td>
                <td>
                    <span style="font-size:.72rem; text-transform:uppercase; letter-spacing:.06em; color:var(--fc-muted)">
                        {{ str_replace('_', ' ', $alert->type) }}
                    </span>
                </td>
                <td>
                    <span style="font-size:.75rem; color:var(--fc-muted)">{{ $alert->assignee->name ?? '—' }}</span>
                </td>
                <td>
                    <span class="badge-alert-status {{ $alert->status }}">
                        {{ ucfirst(str_replace('_', ' ', $alert->status)) }}
                    </span>
                </td>
                <td style="font-size:.72rem; color:var(--fc-muted); white-space:nowrap">
                    {{ $alert->created_at->format('d M Y') }}<br>
                    <span style="font-size:.65rem">{{ $alert->created_at->diffForHumans() }}</span>
                </td>
                <td>
                    @if($alert->status === 'open' || $alert->status === 'investigating')
                    <form method="POST" action="{{ route('admin.fraud.alerts.resolve', $alert) }}" class="resolve-form d-flex gap-1 align-items-center">
                        @csrf @method('PATCH')
                        <select name="status">
                            <option value="investigating">Investigating</option>
                            <option value="resolved">Resolved</option>
                            <option value="false_positive">False +</option>
                        </select>
                        <button type="submit"><i class="fas fa-check"></i></button>
                    </form>
                    @else
                    <span style="font-size:.68rem; color:var(--fc-muted)">{{ $alert->resolved_at?->format('d M') ?? '—' }}</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.fraud.alerts.show', $alert) }}" class="action-btn" title="View Alert">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10">
                    <div class="empty-state">
                        <i class="fas fa-bell-slash" style="color:var(--fc-success)"></i>
                        No alerts found
                        <div style="font-size:.78rem; margin-top:.5rem; color:var(--fc-muted)">All clear — no active fraud alerts</div>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:1.5rem">{{ $alerts->links() }}</div>
</div>
@endsection
