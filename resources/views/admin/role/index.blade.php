{{-- resources/views/admin/role/index.blade.php --}}
@extends('admin.master')

@section('content')
<style>
  .rp-wrapper { display:flex; gap:0; min-height:80vh; }

  /* ── LEFT PANEL ── */
  .roles-panel {
    width:400px; flex-shrink:0; background:#fff;
    border-radius:12px 0 0 12px; padding:28px 24px;
    border-right:1.5px solid #f0f0f0;
  }
  .roles-panel h5 { font-size:1.1rem; font-weight:700; color:#1a1a1a; margin-bottom:18px; }
  .search-row { display:flex; gap:10px; margin-bottom:20px; }
  .search-wrap { flex:1; position:relative; }
  .search-wrap input {
    width:100%; padding:9px 14px 9px 36px;
    border:1.5px solid #e0e0e0; border-radius:8px;
    font-size:.83rem; font-family:inherit; background:#fafafa; box-sizing:border-box;
  }
  .search-wrap input:focus { border-color:#e8194b; outline:none; }
  .search-wrap .si { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:#bbb; }
  .btn-add-role {
    background:linear-gradient(135deg,#e8194b,#b8002e); color:#fff;
    border:none; border-radius:8px; padding:9px 16px; font-size:.83rem;
    font-weight:600; cursor:pointer; white-space:nowrap;
    display:inline-flex; align-items:center; gap:5px;
    box-shadow:0 3px 10px rgba(232,25,75,.3);
  }
  .role-list { display:flex; flex-direction:column; }
  .role-item {
    display:flex; justify-content:space-between; align-items:center;
    padding:13px 14px; border-bottom:1px solid #f5f5f5;
    cursor:pointer; transition:background .15s; border-radius:8px;
  }
  .role-item:hover { background:#fdf0f3; }
  .role-item.active { background:#fde8ec; border-left:3px solid #e8194b; }
  .role-name { font-size:.88rem; font-weight:500; color:#222; display:flex; align-items:center; gap:8px; }
  .role-actions { display:flex; align-items:center; gap:6px; }
  .no-action-badge { background:#333; color:#fff; font-size:.7rem; padding:3px 10px; border-radius:20px; font-weight:600; }
  .shop-badge { background:#fde8ec; border:1px solid #f5b8c4; border-radius:6px; padding:2px 7px; font-size:.7rem; color:#e8194b; font-weight:600; }
  .btn-icon { background:none; border:none; cursor:pointer; padding:3px 5px; font-size:.95rem; }
  .btn-icon.edit { color:#5bc0de; } .btn-icon.edit:hover { color:#31a0c0; }
  .btn-icon.del  { color:#e8194b; } .btn-icon.del:hover  { color:#b8002e; }

  /* ── RIGHT PANEL ── */
  .perms-panel {
    flex:1; background:#fff; border-radius:0 12px 12px 0;
    padding:28px 28px 32px; overflow-y:auto; max-height:88vh;
  }
  .perms-panel h5 { font-size:1.1rem; font-weight:700; color:#1a1a1a; margin-bottom:16px; }
  .perms-top {
    display:flex; justify-content:space-between; align-items:center;
    margin-bottom:18px; padding-bottom:14px; border-bottom:1.5px solid #f0f0f0;
  }
  .selected-count { display:flex; align-items:center; gap:8px; font-size:.85rem; color:#555; }
  .selected-count input[type=checkbox] { width:15px; height:15px; accent-color:#e8194b; }
  .btn-clear { background:none; border:none; color:#e8194b; font-size:.83rem; font-weight:600; cursor:pointer; }
  .section-heading { font-size:1rem; font-weight:700; color:#1a1a1a; background:#f4f4f4; padding:10px 16px; border-radius:8px; margin:0 0 16px; }
  .perm-group { margin-bottom:20px; padding-bottom:16px; border-bottom:1px solid #f0f0f0; }
  .perm-group-title { font-size:.85rem; font-weight:700; color:#1a1a1a; margin-bottom:10px; }
  .perm-checks { display:flex; flex-wrap:wrap; gap:10px 24px; }
  .perm-check { display:flex; align-items:center; gap:6px; font-size:.83rem; color:#444; cursor:pointer; }
  .perm-check input[type=checkbox] { width:14px; height:14px; accent-color:#e8194b; cursor:pointer; }
  .btn-update {
    background:linear-gradient(135deg,#e8194b,#b8002e); color:#fff;
    border:none; border-radius:8px; padding:10px 26px; font-size:.88rem;
    font-weight:600; cursor:pointer; margin-top:20px;
    box-shadow:0 4px 14px rgba(232,25,75,.3);
    display:none; align-items:center; gap:6px;
  }
  .no-role-msg { color:#bbb; font-size:.85rem; text-align:center; padding:40px 0; }

  /* Toast */
  .toast-msg {
    position:fixed; bottom:28px; right:28px; z-index:9999;
    background:#2e7d32; color:#fff; padding:12px 22px;
    border-radius:10px; font-size:.85rem; font-weight:500;
    box-shadow:0 6px 20px rgba(0,0,0,.15);
    display:none; align-items:center; gap:8px;
  }
  .toast-msg.show { display:flex; }
  .toast-msg.error { background:#c62828; }

  /* Alert */
  .alert-success { background:#e8f5e9; color:#2e7d32; border-radius:8px; padding:10px 16px; margin-bottom:16px; font-size:.85rem; }

  /* Modal */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9998; align-items:center; justify-content:center; }
  .modal-overlay.active { display:flex; }
  .modal-box { background:#fff; border-radius:14px; padding:32px 36px; width:100%; max-width:460px; box-shadow:0 12px 40px rgba(0,0,0,.15); }
  .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:22px; }
  .modal-header h5 { font-size:1rem; font-weight:700; color:#1a1a1a; margin:0; }
  .modal-close { background:none; border:none; font-size:1.4rem; cursor:pointer; color:#888; line-height:1; }
  .modal-label { font-size:.8rem; font-weight:500; color:#444; margin-bottom:6px; display:block; }
  .modal-input { width:100%; padding:11px 14px; border:1.5px solid #dde; border-radius:9px; font-size:.85rem; font-family:inherit; background:#fafafa; transition:border-color .2s; box-sizing:border-box; }
  .modal-input:focus { border-color:#e8194b; outline:none; background:#fff; }
  .modal-check-row { display:flex; align-items:center; gap:8px; margin-top:14px; font-size:.85rem; color:#444; }
  .modal-check-row input { width:16px; height:16px; accent-color:#e8194b; }
  .modal-footer { display:flex; justify-content:flex-end; gap:10px; margin-top:24px; }
  .btn-close-modal { padding:9px 22px; border-radius:8px; border:1.5px solid #dde; background:#fff; font-size:.85rem; cursor:pointer; font-weight:500; color:#555; }
  .btn-modal-submit { padding:9px 22px; border-radius:8px; border:none; background:linear-gradient(135deg,#e8194b,#b8002e); color:#fff; font-size:.85rem; font-weight:600; cursor:pointer; box-shadow:0 4px 12px rgba(232,25,75,.3); }
</style>

{{-- Laravel session success --}}
@if(session('success'))
  <div class="alert-success">✓ {{ session('success') }}</div>
@endif

<h4 style="font-size:1.2rem;font-weight:700;color:#1a1a1a;margin-bottom:20px;">
  Roles &amp; Permissions
</h4>

<div class="rp-wrapper">

  {{-- ══════════ LEFT: Roles ══════════ --}}
  <div class="roles-panel">
    <h5>Roles</h5>

    <div class="search-row">
      <div class="search-wrap">
        <i class="bi bi-search si"></i>
        <input type="text" placeholder="Search by role name" oninput="filterRoles(this.value)"/>
      </div>
      <button class="btn-add-role" onclick="openCreateModal()">
        <i class="bi bi-plus"></i> Add Role
      </button>
    </div>

    <div class="role-list" id="roleList">

      {{-- Root fixed --}}
      <div class="role-item" data-name="root (all)">
        <span class="role-name">Root (All)</span>
        <span class="no-action-badge">No Action</span>
      </div>

      {{-- Dynamic --}}
      @foreach($roles as $role)
      <div class="role-item"
           data-name="{{ strtolower($role->name) }}"
           data-id="{{ $role->id }}"
           onclick="selectRole({{ $role->id }}, this)">
        <span class="role-name">
          {{ $role->name }}
          <span class="perm-count-badge" id="count-{{ $role->id }}">({{ $role->permissions_count }})</span>
          @if($role->applicable_for_shop)
            <span class="shop-badge"><i class="bi bi-shop"></i></span>
          @endif
        </span>
        <div class="role-actions" onclick="event.stopPropagation()">
          <button class="btn-icon edit" title="Edit"
                  onclick="openEditModal({{ $role->id }}, '{{ addslashes($role->name) }}', {{ $role->applicable_for_shop ? 'true' : 'false' }})">
            <i class="bi bi-pencil-square"></i>
          </button>
          <form action="{{ route('admin.role.destroy', $role->id) }}" method="POST"
                style="display:inline;" onsubmit="return confirm('Delete this role?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn-icon del" title="Delete">
              <i class="bi bi-trash3-fill"></i>
            </button>
          </form>
        </div>
      </div>
      @endforeach

    </div>
  </div>

  {{-- ══════════ RIGHT: Permissions ══════════ --}}
  <div class="perms-panel" id="permsPanel">
    <h5>Permissions</h5>

    <div class="perms-top">
      <label class="selected-count">
        <input type="checkbox" id="selectAllChk" onchange="toggleAll(this)"/>
        <span id="selectedCount">0</span> Permissions Selected
      </label>
      <button type="button" class="btn-clear" onclick="clearAll()">Clear</button>
    </div>

    {{-- No role selected message --}}
    <div id="noRoleMsg" class="no-role-msg">
      <i class="bi bi-arrow-left-circle" style="font-size:1.5rem;display:block;margin-bottom:8px;"></i>
      Select a role from the left to manage permissions
    </div>

    {{-- Permissions (hidden until role selected) --}}
    <div id="permsContent" style="display:none;">

      @php
        $shopGroups  = ['Order','Product','Flash Sale','Promo Code','Bulk Product Import','Bulk Product Export','Gallery Import','Pos'];
        $otherGroups = ['Employee','Profile','ReturnOrder','Supplier','Purchase','PurchaseReturn'];
      @endphp

      <div class="section-heading">Shop</div>

      @foreach($shopGroups as $groupName)
        @if(isset($permissions[$groupName]))
        <div class="perm-group">
          <div class="perm-group-title">{{ $groupName }}</div>
          <div class="perm-checks">
            @foreach($permissions[$groupName] as $perm)
            <label class="perm-check">
              <input type="checkbox" value="{{ $perm->id }}" class="perm-chk" onchange="updateCount()"/>
              {{ $perm->name }}
            </label>
            @endforeach
          </div>
        </div>
        @endif
      @endforeach

      @foreach($otherGroups as $groupName)
        @if(isset($permissions[$groupName]))
        <div class="perm-group">
          <div class="perm-group-title">{{ $groupName }}</div>
          <div class="perm-checks">
            @foreach($permissions[$groupName] as $perm)
            <label class="perm-check">
              <input type="checkbox" value="{{ $perm->id }}" class="perm-chk" onchange="updateCount()"/>
              {{ $perm->name }}
            </label>
            @endforeach
          </div>
        </div>
        @endif
      @endforeach

      <button type="button" class="btn-update" id="updateBtn" onclick="savePermissions()">
        <i class="bi bi-arrow-clockwise"></i> Update
      </button>

    </div>
  </div>

</div>

{{-- Toast --}}
<div class="toast-msg" id="toastMsg">
  <i class="bi bi-check-circle-fill"></i>
  <span id="toastText"></span>
</div>

{{-- ══════════ Create Modal ══════════ --}}
<div class="modal-overlay" id="createModal">
  <div class="modal-box">
    <div class="modal-header">
      <h5>Create Role</h5>
      <button class="modal-close" onclick="closeModal('createModal')">×</button>
    </div>
    <form method="POST" action="{{ route('admin.role.store') }}">
      @csrf
      <label class="modal-label">Role Name</label>
      <input type="text" name="name" class="modal-input" placeholder="Role Name" required/>
      <div class="modal-check-row">
        <input type="checkbox" name="applicable_for_shop" value="1" id="createShopChk" checked/>
        <label for="createShopChk">Applicable For Shop</label>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-close-modal" onclick="closeModal('createModal')">Close</button>
        <button type="submit" class="btn-modal-submit">Submit</button>
      </div>
    </form>
  </div>
</div>

{{-- ══════════ Edit Modal ══════════ --}}
<div class="modal-overlay" id="editModal">
  <div class="modal-box">
    <div class="modal-header">
      <h5>Edit Role</h5>
      <button class="modal-close" onclick="closeModal('editModal')">×</button>
    </div>
    <form method="POST" id="editForm" action="">
      @csrf
      <label class="modal-label">Role Name</label>
      <input type="text" name="name" id="editRoleName" class="modal-input" placeholder="Role Name" required/>
      <div class="modal-check-row">
        <input type="checkbox" name="applicable_for_shop" value="1" id="editShopChk"/>
        <label for="editShopChk">Applicable For Shop</label>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-close-modal" onclick="closeModal('editModal')">Close</button>
        <button type="submit" class="btn-modal-submit">Update</button>
      </div>
    </form>
  </div>
</div>

<script>
  const BASE     = '{{ url("admin/roles") }}';
  const CSRF     = '{{ csrf_token() }}';
  let activeId   = null;

  // ══════════════════════════════════════
  // Select role → load permissions via AJAX
  // ══════════════════════════════════════
  function selectRole(id, el) {
    document.querySelectorAll('.role-item').forEach(r => r.classList.remove('active'));
    el.classList.add('active');
    activeId = id;

    // Show permissions panel
    document.getElementById('noRoleMsg').style.display    = 'none';
    document.getElementById('permsContent').style.display = 'block';
    document.getElementById('updateBtn').style.display    = 'inline-flex';

    // Uncheck all first
    document.querySelectorAll('.perm-chk').forEach(c => c.checked = false);

    // Load role's permissions
    fetch(BASE + '/' + id + '/permissions')
      .then(r => r.json())
      .then(data => {
        data.permission_ids.forEach(pid => {
          const chk = document.querySelector('.perm-chk[value="' + pid + '"]');
          if (chk) chk.checked = true;
        });
        updateCount();
      })
      .catch(() => showToast('Failed to load permissions', true));
  }

  // ══════════════════════════════════════
  // Save permissions via AJAX POST
  // ══════════════════════════════════════
  function savePermissions() {
    if (!activeId) return;

    const checkedIds = [...document.querySelectorAll('.perm-chk:checked')]
                        .map(c => c.value);

    fetch(BASE + '/' + activeId + '/sync-permissions', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF,
        'Accept':       'application/json',
      },
      body: JSON.stringify({ permissions: checkedIds }),
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showToast(data.message);
        // Update count badge in role list
        const badge = document.getElementById('count-' + activeId);
        if (badge) badge.textContent = '(' + data.count + ')';
      } else {
        showToast('Something went wrong', true);
      }
    })
    .catch(() => showToast('Network error', true));
  }

  // ══════════════════════════════════════
  // Helpers
  // ══════════════════════════════════════
  function updateCount() {
    const checked = document.querySelectorAll('.perm-chk:checked').length;
    const total   = document.querySelectorAll('.perm-chk').length;
    document.getElementById('selectedCount').textContent = checked;
    document.getElementById('selectAllChk').checked = (checked === total && total > 0);
  }

  function toggleAll(chk) {
    document.querySelectorAll('.perm-chk').forEach(c => c.checked = chk.checked);
    updateCount();
  }

  function clearAll() {
    document.querySelectorAll('.perm-chk').forEach(c => c.checked = false);
    document.getElementById('selectAllChk').checked = false;
    updateCount();
  }

  function filterRoles(val) {
    document.querySelectorAll('.role-item').forEach(item => {
      const name = (item.dataset.name || '').toLowerCase();
      item.style.display = name.includes(val.toLowerCase()) ? '' : 'none';
    });
  }

  function showToast(msg, isError = false) {
    const toast = document.getElementById('toastMsg');
    document.getElementById('toastText').textContent = msg;
    toast.className = 'toast-msg show' + (isError ? ' error' : '');
    setTimeout(() => { toast.className = 'toast-msg'; }, 3000);
  }

  // ══════════════════════════════════════
  // Modals
  // ══════════════════════════════════════
  function openCreateModal() {
    document.getElementById('createModal').classList.add('active');
  }

  function openEditModal(id, name, shopApplicable) {
    document.getElementById('editRoleName').value   = name;
    document.getElementById('editShopChk').checked  = shopApplicable;
    document.getElementById('editForm').action      = BASE + '/' + id + '/update-name';
    document.getElementById('editModal').classList.add('active');
  }

  function closeModal(id) {
    document.getElementById(id).classList.remove('active');
  }

  document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
  });
</script>

@endsection
