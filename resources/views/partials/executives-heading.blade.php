@if (auth()->user()->employeeRS?->department == 1)

    @section('title')Tinatangi Cafe ERP Management System @endsection
    @section('sidebar-title') Executives @endsection
    @section('hr-executives') Human Resources @endsection
    @section('finance-executives') Finance and Accounting @endsection
    @section('procurement-executives') Procurement @endsection
    @section('operations-executives') Service Operations @endsection
    @section('crm-executives') Customer Relationship @endsection
    @section('executives') d-block @endsection
    @section('human_resources') d-block @endsection
    @section('finance') d-block @endsection
    @section('procurement') d-block @endsection
    @section('inventory') d-block @endsection
    @section('operations') d-block @endsection
    @section('crm') d-block @endsection
    @section('supplierPage') d-none @endsection
    @section('general_employee') d-none @endsection

    @section('human_resources-executives')
        <i class="bi bi-person-lines-fill"></i>
        <span>Human Resources Dashboard</span>
    @endsection

    @section('finance-dashboard')
        <i class="fa-solid fa-chart-pie"></i>
        <span>Finance Dashboard</span>
    @endsection

    @section('procurement-dashboard')
        <i class="fa-solid fa-cart-flatbed"></i>
        <span>Procurement Dashboard</span>
    @endsection

    @section('inventory-dashboard')
        <i class="fa-solid fa-warehouse"></i>
        <span>Inventory Dashboard</span>
    @endsection

    @section('operations-dashboard')
        <i class="bi bi-grid-1x2-fill"></i>
        <span>Operations Dashboard</span>
    @endsection

    @section('crm-dashboard')
        <i class="fa-solid fa-people-group"></i>
        <span>Customer Relationship Dashboard</span>
    @endsection

    @section('dsh') d-none @endsection
    @section('emplMngt')d-none @endsection
    @section('emplMngt2')d-none @endsection
    @section('sbi1')d-none @endsection
    @section('emplMngt')d-none @endsection
    @section('emplMngt2')d-none @endsection
    @section('sbi2')d-none @endsection
    @section('payroll') d-none @endsection
    @section('financeBudgets') d-none @endsection
    @section('financePayroll') d-none @endsection
    @section('financePurchases') d-none @endsection
    @section('financeSales') d-none @endsection
    @section('createPR') d-none @endsection
    @section('supplier') d-none @endsection
    @section('purchaseOrders') d-none @endsection
    @section('inventoryItems') d-none @endsection
    @section('inventoryProducts') d-none @endsection
    @section('inventoryTransactions') d-none @endsection
    @section('inventoryRecipes') d-none @endsection
    @section('inventoryIndex') d-none @endsection
    @section('operationsSalesReports') d-none @endsection
    @section('operationsPOS') d-none @endsection
    @section('operationsKDS') d-none @endsection
    @section('crmBooking') d-none @endsection
    @section('crmFaqs') d-none @endsection
    @section('crmFeedback') d-none @endsection
    @section('crmTables') d-none @endsection

@endif
