@extends('admin.master')

@section('title', 'Fraud Blacklist')

@section('content')
<style>
:root {
    --fc-primary:#0a0e1a; --fc-card:#1a2235; --fc-border:rgba(255,255,255,0.07);
    --fc-accent:#6366f1; --fc-success:#10b981; --fc-warning:#f59e0b;
    --fc-danger:#ef4444; --fc-muted:#64748b; --fc-text:#f1f5f9;
}
body { background: var(--fc-primary); color: var(--fc-text); }

.fc-page-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--fc-border);
    display: flex; align-items: center; justify-content: space-between;
}
.fc-page-title { font-size: 1.2rem; font-weight: 700; }

.bl-layout {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 1.5rem;
    padding: 1.5rem 2rem;
}

.stat-mini-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: .75rem;
    margin-bottom: 1.5rem;
}
.stat-mini {
    background: var(--fc-card);
    border: 1px solid var(--fc-border);
    border-radius: 10px;
    padding: .9rem 1rem;
}
.stat-mini-label { font-size: .62rem; color: var(--fc-muted); text-transform: uppercase; letter-spacing: .1em; margin-bottom: .25rem; }
.stat-mini-value { font-size: 1.5rem; font-weight: 800; line-height: 1; }

.fc-filters {
    display: flex; gap: .75rem; flex-wrap: wrap; align-items: center;
    margin-bottom: 1.25rem;
}
.fc-select, .fc-input-sm {
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--fc-border);
    color: var(--fc-text);
    padding: 6px 12px;
    border-radius: 8px;
    font-size: .78rem;
    outline: none;
}
.fc-select:focus, .fc-input-sm:focus { border-color: var(--fc-accent); }
.fc-input-sm { min-width: 200px; }

.fc-table { width: 100%; border-collapse: collapse; font-size: .8rem; }
.fc-table thead th {
    background: var(--fc-card);
    color: var(--fc-muted);
    text-transform: uppercase;
    font-size: .63rem; letter-spacing: .1em;
    padding: .7rem 1rem;
    text-align: left;
    border-bottom: 1px solid var(--fc-border);
    white-space: nowrap;
}
.fc-table tbody tr { border-bottom: 1px solid var(--fc-border); transition: background .15s; }
.fc-table tbody tr:hover { background: rgba(255,255,255,0.02); }
.fc-table tbody td { padding: .8rem 1rem; vertical-align: middle; }

.bl-type-badge {
    display: inline-block;
    font-size: .62rem; font-weight: 700;
    padding: 3px 9px;
    border-radius: 4px;
    text-transform: uppercase; letter-spacing: .07em;
}
.bl-type-badge.email   { background: rgba(99,102,241,.15); color: #a5b4fc; }
.bl-type-badge.phone   { background: rgba(16,185,129,.15); color: var(--fc-success); }
.bl-type-badge.ip      { background: rgba(56,189,248,.15); color: #38bdf8; }
.bl-type-badge.device  { background: rgba(245,158,11,.15); color: var(--fc-warning); }
.bl-type-badge.country { background: rgba(239,68,68,.15); color: var(--fc-danger); }

.bl-value { font-family: monospace; font-size: .78rem; font-weight: 600; color: var(--fc-text); }

.bl-status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: .63rem; font-weight: 700;
    padding: 3px 9px;
    border-radius: 20px;
    cursor: pointer;
    border: none;
    transition: all .2s;
}
.bl-status-badge.active   { background: rgba(16,185,129,.15); color: var(--fc-success); border: 1px solid rgba(16,185,129,.3); }
.bl-status-badge.inactive { background: rgba(100,116,139,.1); color: var(--fc-muted); border: 1px solid rgba(100,116,139,.2); }

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
.action-btn.danger:hover { background: rgba(239,68,68,.15); border-color: var(--fc-danger); color: var(--fc-danger); }

.bl-add-panel {
    background: var(--fc-card);
    border: 1px solid var(--fc-border);
    border-radius: 12px;
    overflow: hidden;
    position: sticky;
    top: 1rem;
}
.bl-add-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--fc-border);
    font-size: .78rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: .08em;
    color: var(--fc-muted);
    display: flex; align-items: center; gap: 8px;
}
.bl-add-body { padding: 1.25rem; }

.fc-label {
    display: block;
    font-size: .7rem; font-weight: 600;
    text-transform: uppercase; letter-spacing: .08em;
    color: var(--fc-muted); margin-bottom: 5px;
}
.fc-label span { color: var(--fc-danger); }

.fc-control {
    width: 100%;
    background: rgba(255,255,255,0.04);
    border: 1px solid var(--fc-border);
    color: var(--fc-text);
    padding: 9px 12px;
    border-radius: 8px;
    font-size: .82rem;
    outline: none;
    transition: border-color .2s;
    margin-bottom: 1rem;
}
.fc-control:focus { border-color: var(--fc-accent); }
.fc-control.is-invalid { border-color: var(--fc-danger); }
.invalid-feedback { color: var(--fc-danger); font-size: .7rem; margin-top: -10px; margin-bottom: 10px; display: block; }

.bl-submit {
    width: 100%; padding: 11px;
    background: var(--fc-accent);
    color: #fff; border: none;
    border-radius: 9px;
    font-size: .82rem; font-weight: 700;
    cursor: pointer; transition: all .2s;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.bl-submit:hover { background: #4f46e5; }

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

.expires-chip {
    font-size: .65rem; font-weight: 600;
    color: var(--fc-warning);
    background: rgba(245,158,11,.1);
    border: 1px solid rgba(245,158,11,.2);
    padding: 2px 7px; border-radius: 10px;
}
.expires-chip.expired { color: var(--fc-danger); background: rgba(239,68,68,.1); border-color: rgba(239,68,68,.2); }

.empty-state { text-align: center; padding: 3rem; color: var(--fc-muted); }
.empty-state i { font-size: 2rem; margin-bottom: 1rem; display: block; }
</style>

{{-- Page Header --}}
<div class="fc-page-header">
    <div>
        <div class="fc-page-title">
            <i class="fas fa-ban me-2" style="color:var(--fc-danger)"></i>Fraud Blacklist
        </div>
        <div style="font-size:.72rem; color:var(--fc-muted); margin-top:3px">
            {{ $stats['active'] }} active entries blocking emails, IPs & phones
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.fraud.dashboard') }}" class="btn-fc-ghost">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
    </div>
</div>

<div class="bl-layout">

    {{-- Left: List --}}
    <div>
        {{-- Stats --}}
        <div class="stat-mini-grid">
            <div class="stat-mini">
                <div class="stat-mini-label">Total</div>
                <div class="stat-mini-value">{{ $stats['total'] }}</div>
            </div>
            <div class="stat-mini" style="border-color:rgba(16,185,129,.2)">
                <div class="stat-mini-label">Active</div>
                <div class="stat-mini-value" style="color:var(--fc-success)">{{ $stats['active'] }}</div>
            </div>
            <div class="stat-mini" style="border-color:rgba(99,102,241,.2)">
                <div class="stat-mini-label">Email</div>
                <div class="stat-mini-value" style="color:#a5b4fc">{{ $stats['email'] }}</div>
            </div>
            <div class="stat-mini" style="border-color:rgba(56,189,248,.2)">
                <div class="stat-mini-label">IP</div>
                <div class="stat-mini-value" style="color:#38bdf8">{{ $stats['ip'] }}</div>
            </div>
            <div class="stat-mini" style="border-color:rgba(16,185,129,.2)">
                <div class="stat-mini-label">Phone</div>
                <div class="stat-mini-value" style="color:var(--fc-success)">{{ $stats['phone'] }}</div>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="fc-filters">
            <input type="text" name="search" class="fc-input-sm" placeholder="Search value..." value="{{ request('search') }}">
            <select name="type" class="fc-select" onchange="this.form.submit()">
                <option value="">All Types</option>
                @foreach($types as $val => $label)
                <option value="{{ $val }}" {{ request('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @if(request()->hasAny(['search','type']))
            <a href="{{ route('admin.fraud.blacklist.index') }}" class="btn-fc-ghost" style="padding:6px 12px; font-size:.72rem;">
                <i class="fas fa-times"></i> Clear
            </a>
            @endif
            <button type="submit" class="btn-fc-ghost" style="padding:6px 12px; font-size:.72rem;">
                <i class="fas fa-search"></i> Search
            </button>
        </form>

        @if(session('success'))
        <div style="background:rgba(16,185,129,.1); border:1px solid rgba(16,185,129,.3); color:var(--fc-success); padding:.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:.82rem;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
        @endif

        <table class="fc-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Reason</th>
                    <th>Added By</th>
                    <th>Expires</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($blacklists as $item)
                <tr>
                    <td><span class="bl-type-badge {{ $item->type }}">{{ ucfirst($item->type) }}</span></td>
                    <td><span class="bl-value">{{ $item->value }}</span></td>
                    <td><span style="font-size:.78rem; color:var(--fc-muted)">{{ Str::limit($item->reason, 45) }}</span></td>
                    <td>
                        <span style="font-size:.75rem; color:var(--fc-muted)">{{ $item->creator->name ?? 'System' }}</span>
                        <div style="font-size:.65rem; color:var(--fc-muted)">{{ $item->created_at->format('d M Y') }}</div>
                    </td>
                    <td>
                        @if($item->expires_at)
                            @if($item->expires_at->isPast())
                                <span class="expires-chip expired">Expired {{ $item->expires_at->diffForHumans() }}</span>
                            @else
                                <span class="expires-chip">Expires {{ $item->expires_at->diffForHumans() }}</span>
                            @endif
                        @else
                            <span style="font-size:.72rem; color:var(--fc-muted)">Permanent</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.fraud.blacklist.toggle', $item) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="bl-status-badge {{ $item->is_active ? 'active' : 'inactive' }}">
                                <span style="width:5px;height:5px;border-radius:50%;background:currentColor;display:inline-block"></span>
                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.fraud.blacklist.destroy', $item) }}" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn danger" title="Remove"
                                onclick="return confirm('Remove {{ $item->value }} from blacklist?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fas fa-shield-check" style="color:var(--fc-success)"></i>
                            Blacklist is empty
                            <div style="font-size:.78rem; margin-top:.5rem">Use the form to add emails, IPs or phones</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:1.25rem">{{ $blacklists->links() }}</div>
    </div>

    {{-- Right: Add Form --}}
    <div>
        <div class="bl-add-panel">
            <div class="bl-add-header">
                <i class="fas fa-plus" style="color:var(--fc-danger)"></i> Add to Blacklist
            </div>
            <div class="bl-add-body">
                @if($errors->any())
                <div style="background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.3); color:var(--fc-danger); padding:.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:.75rem;">
                    @foreach($errors->all() as $error)
                    <div><i class="fas fa-exclamation-circle me-1"></i>{{ $error }}</div>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('admin.fraud.blacklist.store') }}">
                    @csrf

                    <label class="fc-label">Type <span>*</span></label>
                    <select name="type" class="fc-control @error('type') is-invalid @enderror">
                        <option value="">Select type...</option>
                        @foreach($types as $val => $label)
                        <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')<span class="invalid-feedback">{{ $message }}</span>@enderror

                    <label class="fc-label">Value <span>*</span></label>
                    <input type="text" name="value"
                        class="fc-control @error('value') is-invalid @enderror"
                        placeholder="e.g. test@mailinator.com"
                        value="{{ old('value') }}">
                    @error('value')<span class="invalid-feedback">{{ $message }}</span>@enderror

                    <label class="fc-label">Reason <span>*</span></label>
                    <input type="text" name="reason"
                        class="fc-control @error('reason') is-invalid @enderror"
                        placeholder="e.g. Known fraud account"
                        value="{{ old('reason') }}">
                    @error('reason')<span class="invalid-feedback">{{ $message }}</span>@enderror

                    <label class="fc-label">Expires At <span style="color:var(--fc-muted)">(optional)</span></label>
                    <input type="datetime-local" name="expires_at"
                        class="fc-control @error('expires_at') is-invalid @enderror"
                        value="{{ old('expires_at') }}">
                    @error('expires_at')<span class="invalid-feedback">{{ $message }}</span>@enderror

                    <button type="submit" class="bl-submit">
                        <i class="fas fa-ban"></i> Add to Blacklist
                    </button>
                </form>

                <div style="margin-top:1.25rem; padding:.85rem; background:rgba(99,102,241,.07); border:1px solid rgba(99,102,241,.15); border-radius:8px; font-size:.72rem; color:var(--fc-muted); line-height:1.6">
                    <strong style="color:var(--fc-accent)">How it works:</strong><br>
                    Blacklisted values score <strong style="color:var(--fc-danger)">+80 risk</strong> automatically on every new fraud check.
                    Expired entries are ignored. Toggle to temporarily disable without deleting.
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
