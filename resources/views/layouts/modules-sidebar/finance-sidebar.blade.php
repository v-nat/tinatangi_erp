<li class="sidebar-title">@yield('finance-executives')</li>

<li class="sidebar-item @yield('financeIndex') ">
    <a href="{{route('finance')}}" class='sidebar-link'>
        @yield('finance-dashboard')
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
        <i class="fa-solid fa-clipboard-check"></i>
        <span>Purchase Order Approvals</span>
    </a>
</li>
<li class="sidebar-item @yield('financeBudgets') ">
    <a href="{{route('finance.budgets')}}" class='sidebar-link'>
        <i class="fa-solid fa-money-check-dollar"></i>
        <span>Budget Releasing</span>
    </a>
</li>

<li class="sidebar-item @yield('financeSales') ">
    <a href="{{ route('finance.sales') }}" class='sidebar-link'>
        <i class="fa-solid fa-receipt"></i>
        <span>Sales Transactions</span>
    </a>
</li>
