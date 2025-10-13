<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    @include('partials.app-head')
</head>

<body class="dark light">
    <script src="{{ asset('assets/js/initTheme.js')}}"></script>
    <div id="app">
        @include('layouts.top-bar-user-info')
        @include('layouts.sidebar')
        <div id="main" class="layout-navbar">
            @include('layouts.top-navbar')
            <div id="main-content">
                <div class="page-heading">
                    <h3>@yield('headings')</h3>
                </div>

                <div class="page-content">
                    @yield('content')
                </div>

                <footer>
                    <div class="footer clearfix mb-0 text-muted">
                        <div class="float-start">
                            <p>2025 &copy; Tinatangi Cafe</p>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        @include('layouts.loading-state')
        @include('layouts.dark-mode-toggler-setting')
        @include('layouts.modals.attendance-modal')

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
