@extends('layouts.app')
@include('partials.inventory-heading')
@section('inventoryIndex') active
@endsection
@section('headings') Inventory @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('inventory')}}">Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>


    <div class="row">
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-3 py-4-5">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stats-icon green">
                                <i class="fa-solid fa-truck"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted font-semibold">To Receive </h6>
                            <h6 class="font-extrabold mb-0">{{ $purchaseOrders }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-3 py-4-5">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stats-icon blue">
                                <i class="fa-solid fa-warehouse"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted font-semibold">Total Stocks</h6>
                            <h6 class="font-extrabold mb-0"></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-3 py-4-5">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stats-icon bg-warning">
                                <i class="fa-solid fa-arrow-trend-down"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted font-semibold">Low Stocks</h6>
                            <h6 class="font-extrabold mb-0">112.000</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-3 py-4-5">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="stats-icon red">
                                <i class="fa-solid fa-exclamation"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted font-semibold">Out of Stock</h6>
                            <h6 class="font-extrabold mb-0">112</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="section row mb-2">
        <div class="col-6 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">To Receive Items</h4>
                </div>
                <div id="invClaims" class="card-body">

                </div>
            </div>

        </div>

        <div class="col-6 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Need for Restock</h4>
                </div>
                <div id="invRestock" class="card-body">
                    <div class="alert alert-light-warning">No purchase requests are currently low in stocks.</div>
                </div>
            </div>

        </div>
    </section>


    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Items Table</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="purchaseOrderTable" class="table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order No.</th>
                                <th>Order Ddate</th>
                                <th>Delivery Date</th>
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

    @include('layouts.modals.invoice-modal')
@endsection
@section('scripts')
    <script src="{{ asset('js/inventoryDashboard.js') }}   "></script>
@endsection
