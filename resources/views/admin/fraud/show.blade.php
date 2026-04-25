@extends('admin.master')
@section('title', ($fraudCheck->check_id ?? 'Check') . ' — Fraud Check')

@section('content')
<style>
:root {
    --fc-primary : #0a0e1a;
    --fc-card    : #1a2235;
    --fc-border  : rgba(255,255,255,.07);
    --fc-accent  : #6366f1;
    --fc-success : #10b981;
    --fc-warning : #f59e0b;
    --fc-danger  : #ef4444;
    --fc-muted   : #64748b;
    --fc-text    : #f1f5f9;
}
body { background: var(--fc-primary); color: var(--fc-text); }

/* ── Layout ──────────────────────────────────────────────── */
.fc-wrap     { max-width: 1100px; margin: 0 auto; padding: 1.5rem; }
.fc-card     { background: var(--fc-card); border: 1px solid var(--fc-border); border-radius: 12px; overflow: hidden; margin-bottom: 1.25rem; }
.fc-card-hdr { padding: .9rem 1.25rem; border-bottom: 1px solid var(--fc-border); font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .09em; color: var(--fc-muted); display: flex; align-items: center; gap: 8px; }

/* ── Result Header ───────────────────────────────────────── */
.result-header { background: var(--fc-card); border: 1px solid var(--fc-border); border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap; }

/* ── Gauge ───────────────────────────────────────────────── */
.gauge-wrap     { position: relative; width: 100px; height: 100px; flex-shrink: 0; }
.gauge-wrap svg { transform: rotate(-90deg); }
.gauge-bg       { fill: none; stroke: rgba(255,255,255,.06); stroke-width: 8; }
.gauge-fill     { fill: none; stroke-width: 8; stroke-linecap: round; }
.gauge-label    { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); text-align: center; line-height: 1; }
.gauge-score    { font-size: 1.3rem; font-weight: 800; }
.gauge-sub      { font-size: .55rem; color: var(--fc-muted); text-transform: uppercase; letter-spacing: .08em; margin-top: 2px; }

/* ── Result Meta ─────────────────────────────────────────── */
.result-meta  { flex: 1; min-width: 200px; }
.result-id    { font-size: .75rem; font-family: monospace; color: var(--fc-accent); margin-bottom: .4rem; }
.result-name  { font-size: 1.2rem; font-weight: 700; margin-bottom: .25rem; }
.result-email { font-size: .82rem; color: var(--fc-muted); margin-bottom: .75rem; }

/* ── Badges ──────────────────────────────────────────────── */
.badge-status          { display: inline-block; font-size: .7rem; font-weight: 700; padding: 4px 12px; border-radius: 6px; text-transform: uppercase; letter-spacing: .07em; margin-right: 6px; }
.badge-status.approved { background: rgba(16,185,129,.15); color: var(--fc-success); border: 1px solid rgba(16,185,129,.3); }
.badge-status.review   { background: rgba(245,158,11,.15); color: var(--fc-warning); border: 1px solid rgba(245,158,11,.3); }
.badge-status.declined { background: rgba(239,68,68,.15);  color: var(--fc-danger);  border: 1px solid rgba(239,68,68,.3); }
.badge-status.pending  { background: rgba(56,189,248,.15); color: #38bdf8;           border: 1px solid rgba(56,189,248,.3); }

.badge-risk          { display: inline-block; font-size: .7rem; font-weight: 700; padding: 4px 12px; border-radius: 6px; text-transform: uppercase; margin-right: 4px; }
.badge-risk.low      { background: rgba(16,185,129,.15); color: var(--fc-success); }
.badge-risk.medium   { background: rgba(245,158,11,.15); color: var(--fc-warning); }
.badge-risk.high     { background: rgba(239,68,68,.15);  color: var(--fc-danger); }
.badge-risk.critical { background: rgba(220,38,38,.2);   color: #fca5a5; }

/* ── Grid ────────────────────────────────────────────────── */
.detail-grid { display: grid; grid-template-columns: 1fr 360px; gap: 1.5rem; }
@media (max-width: 900px) { .detail-grid { grid-template-columns: 1fr; } }

/* ── Info Table ──────────────────────────────────────────── */
.info-table               { width: 100%; font-size: .8rem; border-collapse: collapse; }
.info-table tr            { border-bottom: 1px solid var(--fc-border); }
.info-table tr:last-child { border-bottom: none; }
.info-table td            { padding: .75rem 1.25rem; }
.info-table td:first-child { color: var(--fc-muted); width: 45%; font-size: .72rem; text-transform: uppercase; letter-spacing: .07em; }
.info-table td:last-child  { font-weight: 500; word-break: break-all; }

/* ── Signal Rows ─────────────────────────────────────────── */
.signal-row             { display: flex; align-items: center; gap: 10px; padding: .75rem 1.25rem; border-bottom: 1px solid var(--fc-border); font-size: .8rem; }
.signal-row:last-child  { border-bottom: none; }
.signal-dot             { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.signal-dot.safe        { background: var(--fc-success); }
.signal-dot.warning     { background: var(--fc-warning); }
.signal-dot.danger      { background: var(--fc-danger); }
.signal-dot.unknown     { background: var(--fc-muted); }
.signal-key             { color: var(--fc-muted); font-size: .72rem; text-transform: uppercase; letter-spacing: .07em; width: 140px; flex-shrink: 0; }

/* ── Rule Rows ───────────────────────────────────────────── */
.rule-row             { display: flex; align-items: center; justify-content: space-between; padding: .7rem 1.25rem; border-bottom: 1px solid var(--fc-border); font-size: .78rem; }
.rule-row:last-child  { border-bottom: none; }
.rule-code            { font-family: monospace; color: var(--fc-accent); font-size: .72rem; }

.impact-pill     { font-size: .7rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; }
.impact-pill.pos { background: rgba(239,68,68,.15);   color: var(--fc-danger); }
.impact-pill.neg { background: rgba(16,185,129,.15);  color: var(--fc-success); }
.impact-pill.neu { background: rgba(100,116,139,.15); color: var(--fc-muted); }

/* ── Flag Pills ──────────────────────────────────────────── */
.flag-pill { display: inline-flex; align-items: center; gap: 5px; background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.25); color: #fca5a5; font-size: .7rem; padding: 4px 10px; border-radius: 20px; margin: 3px; }

/* ── Form ────────────────────────────────────────────────── */
.fc-label   { font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; color: var(--fc-muted); margin-bottom: 5px; display: block; }
.fc-control { width: 100%; background: rgba(255,255,255,.04); border: 1px solid var(--fc-border); color: var(--fc-text); padding: 9px 12px; border-radius: 8px; font-size: .82rem; outline: none; transition: border-color .2s; }
.fc-control:focus { border-color: var(--fc-accent); }

/* ── Buttons ─────────────────────────────────────────────── */
.btn          { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: .78rem; font-weight: 600; cursor: pointer; border: none; transition: all .2s; text-decoration: none; }
.btn-primary  { background: var(--fc-accent); color: #fff; }
.btn-primary:hover  { background: #4f46e5; color: #fff; }
.btn-ghost    { background: rgba(255,255,255,.05); border: 1px solid var(--fc-border); color: var(--fc-text); }
.btn-ghost:hover    { background: rgba(255,255,255,.1); color: var(--fc-text); }
.btn-danger   { background: rgba(239,68,68,.1); color: var(--fc-danger); border: 1px solid rgba(239,68,68,.3); }
.btn-danger:hover   { background: rgba(239,68,68,.2); }

/* ── Misc ────────────────────────────────────────────────── */
.alert-success        { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3); color: var(--fc-success); padding: .75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: .82rem; }
.breadcrumb-bar       { font-size: .75rem; color: var(--fc-muted); margin-bottom: 1.5rem; display: flex; gap: 8px; align-items: center; }
.breadcrumb-bar a     { color: var(--fc-muted); text-decoration: none; }
.breadcrumb-bar a:hover { color: var(--fc-text); }
</style>

<div class="fc-wrap">

    {{-- ── Breadcrumb ─────────────────────────────────────────── --}}
    <div class="breadcrumb-bar">
        <a href="{{ route('admin.fraud.dashboard') }}">Dashboard</a>
        <span>›</span>
        <a href="{{ route('admin.fraud.index') }}">Checks</a>
        <span>›</span>
        <span style="color:var(--fc-text)">{{ $fraudCheck->check_id }}</span>
    </div>

    @if(session('success'))
        <div class="alert-success">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- ── Result Header ────────────────────────────────────────── --}}
    @php
        $sc    = (int) ($fraudCheck->risk_score ?? 0);
        $color = $sc >= 80 ? '#ef4444' : ($sc >= 60 ? '#f59e0b' : ($sc >= 40 ? '#f59e0b' : '#10b981'));
        $circ  = round(2 * M_PI * 40, 4);
        $dash  = round(($sc / 100) * $circ, 4);
    @endphp

    <div class="result-header">

        {{-- Gauge --}}
        <div class="gauge-wrap">
            <svg width="100" height="100" viewBox="0 0 100 100">
                <circle class="gauge-bg"   cx="50" cy="50" r="40"/>
                <circle class="gauge-fill" cx="50" cy="50" r="40"
                    stroke="{{ $color }}"
                    stroke-dasharray="{{ $dash }} {{ $circ }}"/>
            </svg>
            <div class="gauge-label">
                <div class="gauge-score" style="color:{{ $color }}">{{ $sc }}</div>
                <div class="gauge-sub">Risk</div>
            </div>
        </div>

        {{-- Meta --}}
        <div class="result-meta">
            <div class="result-id">
                {{ $fraudCheck->check_id }} · {{ strtoupper($fraudCheck->type ?? '') }}
            </div>
            <div class="result-name">
                {{ $fraudCheck->customer_name ?: ($fraudCheck->input_value ?: '—') }}
            </div>
            <div class="result-email">
                {{ $fraudCheck->customer_email ?: 'No email provided' }}
            </div>
            <div>
                <span class="badge-risk {{ $fraudCheck->risk_level ?? 'low' }}">
                    {{ strtoupper($fraudCheck->risk_level ?? 'unknown') }} RISK
                </span>
                <span class="badge-status {{ $fraudCheck->status ?? 'pending' }}">
                    {{ ucfirst($fraudCheck->status ?? 'pending') }}
                </span>
                @if($fraudCheck->vpn_detected)   <span class="badge-risk medium">VPN</span>@endif
                @if($fraudCheck->proxy_detected) <span class="badge-risk medium">PROXY</span>@endif
                @if($fraudCheck->tor_detected)   <span class="badge-risk critical">TOR</span>@endif
            </div>
        </div>

        {{-- Timestamps --}}
        <div style="text-align:right; margin-left:auto; flex-shrink:0">
            <div style="font-size:.7rem; color:var(--fc-muted); margin-bottom:.3rem">Checked</div>
            <div style="font-size:.82rem; font-weight:600">
                {{-- ✅ null-safe --}}
                {{ $fraudCheck->created_at ? $fraudCheck->created_at->format('d M Y H:i') : '—' }}
            </div>
            @if($fraudCheck->reviewed_by)
            <div style="font-size:.68rem; color:var(--fc-muted); margin-top:.5rem">
                Reviewed by {{ optional($fraudCheck->reviewer)->name ?? '—' }}<br>
                {{-- ✅ null-safe: reviewed_at null হলে format() crash করবে না --}}
                {{ $fraudCheck->reviewed_at ? $fraudCheck->reviewed_at->format('d M Y H:i') : '—' }}
            </div>
            @endif
        </div>

    </div>{{-- /.result-header --}}

    <div class="detail-grid">

        {{-- ══════════ LEFT COLUMN ══════════ --}}
        <div>

            {{-- Flags --}}
            @if(!empty($fraudCheck->flags))
            <div class="fc-card">
                <div class="fc-card-hdr">
                    <i class="fas fa-flag" style="color:var(--fc-danger)"></i>
                    Risk Flags ({{ count($fraudCheck->flags) }})
                </div>
                <div style="padding:1rem 1.25rem">
                    @foreach($fraudCheck->flags as $flag)
                        <span class="flag-pill">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ str_replace('_', ' ', $flag) }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Network & Location --}}
            <div class="fc-card">
                <div class="fc-card-hdr">
                    <i class="fas fa-globe" style="color:var(--fc-accent)"></i>
                    Network & Location
                </div>
                @foreach([
                    ['IP Address',     $fraudCheck->ip_address    ?: 'Not provided', 'unknown'],
                    ['Country',        $fraudCheck->country       ?: 'Unknown',      'unknown'],
                    ['City',           $fraudCheck->city          ?: 'Unknown',      'unknown'],
                    ['VPN Detected',   $fraudCheck->vpn_detected   ? '⚠ Yes' : '✓ No', $fraudCheck->vpn_detected   ? 'warning' : 'safe'],
                    ['Proxy Detected', $fraudCheck->proxy_detected ? '⚠ Yes' : '✓ No', $fraudCheck->proxy_detected ? 'warning' : 'safe'],
                    ['Tor Network',    $fraudCheck->tor_detected   ? '🚨 Yes' : '✓ No', $fraudCheck->tor_detected  ? 'danger'  : 'safe'],
                ] as [$key, $val, $dot])
                <div class="signal-row">
                    <div class="signal-dot {{ $dot }}"></div>
                    <div class="signal-key">{{ $key }}</div>
                    <div>{{ $val }}</div>
                </div>
                @endforeach
            </div>

            {{-- Email Analysis --}}
            @if($fraudCheck->customer_email)
            <div class="fc-card">
                <div class="fc-card-hdr">
                    <i class="fas fa-envelope" style="color:var(--fc-accent)"></i>
                    Email Analysis
                </div>
                @foreach([
                    ['Email',       $fraudCheck->customer_email,                                                        'unknown'],
                    ['Valid Format',$fraudCheck->email_valid      ? '✓ Yes' : '✗ No',   $fraudCheck->email_valid      ? 'safe'    : 'danger'],
                    ['Disposable',  $fraudCheck->email_disposable ? '⚠ Yes' : '✓ No',   $fraudCheck->email_disposable ? 'warning' : 'safe'],
                    ['Domain',      $fraudCheck->email_domain     ?: '—',                'unknown'],
                    ['Domain Age',  $fraudCheck->email_domain_age ? $fraudCheck->email_domain_age.' days' : '—', 'unknown'],
                ] as [$key, $val, $dot])
                <div class="signal-row">
                    <div class="signal-dot {{ $dot }}"></div>
                    <div class="signal-key">{{ $key }}</div>
                    <div>{{ $val }}</div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Phone Analysis --}}
            @if($fraudCheck->customer_phone)
            <div class="fc-card">
                <div class="fc-card-hdr">
                    <i class="fas fa-phone" style="color:var(--fc-accent)"></i>
                    Phone Analysis
                </div>
                @foreach([
                    ['Phone',   $fraudCheck->customer_phone,                                      'unknown'],
                    ['Valid',   $fraudCheck->phone_valid  ? '✓ Valid' : '✗ Invalid',             $fraudCheck->phone_valid ? 'safe' : 'danger'],
                    ['Type',    ucfirst($fraudCheck->phone_type ?: '—'),                         ($fraudCheck->phone_type === 'voip') ? 'warning' : 'safe'],
                    ['Carrier', $fraudCheck->phone_carrier ?: '—',                               'unknown'],
                    ['Country', $fraudCheck->phone_country ?: '—',                               'unknown'],
                ] as [$key, $val, $dot])
                <div class="signal-row">
                    <div class="signal-dot {{ $dot }}"></div>
                    <div class="signal-key">{{ $key }}</div>
                    <div>{{ $val }}</div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Triggered Rules --}}
            @if(!empty($fraudCheck->triggered_rules))
            <div class="fc-card">
                <div class="fc-card-hdr">
                    <i class="fas fa-code-branch" style="color:var(--fc-accent)"></i>
                    Triggered Rules ({{ count($fraudCheck->triggered_rules) }})
                </div>
                @foreach($fraudCheck->triggered_rules as $rule)
                    @php $impact = (int) ($rule['impact'] ?? 0); @endphp
                    <div class="rule-row">
                        <div>
                            <div class="rule-code">{{ $rule['rule'] ?? '—' }}</div>
                            <div style="font-size:.78rem; margin-top:2px">
                                {{ $rule['name'] ?? $rule['rule'] ?? '—' }}
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px">
                            <span style="font-size:.72rem; color:var(--fc-muted)">
                                {{ ucfirst($rule['action'] ?? '') }}
                            </span>
                            <span class="impact-pill {{ $impact > 0 ? 'pos' : ($impact < 0 ? 'neg' : 'neu') }}">
                                {{ $impact > 0 ? '+' : '' }}{{ $impact }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Social Profiles --}}
            @if(!empty($fraudCheck->social_profiles))
            <div class="fc-card">
                <div class="fc-card-hdr">
                    <i class="fas fa-share-alt" style="color:var(--fc-accent)"></i>
                    Social Profiles
                </div>
                <div style="padding:1rem 1.25rem; display:flex; gap:10px; flex-wrap:wrap">
                    @foreach($fraudCheck->social_profiles as $platform => $found)
                    <div style="display:flex; align-items:center; gap:6px; background:rgba(255,255,255,.04); border:1px solid var(--fc-border); border-radius:8px; padding:6px 12px; font-size:.78rem">
                        <i class="fab fa-{{ $platform }}"
                           style="color:{{ $found ? 'var(--fc-success)' : 'var(--fc-muted)' }}"></i>
                        {{ ucfirst($platform) }}
                        <span style="color:{{ $found ? 'var(--fc-success)' : 'var(--fc-muted)' }}">
                            {{ $found ? '✓' : '✗' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>{{-- /left --}}

        {{-- ══════════ RIGHT COLUMN ══════════ --}}
        <div>

            {{-- Update Decision --}}
            <div class="fc-card">
                <div class="fc-card-hdr">
                    <i class="fas fa-gavel"></i> Update Decision
                </div>
                {{--
                    ✅ FIX: route parameter name হলো {check} কারণ
                    resource name = 'checks' → singular = 'check'
                    Model object pass করলে Laravel নিজেই সঠিক parameter bind করে।
                    ->id pass করলে "Missing parameter: check" error আসে।
                --}}
                <form method="POST"
                      action="{{ route('admin.fraud.update', $fraudCheck) }}"
                      style="padding:1.25rem">
                    @csrf
                    @method('PUT')

                    <div style="margin-bottom:1rem">
                        <label class="fc-label">Status</label>
                        <select name="status" class="fc-control">
                            @foreach([
                                'approved' => 'Approved',
                                'review'   => 'In Review',
                                'declined' => 'Declined',
                                'pending'  => 'Pending',
                            ] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ ($fraudCheck->status ?? '') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-bottom:1rem">
                        <label class="fc-label">Notes</label>
                        <textarea name="notes" class="fc-control" rows="4"
                            placeholder="Investigation notes…">{{ $fraudCheck->notes }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary"
                            style="width:100%; justify-content:center">
                        <i class="fas fa-save"></i> Save Decision
                    </button>
                </form>
            </div>

            {{-- Customer Summary --}}
            <div class="fc-card">
                <div class="fc-card-hdr">
                    <i class="fas fa-user"></i> Customer Summary
                </div>
                <table class="info-table">
                    <tr><td>Name</td>       <td>{{ $fraudCheck->customer_name  ?: '—' }}</td></tr>
                    <tr><td>Email</td>      <td>{{ $fraudCheck->customer_email ?: '—' }}</td></tr>
                    <tr><td>Phone</td>      <td>{{ $fraudCheck->customer_phone ?: '—' }}</td></tr>
                    <tr><td>Type</td>       <td>{{ ucfirst($fraudCheck->type   ?? '—') }}</td></tr>
                    @if($fraudCheck->transaction_amount)
                    <tr>
                        <td>Amount</td>
                        <td>
                            {{ number_format($fraudCheck->transaction_amount, 2) }}
                            {{ $fraudCheck->transaction_currency ?? '' }}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td>Created by</td>
                        <td>{{ optional($fraudCheck->creator)->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td>Created at</td>
                        {{-- ✅ null-safe --}}
                        <td>{{ $fraudCheck->created_at ? $fraudCheck->created_at->format('d M Y H:i') : '—' }}</td>
                    </tr>
                    @if($fraudCheck->updated_at)
                    <tr>
                        <td>Updated at</td>
                        <td>{{ $fraudCheck->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            {{-- Device --}}
            @if($fraudCheck->device_type || $fraudCheck->browser || $fraudCheck->os)
            <div class="fc-card">
                <div class="fc-card-hdr">
                    <i class="fas fa-laptop"></i> Device
                </div>
                <table class="info-table">
                    <tr><td>Device</td>  <td>{{ ucfirst($fraudCheck->device_type ?: '—') }}</td></tr>
                    <tr><td>Browser</td> <td>{{ $fraudCheck->browser ?: '—' }}</td></tr>
                    <tr><td>OS</td>      <td>{{ $fraudCheck->os ?: '—' }}</td></tr>
                </table>
            </div>
            @endif

            {{-- Alerts --}}
            @if($fraudCheck->alerts->isNotEmpty())
            <div class="fc-card">
                <div class="fc-card-hdr">
                    <i class="fas fa-bell" style="color:var(--fc-danger)"></i>
                    Alerts ({{ $fraudCheck->alerts->count() }})
                </div>
                @foreach($fraudCheck->alerts as $alert)
                <div style="padding:.75rem 1.25rem; border-bottom:1px solid var(--fc-border); font-size:.78rem">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px">
                        <span style="width:8px; height:8px; border-radius:50%; flex-shrink:0;
                            background:{{ ($alert->severity ?? '') === 'critical'
                                ? 'var(--fc-danger)' : 'var(--fc-warning)' }}">
                        </span>
                        <div style="font-weight:500">{{ $alert->title ?? '—' }}</div>
                    </div>
                    <div style="color:var(--fc-muted); font-size:.68rem; padding-left:16px">
                        {{ ucfirst($alert->status ?? '') }}
                        {{-- ✅ null-safe --}}
                        · {{ $alert->created_at ? $alert->created_at->diffForHumans() : '—' }}
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Actions --}}
            <div class="d-flex gap-2">
                {{--
                    ✅ FIX: $fraudCheck model pass করা হচ্ছে, ->id নয়।
                    resource 'checks' → parameter {check}
                    Model pass করলে Laravel getRouteKey() দিয়ে নিজেই bind করে।
                --}}
                <a href="{{ route('admin.fraud.edit', $fraudCheck) }}"
                   class="btn btn-ghost"
                   style="flex:1; justify-content:center">
                    <i class="fas fa-edit"></i> Edit
                </a>

                <form method="POST"
                      action="{{ route('admin.fraud.destroy', $fraudCheck) }}"
                      style="flex:1"
                      onsubmit="return confirmDelete(event)">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="btn btn-danger"
                            style="width:100%; justify-content:center">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>

        </div>{{-- /right --}}

    </div>{{-- /.detail-grid --}}
</div>{{-- /.fc-wrap --}}

<script>
function confirmDelete(e) {
    if (!confirm('Are you sure you want to delete this check permanently?')) {
        e.preventDefault();
        return false;
    }
    return true;
}
</script>
@endsection
