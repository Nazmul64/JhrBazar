@extends('admin.master')

@section('content')
<div class="container-fluid px-4 py-4">

    <h5 class="fw-bold mb-4" style="color:#1a1a2e; font-size:20px;">Edit Employee</h5>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li style="font-size:13px;">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ══════════════════════════════════════
         FORM 1: Employee Info Update
    ══════════════════════════════════════ --}}
    <form action="{{ route('admin.employees.update', $employee->id) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm rounded-3 mb-3">
            <div class="card-body p-4">

                <div class="d-flex align-items-center gap-2 mb-4">
                    <i class="fas fa-user" style="color:#aaa; font-size:15px;"></i>
                    <span style="font-size:14px; font-weight:600; color:#333;">User Information</span>
                </div>

                <div class="row g-0">

                    {{-- Left Fields --}}
                    <div class="col-lg-8 pe-lg-4">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="emp-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name"
                                       class="form-control emp-input @error('first_name') is-invalid @enderror"
                                       value="{{ old('first_name', $employee->first_name) }}">
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="emp-label">Last Name</label>
                                <input type="text" name="last_name"
                                       class="form-control emp-input @error('last_name') is-invalid @enderror"
                                       value="{{ old('last_name', $employee->last_name) }}">
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="emp-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone"
                                       class="form-control emp-input @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $employee->phone) }}">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="emp-label">Gender</label>
                                <select name="gender"
                                        class="form-select emp-input @error('gender') is-invalid @enderror">
                                    @foreach(['Male','Female','Other'] as $g)
                                        <option value="{{ $g }}"
                                            {{ old('gender', $employee->gender) == $g ? 'selected' : '' }}>
                                            {{ $g }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="emp-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email"
                                       class="form-control emp-input @error('email') is-invalid @enderror"
                                       value="{{ old('email', $employee->email) }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="emp-label">
                                    New Password
                                    <span style="font-size:11px; color:#999;">(leave blank to keep)</span>
                                </label>
                                <input type="password" name="password"
                                       class="form-control emp-input @error('password') is-invalid @enderror"
                                       placeholder="Enter new password">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="emp-label">Confirm New Password</label>
                                <input type="password" name="password_confirmation"
                                       class="form-control emp-input"
                                       placeholder="Confirm new password">
                            </div>

                        </div>
                    </div>

                    {{-- Right: Image + Role --}}
                    <div class="col-lg-4 mt-4 mt-lg-0">

                        <div class="mb-3">
                            <div id="imgPreviewBox"
                                 onclick="document.getElementById('profile_image').click()"
                                 style="width:160px; height:160px; background:#e9ecef;
                                        border:1.5px dashed #ced4da; border-radius:10px;
                                        overflow:hidden; cursor:pointer; position:relative;
                                        transition: border-color .2s;">
                                @if($employee->profile_image)
                                    <img id="imgPreview"
                                         src="{{ asset($employee->profile_image) }}"
                                         alt="Profile"
                                         style="width:100%; height:100%; object-fit:cover;">
                                    <span id="imgPlaceholder" style="display:none;"></span>
                                @else
                                    <div style="width:100%; height:100%;
                                                display:flex; align-items:center; justify-content:center;">
                                        <span id="imgPlaceholder" style="font-size:13px; color:#adb5bd;">
                                            500 × 500
                                        </span>
                                    </div>
                                    <img id="imgPreview" src="" alt=""
                                         style="display:none; width:100%; height:100%;
                                                object-fit:cover; position:absolute; top:0; left:0;">
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="emp-label">
                                User profile
                                <span style="font-size:11px; color:#888;">(Ratio 1:1)</span>
                            </label>
                            <input type="file" name="profile_image" id="profile_image"
                                   class="form-control emp-input @error('profile_image') is-invalid @enderror"
                                   accept="image/*" onchange="previewImg(this)">
                            @error('profile_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="emp-label">Role <span class="text-danger">*</span></label>
                            <select name="role_id" id="role_id_select"
                                    class="form-select emp-input @error('role_id') is-invalid @enderror"
                                    onchange="syncRoleName(this)">
                                <option value="">Select Role</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->id }}"
                                            data-name="{{ $r->name }}"
                                            {{ old('role_id', $employee->role_id) == $r->id ? 'selected' : '' }}>
                                        {{ ucfirst($r->name) }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="role" id="role_name_hidden"
                                   value="{{ old('role', $employee->role) }}">
                            @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <button type="submit" class="btn-submit-emp">Update Info</button>
            <a href="{{ route('admin.employees.index') }}" class="btn-cancel-emp ms-2">Cancel</a>
        </div>

    </form>

    {{-- ══════════════════════════════════════
         FORM 2: Permissions Update
         Route: POST (no @method spoofing needed)
    ══════════════════════════════════════ --}}
    <form action="{{ route('admin.employees.updatePermission', $employee->id) }}" method="POST">
        @csrf
        {{-- ✅ @method('PUT') নেই — route POST তাই --}}

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">

                {{-- Header --}}
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-shield-alt" style="color:#e91e63; font-size:15px;"></i>
                        <span style="font-size:14px; font-weight:600; color:#333;">Permissions</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span id="permCount" class="perm-count-badge">0 Selected</span>
                        <button type="button" onclick="clearAll()" class="btn-clear-perm">
                            <i class="fas fa-times me-1"></i>Clear All
                        </button>
                        <button type="button" onclick="selectAll()" class="btn-select-all-perm">
                            <i class="fas fa-check-double me-1"></i>Select All
                        </button>
                    </div>
                </div>

                {{-- Permission Groups --}}
                @php
                    $empPermIds = $employee->permissions->pluck('id')->toArray();
                @endphp

                @foreach($groupedPermissions as $groupName => $subGroups)
                <div class="perm-section mb-4">

                    {{-- Group Header with master checkbox --}}
                    <div class="perm-group-header d-flex align-items-center gap-2 mb-3">
                        <input type="checkbox"
                               class="perm-group-check form-check-input mt-0"
                               id="grp_{{ Str::slug($groupName) }}"
                               onchange="toggleGroup(this, '{{ Str::slug($groupName) }}')">
                        <label for="grp_{{ Str::slug($groupName) }}"
                               style="font-size:13px; font-weight:700; color:#1a1a2e;
                                      cursor:pointer; text-transform:uppercase; letter-spacing:.5px;">
                            {{ $groupName }}
                        </label>
                    </div>

                    {{-- Module rows --}}
                    <div class="row g-2 ps-3">
                        @foreach($subGroups as $moduleName => $perms)
                        <div class="col-12">
                            <div class="perm-module-row d-flex align-items-start gap-3 flex-wrap">

                                <div class="perm-module-name">
                                    <span>{{ $moduleName }}</span>
                                </div>

                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    @foreach($perms as $perm)
                                    <label class="perm-chip {{ in_array($perm->id, $empPermIds) ? 'active' : '' }}"
                                           for="perm_{{ $perm->id }}">
                                        <input type="checkbox"
                                               name="permissions[]"
                                               value="{{ $perm->id }}"
                                               id="perm_{{ $perm->id }}"
                                               class="perm-checkbox grp-{{ Str::slug($groupName) }}"
                                               {{ in_array($perm->id, $empPermIds) ? 'checked' : '' }}
                                               onchange="onPermChange(this)">
                                        {{ $perm->key }}
                                    </label>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>

                @if(!$loop->last)
                    <hr style="border-color:#f0f0f0; margin: 0 0 1.5rem;">
                @endif
                @endforeach

            </div>
        </div>

        <div class="mt-3 mb-5">
            <button type="submit" class="btn-submit-emp">Update Permissions</button>
        </div>

    </form>

</div>

{{-- ════════════ STYLES ════════════ --}}
<style>
.emp-label {
    display: block; font-size: 13px; font-weight: 500;
    color: #333; margin-bottom: 6px;
}
.emp-input {
    font-size: 14px; border: 1px solid #e2e8f0; border-radius: 8px;
    padding: 9px 14px; color: #333; background: #fff;
    transition: border-color .2s, box-shadow .2s;
}
.emp-input:focus {
    border-color: #e91e63;
    box-shadow: 0 0 0 3px rgba(233,30,99,.1);
    outline: none;
}
.btn-submit-emp {
    background: linear-gradient(135deg, #e91e63, #c2185b);
    color: #fff; border: none; border-radius: 8px;
    padding: 10px 28px; font-size: 14px; font-weight: 500;
    cursor: pointer; transition: all .2s;
    box-shadow: 0 3px 10px rgba(233,30,99,.35);
}
.btn-submit-emp:hover {
    background: linear-gradient(135deg, #c2185b, #ad1457);
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(233,30,99,.4);
}
.btn-cancel-emp {
    background: #f5f5f5; color: #555; border: 1px solid #e0e0e0;
    border-radius: 8px; padding: 10px 24px; font-size: 14px;
    font-weight: 500; text-decoration: none; transition: all .2s;
    display: inline-flex; align-items: center;
}
.btn-cancel-emp:hover { background: #ebebeb; color: #333; }
#imgPreviewBox:hover { border-color: #e91e63 !important; }

/* ── Permission UI ── */
.perm-count-badge {
    font-size: 12px; font-weight: 600; color: #e91e63;
    background: rgba(233,30,99,.1); padding: 4px 12px; border-radius: 20px;
}
.btn-clear-perm {
    font-size: 12px; color: #888; background: #f5f5f5;
    border: 1px solid #e0e0e0; border-radius: 6px;
    padding: 5px 12px; cursor: pointer; transition: all .2s;
}
.btn-clear-perm:hover { background: #fee; color: #e91e63; border-color: #f8bbd9; }
.btn-select-all-perm {
    font-size: 12px; color: #fff;
    background: linear-gradient(135deg, #e91e63, #c2185b);
    border: none; border-radius: 6px;
    padding: 5px 12px; cursor: pointer; transition: all .2s;
}
.btn-select-all-perm:hover { opacity: .9; }

.perm-group-header {
    padding-bottom: 8px;
    border-bottom: 2px solid #f0f0f0;
}
.perm-module-row {
    padding: 6px 0;
    border-bottom: 1px dashed #f5f5f5;
}
.perm-module-row:last-child { border-bottom: none; }
.perm-module-name {
    min-width: 160px; max-width: 160px;
    font-size: 13px; font-weight: 500; color: #555;
    padding-top: 4px;
}
.perm-chip {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 500; padding: 4px 10px;
    border-radius: 5px; cursor: pointer;
    border: 1.5px solid #e2e8f0;
    background: #f9f9f9; color: #777;
    transition: all .15s; user-select: none;
}
.perm-chip input[type=checkbox] { display: none; }
.perm-chip:hover { border-color: #e91e63; color: #e91e63; background: #fff0f5; }
.perm-chip.active {
    background: rgba(233,30,99,.12);
    border-color: #e91e63;
    color: #e91e63;
    font-weight: 600;
}
</style>

{{-- ════════════ SCRIPTS ════════════ --}}
<script>
// ── Image preview ──────────────────────────────
function previewImg(input) {
    const placeholder = document.getElementById('imgPlaceholder');
    const preview     = document.getElementById('imgPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src           = e.target.result;
            preview.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// ── Role name sync ─────────────────────────────
function syncRoleName(select) {
    const opt = select.options[select.selectedIndex];
    document.getElementById('role_name_hidden').value = opt.dataset.name || '';
}

// ── On DOM ready ───────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('role_id_select');
    if (sel && sel.value) syncRoleName(sel);
    updateCount();
    updateAllGroupStates();
});

// ── Chip visual toggle ─────────────────────────
function onPermChange(cb) {
    cb.closest('.perm-chip').classList.toggle('active', cb.checked);
    updateCount();
    const slug = cb.className.match(/grp-([\w-]+)/)?.[1];
    updateGroupState(slug);
}

// ── Selected count ─────────────────────────────
function updateCount() {
    const total = document.querySelectorAll('.perm-checkbox:checked').length;
    document.getElementById('permCount').textContent = total + ' Selected';
}

// ── Toggle whole group ─────────────────────────
function toggleGroup(masterCb, groupSlug) {
    document.querySelectorAll('.grp-' + groupSlug).forEach(cb => {
        cb.checked = masterCb.checked;
        cb.closest('.perm-chip').classList.toggle('active', masterCb.checked);
    });
    updateCount();
}

// ── Sync group master checkbox state ──────────
function updateGroupState(groupSlug) {
    if (!groupSlug) return;
    const all     = document.querySelectorAll('.grp-' + groupSlug);
    const checked = document.querySelectorAll('.grp-' + groupSlug + ':checked');
    const grpCb   = document.getElementById('grp_' + groupSlug);
    if (!grpCb) return;
    if (checked.length === 0) {
        grpCb.checked = false; grpCb.indeterminate = false;
    } else if (checked.length === all.length) {
        grpCb.checked = true;  grpCb.indeterminate = false;
    } else {
        grpCb.checked = false; grpCb.indeterminate = true;
    }
}

function updateAllGroupStates() {
    document.querySelectorAll('.perm-group-check').forEach(grpCb => {
        updateGroupState(grpCb.id.replace('grp_', ''));
    });
}

// ── Clear / Select All ─────────────────────────
function clearAll() {
    document.querySelectorAll('.perm-checkbox').forEach(cb => {
        cb.checked = false;
        cb.closest('.perm-chip').classList.remove('active');
    });
    document.querySelectorAll('.perm-group-check').forEach(cb => {
        cb.checked = false; cb.indeterminate = false;
    });
    updateCount();
}

function selectAll() {
    document.querySelectorAll('.perm-checkbox').forEach(cb => {
        cb.checked = true;
        cb.closest('.perm-chip').classList.add('active');
    });
    document.querySelectorAll('.perm-group-check').forEach(cb => {
        cb.checked = true; cb.indeterminate = false;
    });
    updateCount();
}
</script>
@endsection
