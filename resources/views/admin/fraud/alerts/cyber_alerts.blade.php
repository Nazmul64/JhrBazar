@extends('admin.master')

@section('content')
<style>
    .ca-wrapper { padding: 10px 0; font-family: 'Hind Siliguri', 'Outfit', sans-serif; }
    .ca-header { margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between; }
    .ca-title-group { }
    .ca-title { font-size: 24px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 10px; margin-bottom: 4px; }
    .ca-title i { color: #ef4444; animation: pulseRed 2s infinite ease-in-out; }
    .ca-subtitle { font-size: 14px; color: #64748b; margin: 0; }

    .ca-card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); padding: 24px; border: 1px solid #f1f5f9; }

    .ca-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .ca-table th { background: #f8fafc; padding: 14px 18px; text-align: left; font-weight: 700; color: #475569; font-size: 13px; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; letter-spacing: 0.5px; }
    .ca-table td { padding: 16px 18px; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .ca-table tbody tr:hover { background: #f8fafc; }
    .ca-table .no-data { text-align: center; color: #94a3b8; padding: 40px; font-size: 14px; }

    /* Custom column styles */
    .time-badge { background: #ef4444; color: #fff; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; display: inline-block; margin-bottom: 4px; }
    .time-relative { font-size: 12px; color: #64748b; display: block; }

    .ip-address { font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 2px; }
    .isp-name { font-size: 12px; color: #2563eb; display: flex; align-items: center; gap: 4px; font-weight: 500; }

    .device-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px 8px; font-size: 12px; font-weight: 600; color: #475569; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 4px; }
    .device-agent { font-size: 11px; color: #94a3b8; display: block; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .location-text { font-size: 13px; font-weight: 600; color: #334155; display: flex; align-items: center; gap: 6px; }
    .location-text i { color: #ef4444; }
    .country-text { font-size: 11px; color: #64748b; margin-left: 18px; display: block; }

    /* Button styles */
    .btn-action-map { background: #10b981; color: #fff; border: none; padding: 7px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; box-shadow: 0 2px 5px rgba(16,185,129,0.2); }
    .btn-action-map:hover { background: #059669; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 8px rgba(16,185,129,0.3); }

    .btn-action-delete { background: #fff; color: #ef4444; border: 1px solid #fca5a5; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s; }
    .btn-action-delete:hover { background: #fef2f2; border-color: #ef4444; }

    .action-group { display: flex; gap: 8px; align-items: center; }

    @keyframes pulseRed {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>

<div class="ca-wrapper">
    <div class="ca-header">
        <div class="ca-title-group">
            <h2 class="ca-title">
                <i class="bi bi-exclamation-triangle-fill"></i> Cyber Security Alerts (Live)
            </h2>
            <p class="ca-subtitle">Tracking all fake order attempts and intercepted exact locations.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius:12px; background:#d1fae5; color:#065f46;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="ca-card">
        <div class="table-responsive">
            <table class="ca-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>IP & ISP</th>
                        <th>Device</th>
                        <th>Location (Auto)</th>
                        <th>Exact GPS (Map)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alerts as $alert)
                        <tr>
                            <!-- Time Column -->
                            <td>
                                <span class="time-badge">
                                    {{ $alert->attempted_at ? \Carbon\Carbon::parse($alert->attempted_at)->setTimezone('Asia/Dhaka')->format('d M, h:i A') : 'N/A' }}
                                </span>
                                <span class="time-relative">
                                    {{ $alert->attempted_at ? \Carbon\Carbon::parse($alert->attempted_at)->diffForHumans() : '' }}
                                </span>
                            </td>

                            <!-- IP & ISP Column -->
                            <td>
                                <div class="ip-address">{{ $alert->ip_address }}</div>
                                <div class="isp-name">
                                    <i class="bi bi-wifi"></i> {{ $alert->wifi_provider ?? 'Unknown ISP' }}
                                </div>
                            </td>

                            <!-- Device Column -->
                            <td>
                                <div class="device-box">
                                    <i class="bi bi-{{ strtolower($alert->device_type) === 'mobile' ? 'phone' : 'laptop' }}"></i>
                                    {{ $alert->device_type ?? 'Desktop' }}
                                </div>
                                <span class="device-agent" title="{{ $alert->device_agent }}">
                                    {{ $alert->device_agent }}
                                </span>
                            </td>

                            <!-- Location Column -->
                            <td>
                                <div class="location-text">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    @php
                                        $locParts = explode(',', $alert->location);
                                        $city = trim($locParts[0] ?? 'Unknown');
                                        $region = trim($locParts[1] ?? '');
                                        $country = trim($locParts[2] ?? 'Bangladesh');
                                    @endphp
                                    {{ $city }}{{ $region ? ', ' . $region : '' }}
                                </div>
                                <span class="country-text">{{ $country }}</span>
                            </td>

                            <!-- Exact GPS Column -->
                            <td>
                                <div class="action-group">
                                    <!-- View Exact Map -->
                                    <button type="button"
    class="btn-action-map"
    data-bs-toggle="modal"
    data-bs-target="#locationModal"
    data-lat="{{ $alert->lat }}"
    data-lon="{{ $alert->lon }}">
    <i class="bi bi-map-fill"></i> View Exact Map
</button>
    

                                    <!-- Delete Entry -->
                                    <form action="{{ route('admin.cyber-alerts.destroy', $alert->id) }}" 
                                          method="POST" 
                                          style="display:inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this alert entry?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action-delete">
                                            <i class="bi bi-trash3-fill"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="no-data">
                                <i class="bi bi-shield-check" style="font-size: 32px; display: block; margin-bottom: 8px; color: #10b981;"></i>
                                No cyber alert attempts logged. Your store is secure!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $alerts->links() }}
        </div>
    </div>
</div>
@endsection
