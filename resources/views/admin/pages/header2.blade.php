<header id="header">
  <button class="header-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
  <div class="header-title">
    <h6>{{ Auth::user()->name }}</h6>
  </div>

  <button class="header-action">
    <i class="bi bi-search"></i>
  </button>
  <button class="header-action" id="mobileSidebarToggle" title="Menu">
    <i class="bi bi-list"></i>
  </button>
  <button class="header-action" style="position:relative;">
    <i class="bi bi-bell"></i>
    <span class="header-notif-badge">9+</span>
  </button>
  <button class="lang-btn">
    <i class="bi bi-globe2"></i>
    <span>English</span>
    <i class="bi bi-chevron-down" style="font-size:10px;"></i>
  </button>
  <div class="avatar-wrap dropdown">
    <div class="d-flex align-items-center gap-2 cursor-pointer dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
        <div class="avatar">
            @if(Auth::user()->profile_image)
                <img src="{{ asset(Auth::user()->profile_image) }}" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
            @else
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            @endif
        </div>
        <div class="avatar-info d-none d-md-block">
            <span class="name">{{ Auth::user()->name }}</span>
            <span class="role">{{ ucfirst(Auth::user()->role) }}</span>
        </div>
    </div>
    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 py-2" style="min-width: 200px; border-radius: 12px;">
        <li class="px-3 py-2 border-bottom mb-2">
            <h6 class="mb-0 fw-bold">{{ Auth::user()->name }}</h6>
            <small class="text-muted">{{ Auth::user()->email }}</small>
        </li>
        <li>
            <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2" href="{{ route('admin.profile.edit') }}">
                <i class="bi bi-person text-primary"></i> Profile
            </a>
        </li>

        <li>
            <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2" href="{{ route('admin.profile.index') }}#change-password">
                <i class="bi bi-shield-lock text-warning"></i> Password
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <form action="{{
                Auth::user()->role === 'admin' ? route('admin.logout') :
                (Auth::user()->role === 'manager' ? route('manager.logout') :
                (Auth::user()->role === 'seller' ? route('seller.logout') : route('employee.logout')))
            }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item py-2 px-3 d-flex align-items-center gap-2 text-danger">
                    <i class="bi bi-box-arrow-right"></i> Sign Out
                </button>
            </form>
        </li>
    </ul>
  </div>
</header>
