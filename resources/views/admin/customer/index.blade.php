{{-- resources/views/admin/customer/index.blade.php --}}
@extends('admin.master')
@section('content')

<style>
  .page-card { background:#fff; border-radius:12px; padding:28px 32px; box-shadow:0 1px 8px rgba(0,0,0,.06); }
  .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
  .page-header h4 { font-size:1.25rem; font-weight:700; color:#1a1a1a; margin:0; }
  .btn-add { background:linear-gradient(135deg,#e8194b,#b8002e); color:#fff; border:none;
             padding:9px 20px; border-radius:8px; font-size:.85rem; font-weight:600;
             display:inline-flex; align-items:center; gap:6px; text-decoration:none;
             box-shadow:0 4px 14px rgba(232,25,75,.3); transition:transform .15s; }
  .btn-add:hover { transform:translateY(-1px); color:#fff; }

  .cust-table { width:100%; border-collapse:collapse; }
  .cust-table thead tr { border-bottom:2px solid #f0f0f0; }
  .cust-table th { font-size:.78rem; font-weight:600; color:#888; padding:10px 14px; text-align:left; }
  .cust-table tbody tr { border-bottom:1px solid #f5f5f5; transition:background .15s; }
  .cust-table tbody tr:hover { background:#fafafa; }
  .cust-table td { padding:12px 14px; font-size:.85rem; color:#333; vertical-align:middle; }

  .profile-img { width:38px; height:38px; border-radius:50%; object-fit:cover; border:2px solid #eee; }
  .profile-placeholder { width:38px; height:38px; border-radius:50%;
                          background:#f0f0f0; display:flex; align-items:center;
                          justify-content:center; color:#bbb; font-size:1.1rem; }

  .dash-text { color:#bbb; font-size:.85rem; }

  .action-btn { background:none; border:none; cursor:pointer; padding:4px 6px; transition:opacity .15s; }
  .action-btn:hover { opacity:.75; }
  .btn-edit   { color:#e8194b; font-size:1rem; }
  .btn-delete { color:#e8194b; font-size:1rem; }
  .btn-key    { color:#5bc0de; font-size:1rem; }

  /* Alert */
  .alert-success { background:#e8f5e9; color:#2e7d32; border-radius:8px;
                   padding:10px 16px; margin-bottom:16px; font-size:.85rem; }

  /* Modal */
  .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45);
                   z-index:9999; align-items:center; justify-content:center; }
  .modal-overlay.active { display:flex; }
  .modal-box { background:#fff; border-radius:14px; padding:32px 36px;
               width:100%; max-width:480px; box-shadow:0 12px 40px rgba(0,0,0,.15); }
  .modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:22px; }
  .modal-header h5 { font-size:1rem; font-weight:700; color:#1a1a1a; margin:0; }
  .modal-close { background:none; border:none; font-size:1.3rem; cursor:pointer; color:#888; }
  .modal-close:hover { color:#e8194b; }
  .modal-label { font-size:.8rem; font-weight:500; color:#444; margin-bottom:6px; display:block; }
  .modal-input { width:100%; padding:11px 14px; border:1.5px solid #dde; border-radius:9px;
                 font-size:.85rem; font-family:inherit; background:#fafafa;
                 transition:border-color .2s; box-sizing:border-box; }
  .modal-input:focus { border-color:#e8194b; outline:none; background:#fff; }
  .input-eye-wrap { position:relative; }
  .input-eye-wrap .eye-btn { position:absolute; right:12px; top:50%;
                              transform:translateY(-50%); background:none;
                              border:none; cursor:pointer; color:#aaa; font-size:.9rem; }
  .modal-footer { display:flex; justify-content:flex-end; gap:10px; margin-top:24px; }
  .btn-close-modal { padding:9px 22px; border-radius:8px; border:1.5px solid #dde;
                     background:#fff; font-size:.85rem; cursor:pointer; font-weight:500; }
  .btn-save { padding:9px 22px; border-radius:8px; border:none;
              background:linear-gradient(135deg,#e8194b,#b8002e);
              color:#fff; font-size:.85rem; font-weight:600; cursor:pointer;
              box-shadow:0 4px 12px rgba(232,25,75,.3); }
</style>

<div class="page-card">

  {{-- Success message --}}
  @if(session('success'))
    <div class="alert-success">✓ {{ session('success') }}</div>
  @endif

  <div class="page-header">
    <h4>All Customers</h4>
    <a href="{{ route('admin.customers.create') }}" class="btn-add">
      <i class="bi bi-plus-circle"></i> Add Customer
    </a>
  </div>

  <table class="cust-table">
    <thead>
      <tr>
        <th>SL.</th>
        <th>Profile</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Gender</th>
        <th>Date of Birth</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @forelse($customers as $i => $customer)
      <tr>
        <td>{{ $customers->firstItem() + $i }}</td>

        {{-- Profile image --}}
        <td>
        @if($customer->profile_image)
            <img src="{{ asset($customer->profile_image) }}"
                class="profile-img" alt="profile"/>
        @else
            <div class="profile-placeholder">
                <i class="bi bi-person-fill"></i>
            </div>
        @endif
        </td>

        <td>{{ $customer->first_name }} {{ $customer->last_name }}</td>
        <td>{{ $customer->user->phone ?? '--' }}</td>
        <td>{{ $customer->user->email ?? '--' }}</td>
        <td>{{ $customer->gender ? ucfirst($customer->gender) : '--' }}</td>
        <td>{{ $customer->date_of_birth
                  ? \Carbon\Carbon::parse($customer->date_of_birth)->format('d M Y')
                  : '--' }}</td>

        <td>
          {{-- Edit --}}
          <a href="{{ route('admin.customers.edit', $customer->id) }}"
             class="action-btn btn-edit" title="Edit">
            <i class="bi bi-pencil-square"></i>
          </a>

          {{-- Delete --}}
          <form action="{{ route('admin.customers.destroy', $customer->id) }}"
                method="POST" style="display:inline;"
                onsubmit="return confirm('Delete this customer?')">
            @csrf @method('DELETE')
            <button type="submit" class="action-btn btn-delete" title="Delete">
              <i class="bi bi-trash3-fill"></i>
            </button>
          </form>

          {{-- Reset Password --}}
          <button class="action-btn btn-key" title="Reset Password"
                  onclick="openResetModal({{ $customer->id }}, '{{ $customer->first_name }} {{ $customer->last_name }}')">
            <i class="bi bi-key-fill"></i>
          </button>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="8" style="text-align:center; padding:30px; color:#aaa;">
          No customers found.
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Pagination --}}
  <div style="margin-top:20px;">
    {{ $customers->links() }}
  </div>

</div>

{{-- ══════════════════════════════════════
     Reset Password Modal
══════════════════════════════════════ --}}
<div class="modal-overlay" id="resetModal">
  <div class="modal-box">
    <div class="modal-header">
      <h5 id="modalTitle">Reset Password</h5>
      <button class="modal-close" onclick="closeResetModal()">×</button>
    </div>

    <form method="POST" id="resetForm">
      @csrf

      <div style="margin-bottom:16px;">
        <label class="modal-label">Password</label>
        <div class="input-eye-wrap">
          <input type="password" name="password" id="mp1"
                 class="modal-input" placeholder="Enter Password"/>
          <button type="button" class="eye-btn" onclick="toggleEye('mp1','eye1')">
            <i class="bi bi-eye-slash" id="eye1"></i>
          </button>
        </div>
      </div>

      <div style="margin-bottom:8px;">
        <label class="modal-label">Confirm Password</label>
        <div class="input-eye-wrap">
          <input type="password" name="password_confirmation" id="mp2"
                 class="modal-input" placeholder="Enter Password again"/>
          <button type="button" class="eye-btn" onclick="toggleEye('mp2','eye2')">
            <i class="bi bi-eye-slash" id="eye2"></i>
          </button>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-close-modal" onclick="closeResetModal()">Close</button>
        <button type="submit" class="btn-save">Save changes</button>
      </div>
    </form>
  </div>
</div>

<script>
  const baseUrl = '{{ url("admin/customers") }}';

  function openResetModal(id, name) {
    document.getElementById('modalTitle').textContent = 'Reset Password (' + name + ')';
    document.getElementById('resetForm').action = baseUrl + '/' + id + '/reset-password';
    document.getElementById('resetModal').classList.add('active');
  }

  function closeResetModal() {
    document.getElementById('resetModal').classList.remove('active');
  }

  // Close modal if clicking outside the box
  document.getElementById('resetModal').addEventListener('click', function(e) {
    if (e.target === this) closeResetModal();
  });

  function toggleEye(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
      input.type = 'text';
      icon.className = 'bi bi-eye';
    } else {
      input.type = 'password';
      icon.className = 'bi bi-eye-slash';
    }
  }
</script>

@endsection
