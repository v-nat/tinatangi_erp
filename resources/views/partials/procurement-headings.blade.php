@if (auth()->user()->employeeRS?->department == 1)

    @section('title')Tinatangi Cafe ERP Management System @endsection
    @section('sidebar-title') @endsection
    @section('hr-admin') Human Resources Management @endsection
    @section('finance-admin') Finance and Accounting Management @endsection
    @section('procurement-admin') Procurement Management @endsection
    @section('human_resources') d-block @endsection
    @section('finance') d-block @endsection
    @section('procurement') d-block @endsection

    @section('human_resources-admin')
        <i class="bi bi-person-lines-fill"></i>
        <span>Human Resources Dashboard</span>
    @endsection

    @section('procurement-dashboard')
        <i class="bi bi-shop"></i>
        <span>Procurement Dashboard</span>
    @endsection
@endif

@section('procurement-dashboard')
    <i class="bi bi-grid-1x2-fill"></i>
    <span>Dashboard</span>
@endsection

@section('title') Procurement Management @endsection
@section('sidebar-title') Procurement Management @endsection
@section('human_resources') d-none @endsection
@section('finance') d-none @endsection
@section('procurement') d-block @endsection