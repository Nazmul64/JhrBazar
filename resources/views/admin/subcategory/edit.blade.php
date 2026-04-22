@extends('admin.master')

@section('content')

<div class="mb-4">
    <h4 class="fw-bold mb-0">
        <i class="bi bi-pencil-square me-2"></i> Edit Sub Category
    </h4>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">

        <div class="d-flex align-items-center mb-4">
            <i class="bi bi-grid-3x3-gap me-2 text-muted"></i>
            <span class="fw-semibold">Sub Category Information</span>
        </div>

        <form action="{{ route('admin.subcategory.update', $subcategory->id) }}"
              method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- ── Select Category ── --}}
            <div class="mb-4" style="position: relative;">
                <label class="form-label fw-semibold">
                    Select Category <span class="text-danger">*</span>
                </label>

                <div id="tag-wrapper"
                     class="form-control d-flex flex-wrap align-items-center gap-1 @error('category_ids') border-danger @enderror"
                     style="min-height:44px; cursor:text; padding:6px 10px;">
                    <div id="tags-container" class="d-flex flex-wrap gap-1"></div>
                    <input type="text" id="cat-search"
                           placeholder="Search & select category..."
                           class="border-0 flex-grow-1"
                           style="min-width:140px; outline:none; background:transparent;"
                           autocomplete="off">
                </div>

                <div id="cat-dropdown" class="border rounded shadow bg-white"
                     style="display:none; position:absolute; top:100%; left:0; right:0;
                            z-index:9999; max-height:220px; overflow-y:auto; margin-top:2px;">
                    @forelse($categories as $cat)
                        <div class="cat-option px-3 py-2"
                             style="cursor:pointer; font-size:14px;"
                             data-id="{{ $cat->id }}"
                             data-name="{{ $cat->name }}">
                            {{ $cat->name }}
                        </div>
                    @empty
                        <div class="px-3 py-2 text-muted" style="font-size:13px;">
                            No active categories found.
                        </div>
                    @endforelse
                </div>

                <div id="hidden-inputs"></div>

                @error('category_ids')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- ── Name ── --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Name <span class="text-danger">*</span>
                </label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       placeholder="Sub category name"
                       value="{{ old('name', $subcategory->name) }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- ── Current Thumbnail Preview ── --}}
            <div class="mb-3 text-center">
                <div class="mx-auto rounded border bg-light d-flex align-items-center justify-content-center overflow-hidden"
                     style="width:160px; height:160px;">
                    @if($subcategory->thumbnail)
                        <img id="preview-img"
                             src="{{ asset($subcategory->thumbnail) }}"
                             alt="Current Thumbnail"
                             class="w-100 h-100" style="object-fit:cover;">
                        <span id="preview-placeholder" class="text-muted small d-none">500 × 500</span>
                    @else
                        <span id="preview-placeholder" class="text-muted small">500 × 500</span>
                        <img id="preview-img" src="" alt="Preview"
                             class="d-none w-100 h-100" style="object-fit:cover;">
                    @endif
                </div>
            </div>

            {{-- ── Thumbnail Upload ── --}}
            <div class="mb-5">
                <label class="form-label fw-semibold">
                    Thumbnail (Ratio 1:1)
                    <span class="fw-normal text-muted" style="font-size:12px;">— leave empty to keep current</span>
                </label>
                <input type="file" name="thumbnail" id="thumbnail"
                       class="form-control @error('thumbnail') is-invalid @enderror"
                       accept=".jpg,.jpeg,.png,.webp"
                       onchange="previewImage(this)">
                @error('thumbnail')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.subcategory.index') }}" class="btn btn-secondary px-4">Back</a>
                <button type="submit" class="btn btn-danger px-5">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ Pass pre-selected data from PHP to JS ══ --}}
<script>
var PRESELECTED = {
    @foreach($subcategory->categories as $cat)
        "{{ $cat->id }}": "{{ addslashes($cat->name) }}",
    @endforeach
};
</script>

{{-- ══ INLINE SCRIPT ══ --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    var selected   = {};
    var searchEl   = document.getElementById('cat-search');
    var dropdown   = document.getElementById('cat-dropdown');
    var tagsEl     = document.getElementById('tags-container');
    var hiddenEl   = document.getElementById('hidden-inputs');
    var wrapperEl  = document.getElementById('tag-wrapper');
    var allOptions = Array.from(document.querySelectorAll('.cat-option'));

    /* ── Pre-load existing categories ── */
    Object.keys(PRESELECTED).forEach(function (id) {
        selected[id] = PRESELECTED[id];
    });
    renderAll(); // show tags immediately on page load

    function openDropdown() {
        filterOptions(searchEl.value.toLowerCase());
        dropdown.style.display = 'block';
    }
    function closeDropdown() { dropdown.style.display = 'none'; }

    function filterOptions(query) {
        var visible = 0;
        allOptions.forEach(function (opt) {
            var show = opt.dataset.name.toLowerCase().indexOf(query) !== -1 && !selected[opt.dataset.id];
            opt.style.display = show ? '' : 'none';
            if (show) visible++;
        });
        var nm = document.getElementById('cat-no-result');
        if (visible === 0) {
            if (!nm) {
                nm = document.createElement('div');
                nm.id = 'cat-no-result';
                nm.style.cssText = 'padding:8px 12px;font-size:13px;color:#9ca3af;';
                nm.textContent   = 'No results found.';
                dropdown.appendChild(nm);
            }
            nm.style.display = '';
        } else { if (nm) nm.style.display = 'none'; }
    }

    function selectCat(id, name) {
        if (selected[id]) return;
        selected[id] = name;
        renderAll();
        searchEl.value = '';
        closeDropdown();
        searchEl.focus();
    }

    window.removeSubTag = function (id) {
        delete selected[id];
        renderAll();
        allOptions.forEach(function (o) { if (o.dataset.id == id) o.style.display = ''; });
    };

    function renderAll() {
        tagsEl.innerHTML = hiddenEl.innerHTML = '';
        Object.keys(selected).forEach(function (id) {
            var name = selected[id];
            var tag  = document.createElement('span');
            tag.className = 'badge d-inline-flex align-items-center gap-1 px-2 py-1';
            tag.style.cssText = 'background:#dbeafe;color:#1d4ed8;font-size:12px;font-weight:500;border-radius:5px;white-space:nowrap;';
            tag.innerHTML = '<span onclick="removeSubTag(\'' + id + '\')" style="cursor:pointer;font-size:15px;line-height:1;color:#6b7280;margin-right:2px;">&times;</span>' + esc(name);
            tagsEl.appendChild(tag);
            var inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = 'category_ids[]'; inp.value = id;
            hiddenEl.appendChild(inp);
        });
        /* hide already-selected from dropdown */
        allOptions.forEach(function (o) { if (selected[o.dataset.id]) o.style.display = 'none'; });
    }

    function esc(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    wrapperEl.addEventListener('click',   function () { searchEl.focus(); });
    searchEl.addEventListener('focus',    function () { openDropdown(); });
    searchEl.addEventListener('input',    function () { openDropdown(); });
    searchEl.addEventListener('keydown',  function (e) {
        if (e.key === 'Escape') closeDropdown();
        if (e.key === 'Backspace' && searchEl.value === '') {
            var ids = Object.keys(selected); if (ids.length) removeSubTag(ids[ids.length-1]);
        }
    });
    allOptions.forEach(function (opt) {
        opt.addEventListener('mousedown', function (e) { e.preventDefault(); selectCat(this.dataset.id, this.dataset.name); });
        opt.addEventListener('mouseenter', function () { this.style.background = '#f1f5f9'; });
        opt.addEventListener('mouseleave', function () { this.style.background = ''; });
    });
    document.addEventListener('mousedown', function (e) {
        if (!wrapperEl.contains(e.target) && !dropdown.contains(e.target)) closeDropdown();
    });
});

function previewImage(input) {
    var img = document.getElementById('preview-img');
    var ph  = document.getElementById('preview-placeholder');
    if (input.files && input.files[0]) {
        var r = new FileReader();
        r.onload = function (e) {
            img.src = e.target.result;
            img.classList.remove('d-none');
            if (ph) ph.classList.add('d-none');
        };
        r.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection
