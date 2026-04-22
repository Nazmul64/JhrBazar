@extends('admin.master')
@section('content')
<div style="background:#f3f4f6; min-height:100vh; padding:24px;">

    {{-- Header --}}
    <div style="background:#fff; border-radius:12px; padding:20px 24px;
                display:flex; align-items:center; justify-content:space-between;
                margin-bottom:16px; box-shadow:0 1px 3px rgba(0,0,0,.07);">
        <h1 style="font-size:20px; font-weight:700; color:#111; margin:0;">
            Edit Theme Color Setting
        </h1>
        <a href="{{ route('admin.themecolorssettings.index') }}"
           style="background:#6b7280; color:#fff; text-decoration:none; border-radius:8px;
                  padding:10px 18px; font-size:14px; font-weight:600;">
            ← Back
        </a>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div style="background:#fee2e2; color:#991b1b; border:1px solid #fca5a5;
                    padding:12px 18px; border-radius:8px; margin-bottom:16px; font-size:14px;">
            @foreach($errors->all() as $error)
                <p style="margin:2px 0;">• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

        {{-- Form Card --}}
        <div style="background:#fff; border-radius:12px; padding:24px;
                    box-shadow:0 1px 3px rgba(0,0,0,.07);">

            <h2 style="font-size:15px; font-weight:700; color:#111; margin:0 0 20px;">
                Color Settings
            </h2>

            <form action="{{ route('admin.themecolorssettings.update', $setting->id) }}"
                  method="POST">
                @csrf
                @method('PUT')

                {{-- Palette Name --}}
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:600;
                                  color:#374151; margin-bottom:6px;">
                        Palette Name (optional)
                    </label>
                    <input type="text" name="palette_name"
                           value="{{ old('palette_name', $setting->palette_name) }}"
                           placeholder="e.g. Brand Pink"
                           style="width:100%; border:1px solid #d1d5db; border-radius:8px;
                                  padding:10px 14px; font-size:14px; color:#374151;
                                  outline:none; box-sizing:border-box;">
                </div>

                {{-- Primary Color --}}
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:600;
                                  color:#374151; margin-bottom:6px;">Primary Color *</label>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <input type="color" id="editPrimaryPicker"
                               value="{{ old('primary_color', $setting->primary_color) }}"
                               onchange="syncPrimary(this.value)"
                               style="width:44px; height:44px; border:2px solid #e5e7eb;
                                      border-radius:6px; cursor:pointer; padding:2px;">
                        <input type="text" name="primary_color" id="editPrimaryHex"
                               value="{{ old('primary_color', $setting->primary_color) }}"
                               oninput="onPrimaryHexInput(this.value)"
                               style="flex:1; border:1px solid #d1d5db; border-radius:8px;
                                      padding:10px 14px; font-size:14px; color:#374151;
                                      outline:none;">
                    </div>
                </div>

                {{-- Secondary Color --}}
                <div style="margin-bottom:24px;">
                    <label style="display:block; font-size:13px; font-weight:600;
                                  color:#374151; margin-bottom:6px;">
                        Secondary Color *
                        <span style="font-weight:400; color:#6b7280;"> (auto-derived or custom)</span>
                    </label>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <input type="color" id="editSecondaryPicker"
                               value="{{ old('secondary_color', $setting->secondary_color) }}"
                               onchange="syncSecondary(this.value)"
                               style="width:44px; height:44px; border:2px solid #e5e7eb;
                                      border-radius:6px; cursor:pointer; padding:2px;">
                        <input type="text" name="secondary_color" id="editSecondaryHex"
                               value="{{ old('secondary_color', $setting->secondary_color) }}"
                               oninput="onSecondaryHexInput(this.value)"
                               style="flex:1; border:1px solid #d1d5db; border-radius:8px;
                                      padding:10px 14px; font-size:14px; color:#374151;
                                      outline:none;">
                    </div>
                </div>

                <button type="submit"
                        style="background:#eb2e61; color:#fff; border:none; border-radius:8px;
                               padding:11px 28px; font-size:14px; font-weight:600; cursor:pointer;">
                    Save And Update
                </button>
            </form>
        </div>

        {{-- Palette Preview --}}
        <div style="background:#fff; border-radius:12px; padding:24px;
                    box-shadow:0 1px 3px rgba(0,0,0,.07);">

            <h2 style="font-size:15px; font-weight:700; color:#111; margin:0 0 20px;">
                Palette Preview
            </h2>

            <div style="display:flex; gap:12px; margin-bottom:20px;">
                <div style="text-align:center;">
                    <div id="previewPrimary"
                         style="width:80px; height:60px; border-radius:8px;
                                background:{{ $setting->primary_color }};"></div>
                    <p style="font-size:11px; color:#6b7280; margin:4px 0 0;">Primary</p>
                </div>
                <div style="text-align:center;">
                    <div id="previewSecondary"
                         style="width:80px; height:60px; border-radius:8px;
                                background:{{ $setting->secondary_color }};"></div>
                    <p style="font-size:11px; color:#6b7280; margin:4px 0 0;">Secondary</p>
                </div>
            </div>

            <div id="editPaletteList" style="border-radius:8px; overflow:hidden;">
                @foreach($palette as $shade => $hex)
                    <div style="padding:10px 14px; background:{{ $hex }};
                                font-size:13px; font-weight:500;
                                color:{{ in_array($shade, ['600','700','800','900','950']) ? '#fff' : '#374151' }};">
                        {{ $shade }}: {{ $hex }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function syncPrimary(val) {
    document.getElementById('editPrimaryHex').value = val;
    document.getElementById('previewPrimary').style.background = val;
    refreshEditPalette(val);
}
function onPrimaryHexInput(val) {
    if (/^#[0-9a-fA-F]{6}$/.test(val)) {
        document.getElementById('editPrimaryPicker').value = val;
        document.getElementById('previewPrimary').style.background = val;
        refreshEditPalette(val);
    }
}
function syncSecondary(val) {
    document.getElementById('editSecondaryHex').value = val;
    document.getElementById('previewSecondary').style.background = val;
}
function onSecondaryHexInput(val) {
    if (/^#[0-9a-fA-F]{6}$/.test(val)) {
        document.getElementById('editSecondaryPicker').value = val;
        document.getElementById('previewSecondary').style.background = val;
    }
}
function refreshEditPalette(hex) {
    fetch('{{ route('admin.themecolorssettings.generate-palette') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ color: hex })
    })
    .then(r => r.json())
    .then(data => {
        const list = document.getElementById('editPaletteList');
        list.innerHTML = '';
        const dark = ['600','700','800','900','950'];
        Object.entries(data.palette).forEach(([shade, color]) => {
            const row = document.createElement('div');
            row.style.cssText = `padding:10px 14px; background:${color}; font-size:13px;
                font-weight:500; color:${dark.includes(shade) ? '#fff' : '#374151'};`;
            row.textContent = `${shade}: ${color}`;
            list.appendChild(row);
        });
        document.getElementById('editSecondaryHex').value          = data.secondary;
        document.getElementById('editSecondaryPicker').value       = data.secondary;
        document.getElementById('previewSecondary').style.background = data.secondary;
    });
}
</script>
@endpush
@endsection
