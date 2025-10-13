<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    @include('partials.app-head')
</head>

<body class="dark light">
    <script src="{{ asset('assets/js/initTheme.js')}}"></script>

    <div class="container">
        @yield('content')
    </div>

    @include('layouts.loading-state')
    
    <script src="{{ asset('assets/js/main2.js') }}"></script>
    <script src=" {{ asset('assets/js/dark.js') }}"></script>

    @include('layouts.toast-swal')

    <script src="{{ asset('source/jquery/datatables.js') }}"></script>
    <script src="{{ asset('source/jquery/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/logout.js') }}"></script>
    @yield('scripts')
</body>

</html>
