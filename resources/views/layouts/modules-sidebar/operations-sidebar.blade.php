<li class="sidebar-title">@yield('operations-admin')</li>

<li class="sidebar-item @yield('operationsIndex') ">
    <a href="{{route('op.dashboard')}}" class='sidebar-link'>
        @yield('operations-dashboard')
    </a>
</li>