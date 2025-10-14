@extends('layouts.modules-layouts.pos-layout')
@section('title') Point of Sale System | Tinatangi Cafe @endsection
@section('content')
    <div class="container-fluid p-0">

        <div class="row align-items-stretch min-vh-90 g-2">

            <div class="col-12 col-md-9 d-flex">
                <div class="card w-100">
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-2">
                                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                    aria-orientation="vertical">
                                    <a class="nav-link active" id="v-pills-all-tab" data-bs-toggle="pill"
                                        href="#v-pills-all" role="tab" aria-controls="v-pills-all"
                                        aria-selected="true">All</a>
                                    <a class="nav-link" id="v-pills-pastries-tab" data-bs-toggle="pill"
                                        href="#v-pills-pastries" role="tab" aria-controls="v-pills-pastries"
                                        aria-selected="false">Pastries</a>
                                    <a class="nav-link" id="v-pills-beverages-tab" data-bs-toggle="pill"
                                        href="#v-pills-beverages" role="tab" aria-controls="v-pills-beverages"
                                        aria-selected="false">Beverages</a>
                                    <a class="nav-link" id="v-pills-meals-tab" data-bs-toggle="pill" href="#v-pills-meals"
                                        role="tab" aria-controls="v-pills-meals" aria-selected="false">Meals</a>
                                </div>
                            </div>
                            <div class="col-10 overflow-y-auto vh-80">
                                <div class="tab-content" id="v-pills-tabContent">
                                    <div class="tab-pane fade show active" id="v-pills-all" role="tabpanel"
                                        aria-labelledby="v-pills-all-tab">

                                        <div class="tab-pane fade show active" id="v-pills-all"
                                            role="tabpanel" aria-labelledby="v-pills-all-tab">

                                            <div class="row row-cols-auto g-3 justify-content-start">
                                                @for ($i = 0; $i < 20; $i++)
                                                    <div class="col">
                                                        <div class="card shadow h-100 product-card-fixed-size d-flex p-2 m-2">

                                                            <img src="{{ asset('img/coffee-tinatangilatte.png') }}"
                                                                class="card-img-top img-fluid prod-img" alt="Product Image">

                                                            <div class="card-body p-2 flex-grow-1">
                                                                <h6 class="card-title mb-1 prod-name">Espresso Delight</h6>
                                                                <h6 class="text-success mb-0 prod-price">₱ 100.00</h6>
                                                            </div>

                                                            <div class="card-footer p-1">
                                                                <button class="btn btn-sm btn-primary w-100">Add</button>
                                                            </div>

                                                        </div>
                                                    </div>
                                                @endfor

                                            </div>
                                        </div>


                                    </div>
                                    <div class="tab-pane fade" id="v-pills-pastries" role="tabpanel"
                                        aria-labelledby="v-pills-pastries-tab">
                                        Integer interdum diam eleifend metus lacinia, quis gravida eros
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-beverages" role="tabpanel"
                                        aria-labelledby="v-pills-beverages-tab">
                                        Integer pretium dolor at sapien laoreet ultricies. Fusce congue et
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-meals" role="tabpanel"
                                        aria-labelledby="v-pills-meals-tab">
                                        Sed lacus quam, convallis quis condimentum ut, accumsan congue
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3 d-flex overflow-y-auto vh-90">
                <div class="card w-100">
                    <div class="card-header">
                        <h2 class="card-title">Order</h2>
                    </div>

                    <div class="card-body  overflow-y-auto">
                        @for ($i = 0; $i < 20; $i++)
                            <div class="d-flex align-items-center py-2 border-bottom">
                                <div class="flex-grow-1 me-3">
                                    <h6 class="mb-0 text-dark">Espresso Delight</h6>
                                    <small class="text-secondary">₱ 100.00</small>
                                </div>
                                <div class="d-flex align-items-center justify-content-between" style="width: 110px;">
                                    <a href="#" class="btn btn-sm btn-danger p-1">
                                        <i class="fa-solid fa-minus"></i>
                                    </a>
                                    <h6 class="mb-0 mx-2">1</h6>
                                    <a href="#" class="btn btn-sm btn-primary p-1">
                                        <i class="fa-solid fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        @endfor
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title mb-3">Total Cost:</h6>
                            <h6 class="mb-3" id="order-total-amount">₱ 100.00</h6>
                        </div>

                        <button class="btn btn-lg btn-primary w-100 -mt-2">
                            Complete Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .min-vh-90 {
            min-height: 90vh;
        }
        .vh-90 {
            height: 89vh;
        }
        .vh-80 {
            height: 75vh;
        }

        .product-card-fixed-size {
            height: 250px;
            width: 250px;
        }

        .product-card-fixed-size .card-img-top {
            height: 200px;
            object-fit: cover;
        }
    </style>
@endsection
@section('scripts')
    <script type="module" src="{{ asset('js/pointOfSale.js') }}   "></script>
@endsection
