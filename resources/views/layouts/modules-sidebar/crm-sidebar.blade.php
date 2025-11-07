<li class="sidebar-title">@yield('crm-admin')</li>

<li class="sidebar-item @yield('crmIndex') ">
    <a href="{{route('crm')}}" class='sidebar-link'>
        @yield('crm-dashboard')
    </a>
</li>
<li class="sidebar-item @yield('crmBooking') ">
    <a href="{{route('crm.bookings')}}" class='sidebar-link'>
        <i class="fa-solid fa-book"></i>
        <span>Booking Management</span>
    </a>
</li>
<li class="sidebar-item @yield('crmFeedback') ">
    <a href="{{route('crm.feedback')}}" class='sidebar-link'>
        <i class="fa-solid fa-comments"></i>
        <span>Feedback Management</span>
    </a>
</li>
<li class="sidebar-item @yield('crmFaqs') ">
    <a href="{{route('crm.faqs')}}" class='sidebar-link'>
        <i class="fa-solid fa-circle-question"></i>
        <span>FAQ Management</span>
    </a>
</li>
