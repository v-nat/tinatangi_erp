<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title')</title>

<link href="{{ asset('source/css/datatables.min.css') }}" rel="stylesheet">
</link>

<link rel="stylesheet" href="{{ asset('css/font/Nunito/static/stylesheet.css')}}">
{{--
<link rel="stylesheet" href="{{ asset('css/font/Nunito/stylesheet.css')}}"> --}}

<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}  ">
<link rel="stylesheet" href="{{ asset('assets/vendors/iconly/bold.css') }}  ">

<link rel="stylesheet" href="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css') }}  ">
<link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}  ">
<link rel="stylesheet" href="{{ asset('assets/css/app.css') }}  ">
{{--
<script src="assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script> --}}

<link rel="stylesheet" crossorigin href=" {{ asset('assets/css/app-light.css') }}" />
<link rel="stylesheet" crossorigin href=" {{ asset('assets/css/app-dark.css') }}" />

<script src="{{ asset('source/jquery/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('assets/js/swal/dist/sweetalert2.all.min.js') }}"></script>

<link rel="stylesheet" href="{{ asset('css/fontawesome-free-7.0.1-web/css/all.min.css') }}">


<link rel="icon" href="{{ asset('logo.png') }} " type="image/x-icon">
<style>
    html[data-bs-theme=dark] .pagination .page-item.disabled .page-link {
        background-color: #000;
        color: #6c757d;
        border-color: #000;
    }

    .dt-column-header {
        color: #6c757d !important;
        font-weight: 610 !important;
    }

    .card-title {
        color: #6c757d !important;
        font-weight: 700 !important;
    }
</style>
