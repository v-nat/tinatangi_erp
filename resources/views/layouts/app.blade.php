<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    {{--
    <link rel="preconnect" href="https://fonts.gstatic.com"> --}}
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

    <script src="{{ asset('source/jquery/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/swal/dist/sweetalert2.all.min.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('css/fontawesome-free-7.0.1-web/css/all.min.css') }}">

    <link rel="icon" href="{{ asset('logo.png') }} " type="image/x-icon">
</head>

<body>
    @if(session('success'))
        <script>
            Toast.fire({
                icon: "success",
                title: "{{ session('success') }}"
            });
        </script>
    @endif
    @if(session('failed'))
        <script>
            Toast.fire({
                icon: "error",
                title: "{{ session('failed') }}"
            });
        </script>
    @endif

    <div id="app">
        <?php
$userId = auth()->user()->id;
$position = App\Models\Employee::where('id', $userId)->first()->position;         
        ?>
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
        <div id="LoadingScreen"
            style="display: none; position: fixed; z-index: 9999; background: rgba(255,255,255,0.7); top: 0; left: 0; width: 100%; height: 100%;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem" role="status"></div>
            </div>
        </div>

        <div id="untilLoaded"
            style="display:block; position: fixed; z-index: 9999; background: rgba(255,255,255,0.7); top: 0; left: 0; width: 100%; height: 100%;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem" role="status"></div>
            </div>
        </div>

        <style>
            #untilLoaded {
                transition: opacity 0.5s ease;
            }
        </style>
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: "top",
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                },
            });

            $(document).on(
                "click",
                'a[href]:not([target="_blank"]):not([href^="#"]):not([href^=" "])',
                function (e) {
                    // console.log('loading');
                    // Optional: check if it's a same-page anchor or already loading
                    var href = $(this).attr("href");
                    if (!href || href === "#" || href.startsWith("javascript:")) return;

                    // Show loader
                    $("#LoadingScreen").fadeIn(200);

                    // Optional: delay navigation for a moment so loader shows clearly
                    // Comment out if you want instant navigation
                    setTimeout(() => {
                        window.location.href = href;
                    }, 200);

                    // Prevent default to delay navigation (only if using setTimeout)
                    e.preventDefault();
                }
            );
            window.addEventListener('load', function () {
                const loader = document.getElementById('untilLoaded');
                loader.style.opacity = '0';
                setTimeout(() => loader.style.display = 'none', 500);
            });

        </script>
        <script src="{{ asset('source/jquery/datatables.js') }}"></script>
        <script src="{{ asset('source/jquery/datatables.min.js') }}"></script>
        {{--
        <link href="{{ asset( 'source/css/datatables.css') }}" rel="stylesheet">
        </link> --}}
        {{--
        <link href="{{ asset( 'source/css/datatables.min.css') }}" rel="stylesheet">
        </link> --}}

        <script src="{{ asset('js/logout.js') }}"></script>
        @yield('scripts')
</body>

</html>