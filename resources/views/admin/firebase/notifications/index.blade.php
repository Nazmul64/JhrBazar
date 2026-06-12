@extends('admin.master')

@section('content')
<style>
    .notifications-hub {
        padding: 24px;
        background: #f8fafc;
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }
    .hub-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        background: #ffffff;
        overflow: hidden;
    }
    .hub-header {
        padding: 24px 30px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }
    .custom-table {
        width: 100%;
        margin-bottom: 0;
    }
    .custom-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 16px 24px;
        border: none;
    }
    .custom-table td {
        padding: 18px 24px;
        vertical-align: middle;
        font-size: 0.85rem;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .badge-status {
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .badge-success {
        background: #ecfdf5;
        color: #059669;
    }
    .badge-failed {
        background: #fef2f2;
        color: #dc2626;
    }
    .badge-pending {
        background: #fffbeb;
        color: #d97706;
    }
    .notification-content {
        max-width: 320px;
    }
    .notification-title {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 3px;
        font-size: 0.9rem;
    }
    .notification-body {
        color: #64748b;
        font-size: 0.8rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .notification-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .badge-recipient {
        background: #f1f5f9;
        color: #475569;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
    }
</style>

<div class="notifications-hub">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold text-slate-800 m-0">Firebase Notification Hub</h1>
            <p class="text-muted m-0 small">Send real-time alerts and track FCM push notifications delivery logs.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.firebase.settings') }}" class="btn btn-light rounded-pill border px-4" style="font-size: 14px; font-weight: 600;">
                <i class="bi bi-gear-fill me-1"></i> FCM Credentials
            </a>
            <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none; font-size: 14px; font-weight: 600;">
                <i class="bi bi-send-fill me-1"></i> Compose Notification
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 8px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="hub-card">
        <div class="hub-header">
            <h5 class="m-0 fw-bold text-slate-800">Notification Dispatch History</h5>
            @if($settings && $settings->status)
                <span class="badge bg-soft-success text-success d-flex align-items-center gap-1 px-3 py-2 rounded-pill" style="font-size: 12px; font-weight: 600; background: #f0fdf4;">
                    <span style="width:8px; height:8px; background:#10b981; border-radius:50%; display:inline-block; animation: pulse 1.5s infinite;"></span> FCM Channel: Active
                </span>
            @else
                <span class="badge bg-soft-danger text-danger d-flex align-items-center gap-1 px-3 py-2 rounded-pill" style="font-size: 12px; font-weight: 600; background: #fef2f2;">
                    <span style="width:8px; height:8px; background:#ef4444; border-radius:50%; display:inline-block;"></span> FCM Channel: Inactive / Unconfigured
                </span>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th width="80">Image</th>
                        <th>Notification Content</th>
                        <th>Recipient</th>
                        <th>Status</th>
                        <th>API Log / Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notif)
                    <tr>
                        <td style="white-space: nowrap;">
                            <div class="fw-bold">{{ $notif->created_at->format('d M, Y') }}</div>
                            <div class="text-muted small">{{ $notif->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            @if($notif->image_url)
                                <img src="{{ $notif->image_url }}" class="notification-image" alt="Image">
                            @else
                                <div class="bg-light text-muted rounded d-flex align-items-center justify-content-center" style="width:50px; height:50px; border:1px solid #e2e8f0; font-size: 18px;">
                                    📢
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="notification-content">
                                <div class="notification-title">{{ $notif->title }}</div>
                                <div class="notification-body" title="{{ $notif->body }}">{{ $notif->body }}</div>
                            </div>
                        </td>
                        <td>
                            @if($notif->user)
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-slate-800">{{ $notif->user->name }}</span>
                                    <span class="text-muted small">{{ $notif->user->phone }}</span>
                                </div>
                            @else
                                <span class="badge bg-primary text-white rounded-pill px-3 py-1" style="font-size: 10px; font-weight: 700; letter-spacing: 0.5px;">All Customers</span>
                            @endif
                        </td>
                        <td>
                            @if($notif->status === 'success')
                                <span class="badge-status badge-success"><i class="bi bi-check-circle-fill"></i> Sent</span>
                            @elseif($notif->status === 'failed')
                                <span class="badge-status badge-failed"><i class="bi bi-x-circle-fill"></i> Failed</span>
                            @else
                                <span class="badge-status badge-pending"><i class="bi bi-hourglass-split"></i> Pending</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $resp = json_decode($notif->response, true);
                                $reason = '';
                                if ($resp) {
                                    if (isset($resp['errors']) && !empty($resp['errors'])) {
                                        $reason = implode(', ', $resp['errors']);
                                    } elseif (isset($resp['message'])) {
                                        $reason = $resp['message'];
                                    } elseif (isset($resp['success_count'])) {
                                        $reason = "Success: " . $resp['success_count'] . ", Failed: " . $resp['failure_count'];
                                    }
                                } else {
                                    $reason = $notif->response;
                                }
                            @endphp
                            <div class="text-truncate text-muted small" style="max-width: 250px;" title="{{ $reason }}">
                                {{ $reason ?: 'N/A' }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-bell-slash fs-1 text-muted d-block mb-3"></i>
                            <span class="text-muted fw-medium">No push notifications have been sent yet.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($notifications->hasPages())
        <div class="p-4 border-top">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
</style>
@endsection
