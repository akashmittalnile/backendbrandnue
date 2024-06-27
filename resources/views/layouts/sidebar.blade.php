<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item @if(request()->routeIs('admin.dashboard')) active @endif ">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="menu-icon las la-tachometer-alt"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item @if(request()->routeIs('admin.digital.ibrary') || Request::is('digital-library/instructional-templates') || Request::is('digital-library/instructional-templates/*') || Request::is('digital-library/instructional-guides') || Request::is('digital-library/instructional-guides/*')) active @endif">
            <a class="nav-link" href="{{ route('admin.digital.library') }}">
                <i class="menu-icon las la-book"></i>
                <span class="menu-title">Digital Library</span>
            </a>
        </li>

        <li class="nav-item @if(in_array(request()->route()->getName(),['admin.membership.elite-membership-request-list'])) active @endif">
            <a class="nav-link" href="{{ route('admin.membership.elite-membership-request-list') }}">
                <i class="menu-icon las la-user-astronaut"></i>
                <span class="menu-title">Elite Member Request</span>
                <span class="badge bg-danger pull-right">{{eliteMemberRequestTotal()}}</span>
            </a>
        </li>

        <li class="nav-item @if(in_array(request()->route()->getName(),['admin.admin.list'])) active @endif">
            <a class="nav-link" href="{{ route('admin.admin.list') }}">
                <i class="menu-icon las la-user-friends"></i>
                <span class="menu-title">Admins</span>
            </a>
        </li>

        <li class="nav-item @if(in_array(request()->route()->getName(),['admin.user.list'])) active @endif">
            <a class="nav-link" href="{{ route('admin.user.list') }}">
                <i class="menu-icon las la-user-friends"></i>
                <span class="menu-title">Users</span>
            </a>
        </li>

        {{-- <li class="nav-item @if(in_array(request()->route()->getName(),['admin.membership.payments'])) active @endif">
            <a class="nav-link" href="{{ route('admin.membership.payments') }}">
                <i class="menu-icon las la-credit-card"></i>
                <span class="menu-title">Payments</span>
            </a>
        </li> --}}

        <li class="nav-item  @if(in_array(request()->route()->getName(),['admin.membership.plans'])) active @endif">
            <a class="nav-link" href="{{ route('admin.membership.plans') }}">
                <i class="menu-icon las la-gem"></i>
                <span class="menu-title">Membership</span>
            </a>
        </li>

        <li class="nav-item @if(request()->routeIs('admin.customer.chat')) active @endif">
            <a class="nav-link" href="{{ route('admin.customer.chat') }}">
                <i class="menu-icon las la-envelope"></i>
                <span class="menu-title">Messages </span><span class="badge bg-danger pull-right">{{chatTotal(1)}}</span>
            </a>
        </li>

        <li class="nav-item @if(in_array(request()->route()->getName(),['admin.notification.list','admin.notification.create'])) active @endif">
            <a class="nav-link" href="{{ route('admin.notification.list') }}">
                <i class="menu-icon las la-envelope"></i>
                <span class="menu-title">Notifications</span>
            </a>
        </li>

        <li class="nav-item @if(in_array(request()->route()->getName(),['admin.supplement.list','admin.supplement.create','admin.supplement.edit'])) active @endif">
            <a class="nav-link" href="{{ route('admin.supplement.list') }}">
                <i class="menu-icon las la-book"></i>
                <span class="menu-title">Supplements</span>
            </a>
        </li>

        <li class="nav-item @if(in_array(request()->route()->getName(),['admin.exercise.list','admin.exercise.create'])) active @endif">
            <a class="nav-link" href="{{ route('admin.exercise.list') }}">
                <i class="menu-icon las la-book"></i>
                <span class="menu-title">Exercises</span>
            </a>
        </li>
        <li class="nav-item @if(in_array(request()->route()->getName(),['admin.category.list','admin.category.create'])) active @endif">
            <a class="nav-link" href="{{ route('admin.category.list') }}">
                <i class="menu-icon las la-book"></i>
                <span class="menu-title">Recipe Categories</span>
            </a>
        </li>
        <li class="nav-item @if(in_array(request()->route()->getName(),['admin.transaction.list'])) active @endif">
            <a class="nav-link" href="{{ route('admin.transaction.list') }}">
                <i class="menu-icon las la-book"></i>
                <span class="menu-title">Transaction List</span>
            </a>
        </li>
        <li class="nav-item @if(in_array(request()->route()->getName(),['admin.log.file'])) active @endif">
            <a class="nav-link" href="{{ route('admin.log.file') }}">
                <i class="menu-icon las la-book"></i>
                <span class="menu-title">Logs</span>
            </a>
        </li>
    </ul>
</nav>
