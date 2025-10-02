@extends('layouts.app')
@section('title') Procurement Management @endsection
@section('sidebar-title') Procurement Management @endsection
@section('human_resources') d-none @endsection
@section('finance') d-none @endsection
@section('procurement') d-block @endsection
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
            <div class="card-header">
                <h4 class="card-title">Purchase Table</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="purchaseOrderTable" class="table table-hover dataTable no-footer" style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order No.</th>
                                <th>Type</th>
                                <th>Order Ddate</th>
                                <th>Supplier</th>
                                <th>Expected Date</th>
                                <th>Delivery Date</th>
                                <th>Delivery Name</th>
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
@endsection
@section('scripts')
    <script src="{{ asset('js/purchaseOrders.js') }}   "></script>
@endsection