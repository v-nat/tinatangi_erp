@extends('layouts.app')
@include('partials.procurement-heading')
@section('createPR') active @endsection
@section('headings') Purchase Request @endsection
@section('content')
    <div class="page-title mb-4">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-first pt-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('procurement.index')}}">Procurement</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Purchase Requests</li>
                    </ol>
                </nav>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-last float-end d-flex justify-content-end">
                <button id="createPR" class="btn btn-primary">Create Purchase Request</button>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="card-title mb-3">Purchase Requests</h4>
                    <div class="d-flex align-items-center"  style="width: 320px;">
                        <label for="status_filter" class="form-label mb-0 me-2 flex-shrink-0">Filter by Status:</label>
                        <select id="status_filter" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label for="pr_type_filter" class="form-label mb-1">Filter by Type:</label>
                        <select id="pr_type_filter" class="form-select form-select-sm">
                            <option value="">All Types</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="pr_order_date_filter" class="form-label mb-1">Filter by Order Date:</label>
                        <input type="date" id="pr_order_date_filter" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label for="pr_supplier_filter" class="form-label mb-1">Filter by Supplier:</label>
                        <select id="pr_supplier_filter" class="form-select form-select-sm">
                            <option value="">All Suppliers</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="purchaseRequestTable" class="table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order No.</th>
                                <th>Type</th>
                                <th>Order Ddate</th>
                                <th>Supplier</th>
                                {{-- <th>Expected Date</th> --}}
                                <th>Delivery Date</th>
                                {{-- <th>Delivery Name</th> --}}
                                <th>Created by</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.modals.procurement-purchase-request-modal')
    @include('layouts.modals.invoice-modal')
@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/createPurchaseRequest.js') }}   "></script>
@endsection
