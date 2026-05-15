@php $setting = \App\Models\GenaralSetting::first(); @endphp
<!DOCTYPE html>
<html lang="en" data-theme="{{ $setting->admin_theme ?? 'light' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', Auth::check() ? ucfirst(Auth::user()->role) . ' Portal' : 'Admin') – {{ $setting->website_name ?? 'Jhr Bazar' }}</title>

    {{-- ── Favicon ── --}}
    @if($setting && $setting->favicon_url)
        <link rel="shortcut icon" href="{{ $setting->favicon_url }}" type="image/x-icon" />
    @endif
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    {{-- ── Google Fonts ── --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Sora:wght@600;700&display=swap" rel="stylesheet" />

    {{-- ── Bootstrap 5.3.3 CSS ── --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    {{-- ── Bootstrap Icons 1.11.3 ── --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />

    {{-- ── SweetAlert2 ── --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />

    {{-- ── FontAwesome 6.5.1 ── --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    {{-- ── Select2 ── --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- ── Summernote (rich text editor) ── --}}
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet" />

    {{-- ── DataTables ── --}}
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

    {{-- ── Custom Admin CSS ── --}}
    <link rel="stylesheet" href="{{ asset('admin/assets/css/style.css') }}" />

    <style>
        :root {
            --bg-body: #f1f5f9;
            --bg-card: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: rgba(0,0,0,0.05);
            --glass-bg: rgba(255,255,255,0.7);
            --header-bg: rgba(255, 255, 255, 0.9);
        }

        [data-theme="dark"] {
            --bg-body: #0b0e14;
            --bg-card: #151921;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --border-color: rgba(255,255,255,0.05);
            --glass-bg: rgba(21, 25, 33, 0.8);
            --header-bg: rgba(9, 11, 16, 0.9);
        }

        body {
            background-color: var(--bg-body) !important;
            color: var(--text-main) !important;
            transition: background-color 0.3s, color 0.3s;
        }

        #main {
            background-color: var(--bg-body) !important;
        }

        .card, .table-card-premium, .stat-card-premium, .wallet-card-premium {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
            color: var(--text-main) !important;
        }

        .text-muted { color: var(--text-muted) !important; }
        
        #header {
            background-color: var(--header-bg) !important;
            border-bottom-color: var(--border-color) !important;
        }

        [data-theme="dark"] .table-header-premium { background: #1a1f29 !important; }
        [data-theme="dark"] .custom-table-premium thead th { background: #1e2430 !important; color: #94a3b8 !important; }
        [data-theme="dark"] .custom-table-premium tbody td { border-bottom-color: rgba(255,255,255,0.05) !important; color: #e2e8f0 !important; }
        [data-theme="dark"] .btn-light { background: #1e293b !important; border-color: rgba(255,255,255,0.1) !important; color: #f1f5f9 !important; }
        [data-theme="dark"] input, [data-theme="dark"] select, [data-theme="dark"] textarea { background-color: #1a1d27 !important; color: #f1f5f9 !important; border-color: rgba(255,255,255,0.1) !important; }
    </style>
</head>
<body>
    {{-- ── Custom Admin JS ── --}}
    <script src="{{ asset('admin/assets/js/app.js') }}"></script>

    {{-- ── Global Flash Messages (SweetAlert2) ── --}}
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 2500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
            });
        });
    </script>
    @endif

    {{-- ── Delete Confirmation Script moved to footer or wrapped ── --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof $ !== 'undefined') {
                $(document).on('click', '.btn-delete-confirm', function (e) {
                    e.preventDefault();
                    var form = $(this).closest('form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This action cannot be undone!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e53e3e',
                        cancelButtonColor:  '#6c757d',
                        confirmButtonText:  'Yes, delete it!',
                        cancelButtonText:   'Cancel',
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }
        });
    </script>
