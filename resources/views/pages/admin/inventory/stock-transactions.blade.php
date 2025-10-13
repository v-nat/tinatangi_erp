@extends('layouts.app')
@include('partials.inventory-heading')
@section('inventoryTransactions') active @endsection
@section('headings') Transaction Records @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('inventory')}}">Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Stock Transactions</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Transactions Table</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="allTransactions" class="table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Batch</th>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Quantity</th>
                                <th>Item</th>
                                <th>Receive</th>
                                <th>Status</th>
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
    <script type="module" src="{{ asset('js/stockTransactions.js') }}"></script>
@endsection
