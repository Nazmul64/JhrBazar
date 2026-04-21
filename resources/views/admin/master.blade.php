
@include('admin/pages/header')
@include('admin/pages/sidebar')

<!-- ══════════════ HEADER ══════════════ -->
@include('admin/pages/header2')
<!-- ══════════════ MAIN ══════════════ -->
<main id="main">
     @yield('content')
</main>
@include('admin/pages/footer')
