@extends('admin.master')

@section('content')
<div style="background:#f3f4f6; min-height:100vh; padding:24px;">

    {{-- Header --}}
    <div style="background:#fff; border-radius:12px; padding:20px 24px;
                display:flex; align-items:center; justify-content:space-between;
                margin-bottom:16px; box-shadow:0 1px 3px rgba(0,0,0,.07);">
        <h1 style="font-size:20px; font-weight:700; color:#111; margin:0;">
            Theme Color — {{ $setting->palette_name ?? 'Detail View' }}
        </h1>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('admin.themecolorssettings.edit', $setting->id) }}"
               style="background:#eb2e61; color:#fff; text-decoration:none; border-radius:8px;
                      padding:10px 18px; font-size:14px; font-weight:600;">
                Edit
            </a>
            <a href="{{ route('admin.themecolorssettings.index') }}"
               style="background:#6b7280; color:#fff; text-decoration:none; border-radius:8px;
                      padding:10px 18px; font-size:14px; font-weight:600;">
                ← Back
            </a>
        </div>
    </div>

    {{-- Current Color --}}
    <div style="background:#fff; border-radius:12px; margin-bottom:16px;
                box-shadow:0 1px 3px rgba(0,0,0,.07); overflow:hidden;">

        <div style="padding:16px 24px; border-bottom:1px solid #f0f0f0;">
            <h2 style="font-size:15px; font-weight:700; color:#111; margin:0;">Current Color</h2>
        </div>

        <div style="padding:24px; display:flex; gap:16px; flex-wrap:wrap;">
            <div style="border:1px solid #e5e7eb; border-radius:10px; overflow:hidden;
                        width:160px; box-shadow:0 1px 4px rgba(0,0,0,.06);">
                <div style="height:110px; background:{{ $setting->primary_color }};"></div>
                <div style="background:#1f2937; padding:10px 12px;">
                    <p style="color:#fff; font-size:13px; font-weight:600; margin:0;">Primary</p>
                    <p style="color:#9ca3af; font-size:12px; margin:4px 0 0;">
                        {{ $setting->primary_color }}
                    </p>
                </div>
            </div>

            <div style="border:1px solid #e5e7eb; border-radius:10px; overflow:hidden;
                        width:160px; box-shadow:0 1px 4px rgba(0,0,0,.06);">
                <div style="height:110px; background:{{ $setting->secondary_color }};"></div>
                <div style="background:#1f2937; padding:10px 12px;">
                    <p style="color:#fff; font-size:13px; font-weight:600; margin:0;">Secondary</p>
                    <p style="color:#9ca3af; font-size:12px; margin:4px 0 0;">
                        {{ $setting->secondary_color }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Palette --}}
    <div style="background:#fff; border-radius:12px;
                box-shadow:0 1px 3px rgba(0,0,0,.07); overflow:hidden;">

        <div style="padding:16px 24px; border-bottom:1px solid #f0f0f0;">
            <h2 style="font-size:15px; font-weight:700; color:#111; margin:0;">
                Available Colors palette
            </h2>
        </div>

        <div style="padding:24px;">
            <div style="border:2px solid {{ $setting->primary_color }}; border-radius:10px;
                        padding:20px; display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">

                @foreach($palette as $shade => $hex)
                    <div style="text-align:center; min-width:80px;">
                        @if($shade == '500')
                            <div style="width:110px; height:100px; border-radius:8px;
                                        background:{{ $hex }}; position:relative;">
                                <span style="position:absolute; bottom:6px; left:50%;
                                             transform:translateX(-50%);
                                             background:#000; color:#fff; font-size:11px;
                                             font-weight:700; padding:2px 8px; border-radius:4px;
                                             white-space:nowrap;">
                                    {{ ucfirst($hex) }}
                                </span>
                            </div>
                        @else
                            <div style="width:80px; height:75px; border-radius:8px;
                                        background:{{ $hex }};"></div>
                        @endif
                        <p style="font-size:11px; color:#6b7280; margin:6px 0 0; line-height:1.3;">
                            {{ $shade }}<br>{{ $hex }}
                        </p>
                    </div>
                @endforeach

            </div>
        </div>
    </div>

</div>
@endsection
