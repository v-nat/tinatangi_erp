<li class="sidebar-title">@yield('inventory-admin')</li>

<li class="sidebar-item @yield('inventoryIndex') ">
    <a href="{{route('inventory')}}" class='sidebar-link'>
        @yield('inventory-dashboard')
    </a>
</li>
<li class="sidebar-item @yield('inventoryItems') ">
    <a href="{{route('inventory.all-items')}}" class='sidebar-link'>
        <i class="bi bi-archive-fill"></i>
        <span>All Items</span>
    </a>
</li>
