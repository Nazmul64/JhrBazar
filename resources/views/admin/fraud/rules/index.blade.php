@extends('admin.master')
@section('title', 'Fraud Rules')

@section('content')
<style>
:root { --fc-primary:#0a0e1a; --fc-card:#1a2235; --fc-border:rgba(255,255,255,.07); --fc-accent:#6366f1; --fc-success:#10b981; --fc-warning:#f59e0b; --fc-danger:#ef4444; --fc-muted:#64748b; --fc-text:#f1f5f9; }
body { background:var(--fc-primary); color:var(--fc-text); }

.fc-header { padding:1.5rem 2rem; border-bottom:1px solid var(--fc-border); display:flex; align-items:center; justify-content:space-between; }
.fc-title  { font-size:1.2rem; font-weight:700; }
.fc-sub    { font-size:.72rem; color:var(--fc-muted); margin-top:3px; }

.fc-filters { background:var(--fc-card); border-bottom:1px solid var(--fc-border); padding:1rem 2rem; display:flex; gap:.75rem; flex-wrap:wrap; align-items:center; }
.fc-select, .fc-input { background:rgba(255,255,255,.05); border:1px solid var(--fc-border); color:var(--fc-text); padding:6px 12px; border-radius:8px; font-size:.78rem; outline:none; }
.fc-select:focus, .fc-input:focus { border-color:var(--fc-accent); }
.fc-input { min-width:220px; }

.fc-table-wrap { padding:1.5rem 2rem; }
.fc-table { width:100%; border-collapse:collapse; font-size:.8rem; }
.fc-table thead th { background:var(--fc-card); color:var(--fc-muted); text-transform:uppercase; font-size:.65rem; letter-spacing:.1em; padding:.75rem 1rem; text-align:left; border-bottom:1px solid var(--fc-border); white-space:nowrap; }
.fc-table tbody tr { border-bottom:1px solid var(--fc-border); transition:background .15s; }
.fc-table tbody tr:hover { background:rgba(255,255,255,.02); }
.fc-table tbody td { padding:.85rem 1rem; vertical-align:middle; }

.badge-cat { display:inline-block; font-size:.62rem; font-weight:600; padding:2px 8px; border-radius:4px; text-transform:uppercase; letter-spacing:.06em; }
.badge-cat.identity    { background:rgba(99,102,241,.15);  color:#a5b4fc; }
.badge-cat.transaction { background:rgba(16,185,129,.12);  color:var(--fc-success); }
.badge-cat.device      { background:rgba(245,158,11,.12);  color:var(--fc-warning); }
.badge-cat.network     { background:rgba(239,68,68,.12);   color:var(--fc-danger); }
.badge-cat.behavioral  { background:rgba(56,189,248,.12);  color:#38bdf8; }

.impact-pill { display:inline-block; font-size:.7rem; font-weight:700; padding:2px 8px; border-radius:4px; }
.impact-pill.pos { background:rgba(239,68,68,.15); color:var(--fc-danger); }
.impact-pill.neg { background:rgba(16,185,129,.15); color:var(--fc-success); }
.impact-pill.neu { background:rgba(100,116,139,.15); color:var(--fc-muted); }

.toggle-switch { position:relative; width:36px; height:20px; }
.toggle-switch input { display:none; }
.toggle-track { position:absolute; inset:0; background:rgba(255,255,255,.1); border-radius:10px; cursor:pointer; transition:background .2s; }
.toggle-track:after { content:''; position:absolute; top:3px; left:3px; width:14px; height:14px; background:#fff; border-radius:50%; transition:transform .2s; }
.toggle-switch input:checked + .toggle-track { background:var(--fc-success); }
.toggle-switch input:checked + .toggle-track:after { transform:translateX(16px); }

.action-btn { display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:6px; font-size:.75rem; text-decoration:none; transition:all .15s; border:1px solid var(--fc-border); background:transparent; color:var(--fc-muted); cursor:pointer; }
.action-btn:hover       { background:rgba(255,255,255,.08); color:var(--fc-text); }
.action-btn.danger:hover{ background:rgba(239,68,68,.15); border-color:var(--fc-danger); color:var(--fc-danger); }

.btn-primary { display:inline-flex; align-items:center; gap:6px; background:var(--fc-accent); color:#fff; padding:8px 16px; border-radius:8px; font-size:.78rem; font-weight:600; text-decoration:none; border:none; cursor:pointer; transition:all .2s; }
.btn-primary:hover { background:#4f46e5; color:#fff; }
.btn-ghost   { display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,.05); border:1px solid var(--fc-border); color:var(--fc-text); padding:8px 14px; border-radius:8px; font-size:.78rem; text-decoration:none; cursor:pointer; }
.btn-ghost:hover { background:rgba(255,255,255,.1); color:var(--fc-text); }

.rule-code   { font-family:monospace; color:var(--fc-accent); font-size:.75rem; font-weight:700; }
.cond-mono   { font-family:monospace; font-size:.72rem; background:rgba(255,255,255,.04); border:1px solid var(--fc-border); padding:3px 8px; border-radius:4px; }
.alert-success { background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.3); color:var(--fc-success); padding:.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:.82rem; }
.empty-state { text-align:center; padding:4rem; color:var(--fc-muted); }

.stat-chips  { display:flex; gap:.75rem; margin-top:3px; }
.stat-chip   { background:rgba(255,255,255,.05); border:1px solid var(--fc-border); border-radius:6px; padding:2px 10px; font-size:.7rem; }
</style>

<div class="fc-header">
    <div>
        <div class="fc-title"><i class="fas fa-code-branch me-2" style="color:var(--fc-accent)"></i>Fraud Rules</div>
        <div class="stat-chips">
            <span class="stat-chip">{{ $stats['total'] }} total</span>
            <span class="stat-chip" style="color:var(--fc-success)">{{ $stats['active'] }} active</span>
            <span class="stat-chip" style="color:var(--fc-muted)">{{ $stats['total'] - $stats['active'] }} inactive</span>
        </div>
    </div>
    <a href="{{ route('admin.fraud.rules.create') }}" class="btn-primary">
        <i class="fas fa-plus"></i> New Rule
    </a>
</div>

<form method="GET" class="fc-filters">
    <input type="text" name="search" class="fc-input" placeholder="🔍  Rule name or code…" value="{{ request('search') }}">

    <select name="category" class="fc-select" onchange="this.form.submit()">
        <option value="">All Categories</option>
        @foreach($categories as $val => $label)
            <option value="{{ $val }}" {{ request('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>

    <select name="is_active" class="fc-select" onchange="this.form.submit()">
        <option value="">All Status</option>
        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
    </select>

    <button type="submit" class="btn-ghost" style="padding:6px 14px;font-size:.72rem"><i class="fas fa-search"></i> Search</button>
    @if(request()->hasAny(['search','category','is_active']))
        <a href="{{ route('admin.fraud.rules.index') }}" class="btn-ghost" style="padding:6px 12px;font-size:.72rem"><i class="fas fa-times"></i> Clear</a>
    @endif
</form>

<div class="fc-table-wrap">

    @if(session('success'))
        <div class="alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif

    <table class="fc-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Rule Name</th>
                <th>Category</th>
                <th>Condition</th>
                <th>Action</th>
                <th>Impact</th>
                <th>Priority</th>
                <th>Triggered</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rules as $rule)
            <tr>
                <td><span class="rule-code">{{ $rule->code }}</span></td>
                <td>
                    <div style="font-weight:500;font-size:.82rem">{{ $rule->name }}</div>
                    @if($rule->description)
                        <div style="font-size:.68rem;color:var(--fc-muted)">{{ Str::limit($rule->description, 60) }}</div>
                    @endif
                </td>
                <td><span class="badge-cat {{ $rule->category }}">{{ $rule->category }}</span></td>
                <td>
                    <span class="cond-mono">{{ $rule->condition_field }}</span>
                    <span style="color:var(--fc-accent);margin:0 4px;font-size:.72rem">{{ $rule->condition_operator }}</span>
                    <span class="cond-mono">{{ Str::limit($rule->condition_value, 20) }}</span>
                </td>
                <td style="font-size:.75rem;color:var(--fc-muted);text-transform:capitalize">{{ $rule->action }}</td>
                <td>
                    @php $impact = $rule->score_impact; @endphp
                    <span class="impact-pill {{ $impact > 0 ? 'pos' : ($impact < 0 ? 'neg' : 'neu') }}">
                        {{ $impact > 0 ? '+' : '' }}{{ $impact }}
                    </span>
                </td>
                <td style="font-size:.8rem;text-align:center">{{ $rule->priority }}</td>
                <td style="font-size:.8rem;color:var(--fc-muted)">{{ number_format($rule->triggered_count) }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.fraud.rules.toggle', $rule) }}">
                        @csrf
                        <label class="toggle-switch">
                            <input type="checkbox" {{ $rule->is_active ? 'checked' : '' }} onchange="this.form.submit()">
                            <div class="toggle-track"></div>
                        </label>
                    </form>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.fraud.rules.edit', $rule) }}" class="action-btn" title="Edit"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="{{ route('admin.fraud.rules.destroy', $rule) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn danger" title="Delete"
                                onclick="return confirm('Delete rule {{ $rule->code }}?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10">
                    <div class="empty-state">
                        <i class="fas fa-code-branch" style="font-size:2.5rem;color:var(--fc-accent);display:block;margin-bottom:1rem"></i>
                        No rules found
                        <div style="font-size:.78rem;margin-top:.5rem">
                            <a href="{{ route('admin.fraud.rules.create') }}" style="color:var(--fc-accent)">Create your first rule</a>
                        </div>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:1.5rem">{{ $rules->links() }}</div>
</div>
@endsection
