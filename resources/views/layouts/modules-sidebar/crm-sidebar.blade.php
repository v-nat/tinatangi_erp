<li class="sidebar-title">@yield('crm-admin')</li>

<li class="sidebar-item @yield('crmIndex') ">
    <a href="{{route('crm')}}" class='sidebar-link'>
        @yield('crm-dashboard')
    </a>
</li>
