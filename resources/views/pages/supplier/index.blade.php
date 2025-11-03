@extends('layouts.app')
@include('partials.supplier-heading')
@section('supplierDashboard') active @endsection
@section('headings') Supplier Dashboard @endsection
@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('supplier.approve')}}">Supplier</a></li>
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
                                <i class="fa-solid fa-peso-sign"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted font-semibold">Total Sales </h6>
                            <h5 class="font-extrabold mb-0" id="totalSales"></h5>
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
                                <i class="fa-solid fa-shop"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted font-semibold">Total Products</h6>
                            <h5 class="font-extrabold mb-0" id="totalProducts"></h5>
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
                                <i class="fa-solid fa-clock"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h6 class="text-muted font-semibold">Pending Orders</h6>
                            <h5 class="font-extrabold mb-0" id="pendingOrders"></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @include('layouts.modals.supplier-modal')
@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/supplier.js') }}   "></script>
@endsection
