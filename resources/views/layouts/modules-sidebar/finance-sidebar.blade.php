<li class="sidebar-title">@yield('finance-admin')</li>

<li class="sidebar-item @yield('financeIndex') ">
    <a href="{{route('finance')}}" class='sidebar-link'>
        <i class="bi bi-bar-chart-line-fill"></i>
        <span>Index</span>
    </a>
</li>
<li class="sidebar-item @yield('financePayroll') ">
    <a href="{{route('finance.payroll')}}" class='sidebar-link'>
        <i class="bi bi-credit-card-2-front-fill"></i>
        <span>Payroll Approvals</span>
    </a>
</li>
<li class="sidebar-item @yield('financePurchases') ">
    <a href="{{route('finance.purchases')}}" class='sidebar-link'>
        <i class="bi bi-bag-fill"></i>
        <span>Purchase Order Approvals</span>
    </a>
</li>
<li class="sidebar-item @yield('financeBudgets') ">
    <a href="{{route('finance.budgets')}}" class='sidebar-link'>
        <i class="bi bi-cash"></i>
        <span>Budget Releasing</span>
    </a>
</li>
