@extends('layouts.app')
@include('partials.supplier-heading')
@section('supplierApprove') active @endsection
@section('headings') Purchase Orders @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('supplier.approve')}}">Supplier</a></li>
            <li class="breadcrumb-item active" aria-current="page">Purchase Orders</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Order Table</h4>
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
@endsection
@section('scripts')
    <script src="{{ asset('js/supplier.js') }}   "></script>
@endsection