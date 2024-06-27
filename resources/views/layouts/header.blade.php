<div class="header">
    <nav class="navbar">
        <div class="header-logo">
            <a class="brand-logo" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('admin/images/logo.svg') }}" class="mr-2" alt="logo" />
            </a>
            <a class="brand-logo-mini" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('admin/images/logo-icon.svg') }}" alt="logo" />
            </a>
        </div>
        <div class="navbar-menu-wrapper">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link nav-toggler" data-toggle="minimize">
                        <i class="las la-border-all"></i>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="profile" data-bs-toggle="dropdown" aria-expanded="false">
                        @if(!empty(Auth::user()->profile_image))
                        <img src="{{ asset(Auth::user()->profile_image) }}" alt="user" class="profile-pic" height="38" width="38" />
                        @else
                        <img src="{{ asset('admin/images/profile.png') }}" alt="user" class="profile-pic" />
                        @endif
                        {{Auth::user()->name}}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="profile">
                        <a class="dropdown-item" href="{{ route('admin.profile') }}">My Profile</a>
                        <a class="dropdown-item" href="{{ route('admin.change.password') }}">Change Password</a>
                        <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
                    </div>
                </li>
            </ul>
        </div>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="icon-menu"></span>
        </button>
    </nav>
</div>
