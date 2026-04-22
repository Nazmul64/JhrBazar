@extends('admin.master')

@section('content')
{{-- ── Page wrapper ─────────────────────────────────────────── --}}
<div style="background:#f3f4f6; min-height:100vh; padding:24px;">

    {{-- ── Success / Error alerts ──────────────────────────── --}}
    @if(session('success'))
        <div style="background:#d1fae5; color:#065f46; border:1px solid #6ee7b7;
                    padding:12px 18px; border-radius:8px; margin-bottom:16px; font-size:14px;">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- ── Top Card ─────────────────────────────────────────── --}}
    <div style="background:#fff; border-radius:12px; padding:20px 24px;
                display:flex; align-items:center; justify-content:space-between;
                margin-bottom:16px; box-shadow:0 1px 3px rgba(0,0,0,.07);">

        <h1 style="font-size:20px; font-weight:700; color:#111; margin:0;">
            Theme Colors Settings
        </h1>

        <button onclick="openChangeModal()"
                style="background:#eb2e61; color:#fff; border:none; border-radius:8px;
                       padding:10px 18px; font-size:14px; font-weight:600; cursor:pointer;
                       display:flex; align-items:center; gap:8px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/>
            </svg>
            Change Color Palette
        </button>
    </div>

    {{-- ── Current Color Card ───────────────────────────────── --}}
    <div style="background:#fff; border-radius:12px;
                margin-bottom:16px; box-shadow:0 1px 3px rgba(0,0,0,.07); overflow:hidden;">

        <div style="padding:16px 24px; border-bottom:1px solid #f0f0f0;">
            <h2 style="font-size:15px; font-weight:700; color:#111; margin:0;">Current Color</h2>
        </div>

        <div style="padding:24px; display:flex; gap:16px; flex-wrap:wrap;">

            {{-- Primary swatch --}}
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

            {{-- Secondary swatch --}}
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

        {{-- Save & Update button --}}
        <div style="padding:0 24px 24px;">
            <form action="{{ route('admin.themecolorssettings.toggle', $setting->id) }}"
                  method="POST" style="display:inline;">
                @csrf
                <button type="submit"
                        style="background:#eb2e61; color:#fff; border:none; border-radius:8px;
                               padding:10px 22px; font-size:14px; font-weight:600; cursor:pointer;">
                    Save And Update
                </button>
            </form>
        </div>
    </div>

    {{-- ── Available Colors Palette Card ───────────────────── --}}
    <div style="background:#fff; border-radius:12px;
                box-shadow:0 1px 3px rgba(0,0,0,.07); overflow:hidden;">

        <div style="padding:16px 24px; border-bottom:1px solid #f0f0f0;">
            <h2 style="font-size:15px; font-weight:700; color:#111; margin:0;">
                Available Colors palette
            </h2>
        </div>

        <div style="padding:24px;">
            <div style="border:2px solid #eb2e61; border-radius:10px; padding:20px;
                        display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">

                @foreach($palette as $shade => $hex)
                    <div style="text-align:center; min-width:80px; flex:0 0 auto;">
                        {{-- Extra-large for the active (500) swatch --}}
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

</div><!-- /page wrapper -->


{{-- ═══════════════════════════════════════════════════════════
     CHANGE COLOR PALETTE MODAL
═══════════════════════════════════════════════════════════ --}}
<div id="changeModal"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5);
            z-index:9999; align-items:center; justify-content:center;">

    <div style="background:#fff; border-radius:12px; width:500px; max-width:95vw;
                max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.3);">

        {{-- Modal header --}}
        <div style="display:flex; align-items:center; justify-content:space-between;
                    padding:20px 24px; border-bottom:1px solid #f0f0f0;">
            <h3 style="font-size:16px; font-weight:700; color:#111; margin:0;">
                Change Color Palette
            </h3>
            <button onclick="closeChangeModal()"
                    style="background:#fff; border:1px solid #d1d5db; border-radius:6px;
                           width:32px; height:32px; cursor:pointer; font-size:16px;
                           display:flex; align-items:center; justify-content:center;">
                ✕
            </button>
        </div>

        {{-- Modal body --}}
        <div style="padding:24px;">

            <p style="font-size:14px; font-weight:600; color:#111; margin:0 0 12px;">
                Select Your Primary Color
            </p>

            {{-- Color input row --}}
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
                <input type="color" id="colorPicker"
                       value="{{ $setting->primary_color }}"
                       onchange="onColorChange(this.value)"
                       style="width:44px; height:44px; border:2px solid #e5e7eb;
                              border-radius:6px; cursor:pointer; padding:2px;">
                <input type="text" id="hexInput"
                       value="{{ $setting->primary_color }}"
                       oninput="onHexInput(this.value)"
                       placeholder="#eb2e61"
                       style="flex:1; border:1px solid #d1d5db; border-radius:8px;
                              padding:10px 14px; font-size:14px; color:#374151;
                              outline:none;">
            </div>

            {{-- Action buttons --}}
            <div style="display:flex; gap:10px; margin-bottom:20px;">
                <button onclick="closeChangeModal()"
                        style="flex:1; border:1px solid #d1d5db; background:#fff;
                               color:#374151; border-radius:8px; padding:11px;
                               font-size:14px; font-weight:600; cursor:pointer;">
                    Close
                </button>
                <button onclick="saveChanges()"
                        style="flex:1; background:#eb2e61; color:#fff; border:none;
                               border-radius:8px; padding:11px; font-size:14px;
                               font-weight:600; cursor:pointer;">
                    Save changes
                </button>
            </div>

            {{-- Palette preview list --}}
            <div id="modalPaletteList" style="border-radius:8px; overflow:hidden;">
                @foreach($palette as $shade => $hex)
                    <div class="palette-row"
                         data-shade="{{ $shade }}"
                         data-hex="{{ $hex }}"
                         onclick="selectShade('{{ $hex }}')"
                         style="padding:14px 16px; background:{{ $hex }};
                                cursor:pointer; font-size:14px; font-weight:500;
                                color:{{ in_array($shade, ['600','700','800','900','950']) ? '#fff' : '#374151' }};
                                transition:opacity .15s;">
                        {{ $shade }}: {{ $hex }}
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════
     HIDDEN FORM — submits the chosen colors
═══════════════════════════════════════════════════════════ --}}
<form id="saveForm" action="{{ route('admin.themecolorssettings.store') }}"
      method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="primary_color"   id="formPrimary"   value="{{ $setting->primary_color }}">
    <input type="hidden" name="secondary_color" id="formSecondary" value="{{ $setting->secondary_color }}">
    <input type="hidden" name="palette_name"    value="Custom">
</form>
<script>
// ── Modal open / close ────────────────────────────────────────
function openChangeModal() {
    document.getElementById('changeModal').style.display = 'flex';
}
function closeChangeModal() {
    document.getElementById('changeModal').style.display = 'none';
}

// ── Sync color picker ↔ text input ───────────────────────────
function onColorChange(value) {
    document.getElementById('hexInput').value = value;
    refreshModalPalette(value);
}
function onHexInput(value) {
    if (/^#[0-9a-fA-F]{6}$/.test(value)) {
        document.getElementById('colorPicker').value = value;
        refreshModalPalette(value);
    }
}

// ── Click a shade row ─────────────────────────────────────────
function selectShade(hex) {
    document.getElementById('hexInput').value   = hex;
    document.getElementById('colorPicker').value = hex;
    refreshModalPalette(hex);
}

// ── Refresh palette via AJAX ──────────────────────────────────
function refreshModalPalette(hex) {
    fetch('{{ route('admin.themecolorssettings.generate-palette') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ color: hex })
    })
    .then(r => r.json())
    .then(data => {
        const list = document.getElementById('modalPaletteList');
        list.innerHTML = '';
        const darkShades = ['600','700','800','900','950'];
        Object.entries(data.palette).forEach(([shade, color]) => {
            const row = document.createElement('div');
            row.className = 'palette-row';
            row.dataset.shade = shade;
            row.dataset.hex   = color;
            row.onclick       = () => selectShade(color);
            row.style.cssText = `
                padding:14px 16px; background:${color};
                cursor:pointer; font-size:14px; font-weight:500;
                color:${darkShades.includes(shade) ? '#fff' : '#374151'};
                transition:opacity .15s;
            `;
            row.textContent = `${shade}: ${color}`;
            list.appendChild(row);
        });

        // Store derived secondary
        document.getElementById('formSecondary').value = data.secondary;
    })
    .catch(console.error);
}

// ── Save changes (submit hidden form) ─────────────────────────
function saveChanges() {
    const hex = document.getElementById('hexInput').value;
    if (!/^#[0-9a-fA-F]{6}$/.test(hex)) {
        alert('Please enter a valid hex color (e.g. #eb2e61)');
        return;
    }
    document.getElementById('formPrimary').value = hex;
    document.getElementById('saveForm').submit();
}

// Close modal on backdrop click
document.getElementById('changeModal').addEventListener('click', function(e) {
    if (e.target === this) closeChangeModal();
});
</script>


@endsection
