<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        // Reuse settings already embedded in homeData to avoid an extra DB query.
        $setting = isset($homeData->data->settings) ? (object) (array) $homeData->data->settings : \App\Models\GenaralSetting::first();
        $websiteName = $setting ? $setting->website_name : 'JhrBazar';
    @endphp
    <title>{{ $websiteName }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @viteReactRefresh
    @vite(['resources/sass/app.scss', 'resources/js/app.jsx'])
    <script>
        window.initialSettings = {!! json_encode($setting) !!};
        window.initialHomeData = {!! isset($homeData) ? json_encode($homeData) : 'null' !!};
    </script>
    <style>
        :root {
            --primary-color: {{ $setting->primary_color ?? '#57b500' }};
            --top-header-bg: {{ $setting->top_header_color ?? '#57b500' }};
            --header-bg: {{ $setting->header_color ?? '#ffffff' }};
            --button-color: {{ $setting->button_color ?? '#57b500' }};
        }
    </style>
    @php
        $trackingSetting = \App\Models\TrackingSetting::first();
    @endphp
    @if($trackingSetting && $trackingSetting->is_active)
        @if($trackingSetting->custom_head_script)
            {!! $trackingSetting->custom_head_script !!}
        @endif
    @endif
</head>
<body class="bg-light">
    @if($trackingSetting && $trackingSetting->is_active && $trackingSetting->custom_body_script)
        {!! $trackingSetting->custom_body_script !!}
    @endif
    <div id="react-app"></div>
</body>
</html>

