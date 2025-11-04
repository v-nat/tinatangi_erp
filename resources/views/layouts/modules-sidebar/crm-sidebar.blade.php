<li class="sidebar-title">@yield('crm-admin')</li>

<li class="sidebar-item @yield('crmIndex') ">
    <a href="{{route('crm')}}" class='sidebar-link'>
        @yield('crm-dashboard')
    </a>
</li>
<li class="sidebar-item @yield('crmFeedback') ">
    <a href="{{route('crm.feedback')}}" class='sidebar-link'>
        <i class="bi bi-grid-1x2-fill"></i>
        <span>Service Feedback</span>
    </a>
</li>
