<li class="sidebar-title">@yield('hr-admin')</li>

<li class="sidebar-item @yield('dsh') ">
    <a href="{{route('hr.dashboard')}}" class='sidebar-link'>
        @yield('human_resources-admin')
    </a>
</li>
<li class="sidebar-item @yield('emplMngt') has-sub">
    <a href=" " class='sidebar-link '>
        <i class="bi bi-people-fill"></i>
        <span>Employee Management</span>
    </a>
    <ul class="submenu @yield('emplMngt2')">
        <li class="submenu-item @yield('sbi1') ">
            <a href="{{route('hr.employees')}}">Employee List</a>
        </li>
        <li class="submenu-item @yield('sbi2') ">
            <a href="{{ route('hr.manage') }}">Manage Emloyee</a>
        </li>
    </ul>
</li>
<li class="sidebar-item @yield('appMngt') has-sub">
    <a href=" " class='sidebar-link '>
        <i class="bi bi-person-check-fill"></i>
        <span>Approval Management</span>
    </a>
    <ul class="submenu @yield('appMngt2')">
        <li class="submenu-item @yield('sbi3') ">
            <a href="{{ route('hr.ot-app') }}">Overtime Approvals</a>
        </li>
        <li class="submenu-item @yield('sbi4') ">
            <a href="{{ route('hr.leave-app') }}">Leave Approvals</a>
        </li>
    </ul>
</li>
<li class="sidebar-item @yield('payroll') ">
    <a href="{{route('hr.payroll')}}" class='sidebar-link'>
        <i class="bi bi-credit-card-2-front-fill"></i>
        <span>Payroll</span>
    </a>
</li>
