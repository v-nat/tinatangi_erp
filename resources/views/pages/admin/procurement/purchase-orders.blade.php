@extends('layouts.app')
@include('partials.procurement-heading')
@section('purchaseOrders') active @endsection
@section('headings') Purchase Orders @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('procurement.index')}}">Procurement</a></li>
            <li class="breadcrumb-item active" aria-current="page">Purchase Orders</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-3">Purchase Orders</h4>

                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="d-flex align-items-center" style="min-width: 240px;">
                            <label for="status_filter" class="form-label mb-0 me-2 flex-shrink-0">Filter by Status:</label>
                            <select id="status_filter" class="form-select form-select-sm">
                                <option value="">All Statuses</option>
                            </select>
                        </div>
                        <button class="btn btn-outline-primary btn-sm" id="btn-refresh-purchase-orders">
                            <i class="fa-solid fa-rotate"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label for="supplier_filter" class="form-label mb-1">Filter by Supplier:</label>
                        <select id="supplier_filter" class="form-select form-select-sm">
                            <option value="">All Suppliers</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="order_date_filter" class="form-label mb-1">Filter by Order Date:</label>
                        <input type="date" id="order_date_filter" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label for="delivery_date_filter" class="form-label mb-1">Filter by Delivery Date:</label>
                        <input type="date" id="delivery_date_filter" class="form-control form-control-sm">
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="purchaseOrderTable" class="table table-hover dataTable no-footer"
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

    @include('layouts.modals.procurement-purchase-orders-modal')
    @include('layouts.modals.invoice-modal')
@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/purchaseOrders.js') }}   "></script>
@endsection
