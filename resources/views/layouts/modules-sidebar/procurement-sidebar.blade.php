<li class="sidebar-title">@yield('procurement-admin')</li>

<li class="sidebar-item @yield('procurementIndex') ">
    <a href="{{route('procurement.index')}}" class='sidebar-link'>
        @yield('procurement-dashboard')
    </a>
</li>
<li class="sidebar-item @yield('createPR') ">
    <a href="{{route('procurement.createPR')}}" class='sidebar-link'>
        <i class="fa-solid fa-file-invoice"></i>
        <span>Purchase Request</span>
    </a>
</li>
<li class="sidebar-item @yield('purchaseOrders') ">
    <a href="{{route('procurement.purchaseOrders')}}" class='sidebar-link'>
        <i class="fa-solid fa-cart-shopping"></i>
        <span>Purchase Orders</span>
    </a>
</li>
<li class="sidebar-item @yield('supplier') ">
    <a href="{{route('procurement.supplier')}}" class='sidebar-link'>
        <i class="fa-solid fa-truck"></i>
        <span>Suppliers</span>
    </a>
</li>
