@extends('layouts.app')
@include('partials.procurement-heading')
@section('supplier') active @endsection
@section('headings') Supplier List @endsection
@section('content')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-first pt-3">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-start">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Procurement</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Suppliers</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section mt-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Supplier Table</h4>

                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-primary btn-sm" id="add_supplier">
                        <i class="fa-solid fa-plus"></i> Add New Supplier
                    </button>
                    <button class="btn btn-outline-primary btn-sm" id="btn-refresh-suppliers">
                        <i class="fa-solid fa-rotate"></i> Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover dataTable no-footer" style="width:100% !important; table-layout:fixed"
                        id="supplier_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Supplier</th>
                                <th>Email Address</th>
                                <th>Phone Number</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.modals.procurement-mng-supplier-modal')

@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/manageSupplier.js') }}   "></script>
@endsection
