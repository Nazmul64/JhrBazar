<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <style>
        :root {
            --bg-light: #fdfdfc;
            --bg-dark: #0a0a0a;
            --text-light: #1b1b18;
            --text-dark: #e5e5e5;
            --sidebar-width: 250px;
        }
        html[data-theme='dark'] body {
            background: var(--bg-dark);
            color: var(--text-dark);
        }
        html[data-theme='light'] body {
            background: var(--bg-light);
            color: var(--text-light);
        }
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar { width: var(--sidebar-width); background: rgba(0,0,0,0.05); }
        .admin-content { flex: 1; padding: 1rem; }
        .admin-sidebar a { display: block; padding: 0.75rem 1rem; color: inherit; text-decoration: none; }
        .admin-sidebar a:hover { background: rgba(0,0,0,0.1); }
    </style>
    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">
        @include('partials.admin_sidebar')
        <main class="admin-content">
            @include('partials.admin_header')
            @yield('content')
        </main>
    </div>
    @stack('scripts')
    <script src="{{ asset('js/dark-mode.js') }}"></script>
</body>
</html>
