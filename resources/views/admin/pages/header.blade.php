<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', Auth::check() ? ucfirst(Auth::user()->role) . ' Portal' : 'Admin') – Jhr Bazar</title>

    {{-- ── Favicon ── --}}
    {{-- <link rel="shortcut icon" href="{{ asset('admin/assets/images/favicon.png') }}" type="image/x-icon" /> --}}
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
