@extends('admin.master')
@section('content')
<style>
    :root {
        --fc-primary:    #0a0e1a;
        --fc-surface:    #111827;
        --fc-card:       #1a2235;
        --fc-border:     rgba(255,255,255,0.07);
        --fc-accent:     #6366f1;
        --fc-success:    #10b981;
        --fc-warning:    #f59e0b;
        --fc-danger:     #ef4444;
        --fc-critical:   #dc2626;
        --fc-text:       #f1f5f9;
        --fc-muted:      #64748b;
        --fc-glow:       0 0 20px rgba(99,102,241,0.3);
    }

    body { background: var(--fc-primary); color: var(--fc-text); font-family: 'JetBrains Mono', monospace; }

    .fraud-header {
        background: linear-gradient(135deg, #0a0e1a 0%, #111827 100%);
        border-bottom: 1px solid var(--fc-border);
        padding: 1.5rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .fraud-title { font-size: 1.4rem; font-weight: 700; letter-spacing: -0.02em; color: var(--fc-text); }
    .fraud-title span { color: var(--fc-accent); }

    .live-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(16,185,129,0.1);
        border: 1px solid rgba(16,185,129,0.3);
        color: var(--fc-success);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.08em;
    }

    .live-badge::before {
        content: '';
        width: 6px; height: 6px;
        background: var(--fc-success);
        border-radius: 50%;
        animation: pulse-dot 1.5s infinite;
    }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.7); }
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem;
        padding: 1.5rem 2rem;
    }

    .stat-card {
        background: var(--fc-card);
        border: 1px solid var(--fc-border);
        border-radius: 12px;
        padding: 1.2rem;
        position: relative;
        overflow: hidden;
        transition: border-color .2s;
    }

    .stat-card:hover { border-color: var(--fc-accent); }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--accent-color, var(--fc-accent)), transparent);
    }

    .stat-card.success { --accent-color: var(--fc-success); }
    .stat-card.warning { --accent-color: var(--fc-warning); }
    .stat-card.danger  { --accent-color: var(--fc-danger); }
    .stat-card.info    { --accent-color: #38bdf8; }

    .stat-label { font-size: 0.68rem; color: var(--fc-muted); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem; }
    .stat-value { font-size: 2rem; font-weight: 800; line-height: 1; color: var(--fc-text); }
    .stat-sub   { font-size: 0.72rem; color: var(--fc-muted); margin-top: 4px; }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 1.5rem;
        padding: 0 2rem 2rem;
    }

    .panel { background: var(--fc-card); border: 1px solid var(--fc-border); border-radius: 12px; overflow: hidden; }

    .panel-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--fc-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.82rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--fc-muted);
    }

    .panel-header a { color: var(--fc-accent); text-decoration: none; font-size: 0.72rem; letter-spacing: 0.05em; }

    .risk-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.9rem 1.25rem;
        border-bottom: 1px solid var(--fc-border);
        transition: background .15s;
        text-decoration: none;
        color: inherit;
    }

    .risk-row:last-child { border-bottom: none; }
    .risk-row:hover { background: rgba(255,255,255,0.02); }

    .risk-id { font-size: 0.72rem; font-weight: 700; color: var(--fc-accent); min-width: 90px; font-family: monospace; }
    .risk-info { flex: 1; min-width: 0; }
    .risk-name { font-size: 0.82rem; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .risk-meta { font-size: 0.68rem; color: var(--fc-muted); margin-top: 2px; }
    .risk-score-bar { min-width: 80px; }
    .score-num { font-size: 0.75rem; font-weight: 700; text-align: right; margin-bottom: 4px; }
    .score-bar-bg { height: 4px; background: rgba(255,255,255,0.08); border-radius: 2px; overflow: hidden; }
    .score-bar-fill { height: 100%; border-radius: 2px; transition: width .6s ease; }

    .badge-risk {
        display: inline-block;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .badge-risk.low      { background: rgba(16,185,129,.15); color: var(--fc-success); border: 1px solid rgba(16,185,129,.3); }
    .badge-risk.medium   { background: rgba(245,158,11,.15); color: var(--fc-warning); border: 1px solid rgba(245,158,11,.3); }
    .badge-risk.high     { background: rgba(239,68,68,.15);  color: var(--fc-danger);  border: 1px solid rgba(239,68,68,.3); }
    .badge-risk.critical { background: rgba(220,38,38,.2);   color: #fca5a5;           border: 1px solid rgba(220,38,38,.5); }

    .alert-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 0.9rem 1.25rem;
        border-bottom: 1px solid var(--fc-border);
    }
    .alert-item:last-child { border-bottom: none; }

    .alert-dot { width: 8px; height: 8px; border-radius: 50%; margin-top: 5px; flex-shrink: 0; }
    .alert-dot.critical { background: var(--fc-critical); box-shadow: 0 0 6px var(--fc-critical); }
    .alert-dot.warning  { background: var(--fc-warning); }
    .alert-dot.info     { background: #38bdf8; }

    .alert-title { font-size: 0.8rem; font-weight: 500; line-height: 1.4; }
    .alert-sub   { font-size: 0.68rem; color: var(--fc-muted); margin-top: 3px; }

    .distribution-list { padding: 1rem 1.25rem; }
    .dist-item { display: flex; align-items: center; gap: 8px; margin-bottom: 0.75rem; }
    .dist-label { font-size: 0.75rem; flex: 1; }
    .dist-count { font-size: 0.75rem; font-weight: 700; color: var(--fc-text); }
    .dist-bar-bg { flex: 2; height: 6px; background: rgba(255,255,255,0.06); border-radius: 3px; overflow: hidden; }
    .dist-bar-fill { height: 100%; border-radius: 3px; }

    .btn-fc {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 16px; border-radius: 8px;
        font-size: 0.78rem; font-weight: 600;
        cursor: pointer; transition: all .2s; border: none; text-decoration: none;
    }
    .btn-fc-primary { background: var(--fc-accent); color: #fff; }
    .btn-fc-primary:hover { background: #4f46e5; box-shadow: var(--fc-glow); color: #fff; }
    .btn-fc-ghost { background: rgba(255,255,255,0.05); color: var(--fc-text); border: 1px solid var(--fc-border); }
    .btn-fc-ghost:hover { background: rgba(255,255,255,0.1); color: var(--fc-text); }
</style>

{{-- ── Header ── --}}
<div class="fraud-header">
    <div>
        <div class="fraud-title">Fraud <span>Detection</span> Center</div>
        <div style="font-size:.72rem; color:var(--fc-muted); margin-top:4px;">
            Powered by Risk Intelligence Engine · SEON-Style Analysis
        </div>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="live-badge">LIVE MONITORING</span>
        <a href="{{ route('admin.fraud.create') }}" class="btn-fc btn-fc-primary">
            <i class="fas fa-search-plus"></i> New Check
        </a>
    </div>
</div>

{{-- ── Stats Grid ── --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-label">Total Checks</div>
        <div class="stat-value">{{ number_format($stats['total']) }}</div>
        <div class="stat-sub">All time</div>
    </div>
    <div class="stat-card success">
        <div class="stat-label">Approved</div>
        <div class="stat-value" style="color:var(--fc-success)">{{ number_format($stats['approved']) }}</div>
        <div class="stat-sub">Auto cleared</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-label">In Review</div>
        <div class="stat-value" style="color:var(--fc-warning)">{{ number_format($stats['review']) }}</div>
        <div class="stat-sub">Manual required</div>
    </div>
    <div class="stat-card danger">
        <div class="stat-label">Declined</div>
        <div class="stat-value" style="color:var(--fc-danger)">{{ number_format($stats['declined']) }}</div>
        <div class="stat-sub">Fraud blocked</div>
    </div>
    <div class="stat-card danger">
        <div class="stat-label">High Risk</div>
        <div class="stat-value" style="color:var(--fc-danger)">{{ number_format($stats['high_risk']) }}</div>
        <div class="stat-sub">Need attention</div>
    </div>
    <div class="stat-card info">
        <div class="stat-label">Today</div>
        <div class="stat-value" style="color:#38bdf8">{{ number_format($stats['today']) }}</div>
        <div class="stat-sub">Checks today</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Avg Risk Score</div>
        <div class="stat-value">{{ $stats['avg_score'] }}</div>
        <div class="stat-sub">Out of 100</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-label">Open Alerts</div>
        <div class="stat-value" style="color:var(--fc-warning)">{{ $openAlerts->count() }}</div>
        <div class="stat-sub">Unresolved</div>
    </div>
</div>

{{-- ── Main Content ── --}}
<div class="content-grid">

    {{-- Left Column --}}
    <div>
        {{-- Recent High Risk --}}
        <div class="panel mb-3">
            <div class="panel-header">
                <span><i class="fas fa-exclamation-triangle me-2" style="color:var(--fc-danger)"></i>Recent High Risk</span>
                <a href="{{ route('admin.fraud.index', ['risk_level' => 'high']) }}">View All →</a>
            </div>
            @forelse($recentHighRisk as $check)
            <a href="{{ route('admin.fraud.show', $check) }}" class="risk-row">
                <div class="risk-id">{{ $check->check_id }}</div>
                <div class="risk-info">
                    <div class="risk-name">{{ $check->customer_name ?: $check->input_value }}</div>
                    <div class="risk-meta">
                        {{ $check->type }} · {{ $check->country ?? 'Unknown' }}
                        @if($check->vpn_detected) · <span style="color:var(--fc-warning)">VPN</span>@endif
                        · {{ $check->created_at->diffForHumans() }}
                    </div>
                </div>
                <div>
                    <span class="badge-risk {{ $check->risk_level }}">{{ strtoupper($check->risk_level) }}</span>
                </div>
                <div class="risk-score-bar">
                    <div class="score-num" style="color:{{ $check->risk_score >= 80 ? 'var(--fc-danger)' : ($check->risk_score >= 60 ? 'var(--fc-warning)' : 'var(--fc-success)') }}">
                        {{ $check->risk_score }}%
                    </div>
                    <div class="score-bar-bg">
                        <div class="score-bar-fill" style="width:{{ $check->risk_score }}%; background:{{ $check->risk_score >= 80 ? 'var(--fc-danger)' : ($check->risk_score >= 60 ? 'var(--fc-warning)' : 'var(--fc-success)') }}"></div>
                    </div>
                </div>
            </a>
            @empty
            <div style="padding:2rem; text-align:center; color:var(--fc-muted); font-size:.82rem;">
                <i class="fas fa-shield-check" style="font-size:1.5rem; color:var(--fc-success); display:block; margin-bottom:.5rem"></i>
                No high risk checks found
            </div>
            @endforelse
        </div>

        {{-- Top Rules --}}
        <div class="panel">
            <div class="panel-header">
                <span><i class="fas fa-code-branch me-2" style="color:var(--fc-accent)"></i>Top Triggered Rules</span>
                <a href="{{ route('admin.fraud.rules.index') }}">Manage Rules →</a>
            </div>
            @forelse($topRules as $rule)
            <div class="risk-row">
                <div class="risk-id">{{ $rule->code }}</div>
                <div class="risk-info">
                    <div class="risk-name">{{ $rule->name }}</div>
                    <div class="risk-meta">{{ ucfirst($rule->category) }} · {{ $rule->action }}</div>
                </div>
                <div style="font-size:.8rem; color:var(--fc-warning); font-weight:700; min-width:80px; text-align:right">
                    {{ number_format($rule->triggered_count) }}×
                </div>
                <div>
                    <span class="badge-risk {{ $rule->is_active ? 'low' : 'medium' }}">{{ $rule->is_active ? 'ON' : 'OFF' }}</span>
                </div>
            </div>
            @empty
            <div style="padding:1.5rem; text-align:center; color:var(--fc-muted); font-size:.82rem;">No rules configured yet</div>
            @endforelse
        </div>
    </div>

    {{-- Right Column --}}
    <div>
        {{-- Open Alerts --}}
        <div class="panel mb-3">
            <div class="panel-header">
                <span><i class="fas fa-bell me-2" style="color:var(--fc-critical)"></i>Open Alerts</span>
                <a href="{{ route('admin.fraud.alerts.index') }}">All →</a>
            </div>
            @forelse($openAlerts as $alert)
            <div class="alert-item">
                <div class="alert-dot {{ $alert->severity }}"></div>
                <div style="flex:1; min-width:0">
                    <div class="alert-title">{{ Str::limit($alert->title, 50) }}</div>
                    <div class="alert-sub">
                        {{ $alert->fraudCheck->check_id ?? '—' }} · {{ $alert->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
            @empty
            <div style="padding:1.5rem; text-align:center; color:var(--fc-success); font-size:.82rem;">
                <i class="fas fa-check-circle"></i> All clear!
            </div>
            @endforelse
        </div>

        {{-- Risk Distribution --}}
        <div class="panel mb-3">
            <div class="panel-header">
                <span><i class="fas fa-chart-pie me-2"></i>Risk Distribution</span>
            </div>
            <div class="distribution-list">
                @php
                    $total = array_sum($riskDistribution) ?: 1;
                    $dist = [
                        'critical' => ['color' => '#dc2626', 'label' => 'Critical'],
                        'high'     => ['color' => '#ef4444', 'label' => 'High'],
                        'medium'   => ['color' => '#f59e0b', 'label' => 'Medium'],
                        'low'      => ['color' => '#10b981', 'label' => 'Low'],
                    ];
                @endphp
                @foreach($dist as $level => $meta)
                @php $count = $riskDistribution[$level] ?? 0; $pct = round($count / $total * 100); @endphp
                <div class="dist-item">
                    <div class="dist-label" style="color:{{ $meta['color'] }}">{{ $meta['label'] }}</div>
                    <div class="dist-bar-bg">
                        <div class="dist-bar-fill" style="width:{{ $pct }}%; background:{{ $meta['color'] }}"></div>
                    </div>
                    <div class="dist-count">{{ $count }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Quick Check CTA --}}
        <div class="panel" style="padding:1.5rem; text-align:center; background:linear-gradient(135deg,rgba(99,102,241,.15),rgba(99,102,241,.05))">
            <i class="fas fa-search" style="font-size:2rem; color:var(--fc-accent); margin-bottom:.75rem; display:block"></i>
            <div style="font-size:.9rem; font-weight:700; margin-bottom:.4rem">Run Instant Check</div>
            <div style="font-size:.75rem; color:var(--fc-muted); margin-bottom:1rem">Analyze email, phone, IP or transaction in seconds</div>
            <a href="{{ route('admin.fraud.create') }}" class="btn-fc btn-fc-primary w-100" style="justify-content:center">
                <i class="fas fa-search"></i> Start Analysis
            </a>
        </div>
    </div>

</div>
@endsection
