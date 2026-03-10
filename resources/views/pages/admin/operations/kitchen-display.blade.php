@extends('layouts.modules-layouts.operations-screens-layout')
@section('title') Kitchen Display System | Tinatangi Cafe @endsection
@section('topTitle') Tinatangi Cafe | Kitchen Display @endsection
@section('screen') KDS @endsection
@section('posTopNav') d-none @endsection
@section('navSearch') @endsection
@section('content')

    <div class="row align-items-center min-vh-90 g-2 justify-content-center">

        <div class="card col-11 overflow-y-auto vh-80 p-4 ps-5">

            <div id="ordersToday" class="row row-cols-auto g-3 justify-content-start">

                {{-- WILL BE POPULATED BY JS --}}

            </div>
        </div>
    </div>

    <style>
        .product-card-fixed-size .ord-items-container {
            max-height: 79px;
            overflow-y: auto;
            margin-bottom: 5px;
        }

        .card {
            transition: background-color 1s ease-in-out;
        }

        .col-md-2 {
            width: 20%;
        }

        .min-vh-90 {
            min-height: 90vh;
        }

        .vh-90 {
            height: 89vh;
        }

        .vh-80 {
            height: 77vh;
        }

        .product-card-fixed-size {
            height: 300px;
            width: 300px;
        }

        .product-card-fixed-size .prod-price {
            margin-top: auto;
            font-size: 1rem;
            font-weight: 700;
        }

        .product-card-fixed-size .prod-name {
            font-size: 0.9rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {

            .product-card-fixed-size {
                height: 220px;
                width: 100%;
            }

            .product-card-fixed-size .card-img-top {
                height: 150px;
            }

            h6 {
                font-size: 12px
            }
        }

        @media (max-width: 1080px) {

            .product-card-fixed-size {
                height: 250px;
                width: 250px;
            }

            .product-card-fixed-size .ord-items-container {
                min-height: 49px;
                max-height: 49px;
                overflow-y: auto;
                margin-bottom: 5px;
            }

            h6 {
                font-size: 14px
            }
        }
    </style>
@endsection
@section('scripts')
    @vite('resources/js/kitchenDisplay.js')
@endsection
