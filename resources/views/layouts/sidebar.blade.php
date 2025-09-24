<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href=""><img style="height: 50px " src="{{ asset('tinatangilogo2 - Copy.png') }}  " alt="Logo"
                            srcset=""></a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">@yield('sidebar-title')</li>

                <div class="@yield('human_resources')">
                    <li class="sidebar-item @yield('dsh') ">
                        <a href="{{route('hr.dashboard')}}" class='sidebar-link'>
                            <i class="bi bi-grid-1x2-fill"></i>
                            <span>Dashboard</span>
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
                </div>

                <div class="@yield('finance')">
                    <li class="sidebar-item @yield('financePayroll') ">
                        <a href="{{route('finance.payroll')}}" class='sidebar-link'>
                            <i class="bi bi-credit-card-2-front-fill"></i>
                            <span>Payroll Approvals</span>
                        </a>
                    </li>
                </div>

                <div class="@yield('procurement')">
                    <li class="sidebar-item @yield('procurementIndex') ">
                        <a href="{{route('procurement.index')}}" class='sidebar-link'>
                            <i class="bi bi-bag-fill"></i>
                            <span>Index</span>
                        </a>
                    </li>
                </div>


            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>