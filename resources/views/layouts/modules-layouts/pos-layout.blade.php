<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    @include('partials.app-head')
</head>

<body class="dark light">
    <div class="">
        <a href="{{ route('op.dashboard') }}">End POS Session</a>
    </div>
    <div class="container">
        @yield('content')
    </div>

    @include('layouts.loading-state')
    @include('layouts.toast-swal')

    <script src="{{ asset('source/jquery/datatables.js') }}"></script>
    <script src="{{ asset('source/jquery/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/logout.js') }}"></script>
    @yield('scripts')
</body>

</html>
