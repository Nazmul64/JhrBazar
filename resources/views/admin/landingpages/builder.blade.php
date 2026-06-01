{{-- 
    Landing Page Builder — Admin Embedded View
    This view intentionally does NOT extend admin.master.
    It renders a minimal admin-style header (matching screenshot) and mounts the React builder.
--}}
@php
    $setting = \App\Models\GenaralSetting::first();
    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="en" data-theme="{{ $setting->admin_theme ?? 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Page Builder – {{ $landingpage->title }}</title>

    {{-- Icons & Fonts --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0; padding: 0;
            font-family: 'DM Sans', 'Segoe UI', system-ui, sans-serif;
            background: #f1f5f9;
        }

        /* ── Minimal Admin Header ── */
        .builder-topbar {
            position: sticky; top: 0; z-index: 999;
            height: 56px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center;
            padding: 0 20px;
            gap: 16px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }

        /* Hamburger / menu icon */
        .topbar-menu-btn {
            background: none; border: none; cursor: pointer;
            color: #475569; padding: 6px; border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; transition: background .15s;
        }
        .topbar-menu-btn:hover { background: #f1f5f9; }

        /* Search */
        .topbar-search {
            flex: 1; max-width: 300px;
            display: flex; align-items: center;
            background: #f8fafc; border: 1.5px solid #e2e8f0;
            border-radius: 8px; padding: 0 12px; gap: 8px; height: 36px;
        }
        .topbar-search input {
            border: none; background: transparent; outline: none;
            font-size: 13px; color: #1e293b; font-family: inherit;
            flex: 1;
        }
        .topbar-search i { color: #94a3b8; font-size: 14px; }

        /* Right icons */
        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: 8px; }
        .topbar-icon-btn {
            width: 36px; height: 36px; border-radius: 8px;
            background: #f8fafc; border: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: center;
            color: #475569; font-size: 16px; cursor: pointer; position: relative;
            text-decoration: none; transition: background .15s;
        }
        .topbar-icon-btn:hover { background: #f1f5f9; color: #1e293b; }
        .topbar-badge {
            position: absolute; top: -4px; right: -4px;
            width: 16px; height: 16px; border-radius: 50%;
            background: #ef4444; color: #fff; font-size: 9px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #fff;
        }

        /* User avatar */
        .topbar-user {
            display: flex; align-items: center; gap: 8px;
            cursor: pointer; padding: 4px 10px 4px 4px;
            border-radius: 8px; transition: background .15s;
        }
        .topbar-user:hover { background: #f1f5f9; }
        .topbar-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 13px; font-weight: 700; flex-shrink: 0;
            overflow: hidden;
        }
        .topbar-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .topbar-username { font-size: 13px; font-weight: 600; color: #1e293b; }
        .topbar-role { font-size: 11px; color: #64748b; }
        .topbar-chevron { color: #94a3b8; font-size: 12px; }

        /* ── Builder container ── */
        #landing-builder-root {
            min-height: calc(100vh - 56px);
        }
    </style>
</head>
<body>

{{-- Minimal Admin Topbar (matching screenshot 4) --}}
<div class="builder-topbar">
    {{-- Menu icon --}}
    <button class="topbar-menu-btn" title="Toggle Sidebar">
        <i class="bi bi-list"></i>
    </button>

    {{-- Search --}}
    <div class="topbar-search">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Search...">
    </div>

    {{-- Right Side Icons --}}
    <div class="topbar-right">
        {{-- Globe --}}
        <a href="/" target="_blank" class="topbar-icon-btn" title="Visit Website">
            <i class="bi bi-globe2"></i>
        </a>

        {{-- Cart --}}
        <a href="{{ route('admin.orders.index') }}" class="topbar-icon-btn" title="Orders">
            <i class="bi bi-cart3"></i>
            <span class="topbar-badge">0</span>
        </a>

        {{-- Notifications --}}
        <a href="#" class="topbar-icon-btn" title="Notifications">
            <i class="bi bi-bell"></i>
            <span class="topbar-badge">0</span>
        </a>

        {{-- Messages --}}
        <a href="#" class="topbar-icon-btn" title="Messages">
            <i class="bi bi-chat-dots"></i>
            <span class="topbar-badge">0</span>
        </a>

        {{-- User --}}
        <div class="topbar-user">
            <div class="topbar-avatar">
                @if($user && $user->profile_photo)
                    <img src="{{ asset($user->profile_photo) }}" alt="{{ $user->name }}">
                @else
                    {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                @endif
            </div>
            <div>
                <div class="topbar-username">{{ $user->name ?? 'Admin' }}</div>
                <div class="topbar-role">{{ ucfirst($user->role ?? 'Admin') }}</div>
            </div>
            <i class="bi bi-chevron-down topbar-chevron"></i>
        </div>
    </div>
</div>

{{-- Pass data to the React builder --}}
<script>
    window.__BUILDER_PAGE_ID__    = {{ $landingpage->id }};
    window.__BUILDER_PAGE_SLUG__  = "{{ $landingpage->slug }}";
    window.__BUILDER_PAGE_TITLE__ = "{{ addslashes($landingpage->title) }}";
    window.__CSRF_TOKEN__         = "{{ csrf_token() }}";
    window.__IS_ADMIN_BUILDER__   = true;
    window.__BACK_URL__           = "{{ route('admin.landingpages.index') }}";
    window.__PREVIEW_URL__        = "/l/{{ $landingpage->slug }}";
</script>

{{-- React Builder mounts here --}}
<div id="landing-builder-root"></div>

{{-- Scripts --}}
@viteReactRefresh
@vite(['resources/js/builder-entry.jsx'])

</body>
</html>
