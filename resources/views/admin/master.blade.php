
@include('admin/pages/header')

@if(auth()->user()->role === 'admin')
    @include('admin/pages/sidebar')
@elseif(auth()->user()->role === 'manager')
    @include('admin/pages/sidebar_manager')
@elseif(auth()->user()->role === 'seller')
    @include('admin/pages/sidebar_seller')
@else
    @include('admin/pages/sidebar_staff')
@endif

<!-- ══════════════ HEADER ══════════════ -->
@include('admin/pages/header2')
<!-- ══════════════ MAIN ══════════════ -->
<main id="main">
     @yield('content')
</main>
@include('admin/pages/footer')
