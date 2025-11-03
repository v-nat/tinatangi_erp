<li class="sidebar-item @yield('supplierDashboard') ">
    <a href="{{route('supplier')}}" class='sidebar-link'>
        <i class="bi bi-grid-1x2-fill"></i>
        <span>Dashboard</span>
    </a>
</li>
<li class="sidebar-item @yield('supplierApprove') ">
    <a href="{{route('supplier.approve')}}" class='sidebar-link'>
        <i class="bi bi-truck"></i>
        <span>Approve Orders</span>
    </a>
</li>
