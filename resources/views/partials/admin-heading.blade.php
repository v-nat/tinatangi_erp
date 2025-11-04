@if (auth()->user()->employeeRS?->department == 1)

    @section('title')Tinatangi Cafe ERP Management System @endsection
    @section('sidebar-title') Administrator @endsection
    @section('hr-admin') Human Resources Management @endsection
    @section('finance-admin') Finance and Accounting Management @endsection
    @section('procurement-admin') Procurement Management @endsection
    @section('inventory-admin') Inventory Management @endsection
    @section('operations-admin') Service Operations @endsection
    @section('crm-admin') Customer Relationship Management @endsection
    @section('admin') d-block @endsection
    @section('human_resources') d-block @endsection
    @section('finance') d-block @endsection
    @section('procurement') d-block @endsection
    @section('inventory') d-block @endsection
    @section('operations') d-block @endsection
    @section('crm') d-block @endsection
    @section('supplierPage') d-none @endsection
    @section('general_employee') d-none @endsection

    @section('human_resources-admin')
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

@endif
