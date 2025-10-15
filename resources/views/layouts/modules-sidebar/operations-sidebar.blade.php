<li class="sidebar-title">@yield('operations-admin')</li>

<li class="sidebar-item @yield('operationsIndex') ">
    <a href="{{route('op.dashboard')}}" class='sidebar-link'>
        @yield('operations-dashboard')
    </a>
</li>

<li class="sidebar-item @yield('operationsPOS') ">
    <a href="{{route('op.pos')}}" class='sidebar-link'>
        <i class="fa-solid fa-desktop"></i>
        <span>Point of Sale</span>
    </a>
</li>

<li class="sidebar-item @yield('operationsKDS') ">
    <a href="{{route('op.kds')}}" class='sidebar-link'>
        <i class="fa-solid fa-desktop"></i>
        <span>Kitchen Display</span>
    </a>
</li>
