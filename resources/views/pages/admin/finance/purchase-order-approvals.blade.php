@extends('layouts.app')
@include('partials.finance-accounting-heading')
@section('financePurchases') active @endsection
@section('headings') Purchase Request List @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('finance.payroll') }}">Finance</a></li>
            <li class="breadcrumb-item active" aria-current="page">Purchases</li>
        </ol>
    </nav>
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Purchase Order Request Table</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="purchaseReqTable" class="table table-hover dataTable no-footer" style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Department</th>
                                <th>Amount</th>
                                <th>Requested by</th>
                                <th>Requested Date</th>
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

    @include('layouts.modals.finance-po-mngmnt-modal')
    @include('layouts.modals.invoice-modal')
@endsection
@section('scripts')]
    <script type="module" src="{{ asset('js/purchaseRequest.js') }}   "></script>
@endsection
