<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    @include('partials.app-head')
</head>

<body class="dark light">
    <script src="{{ asset('assets/js/initTheme.js')}}"></script>
    <div id="app">
        <?php
$userId = auth()->user()->id;
if (auth()->user()->user_type == 'supplier') {
} else if (auth()->user()->user_type == 'employee') {
    $position = App\Models\Employee::where('id', $userId)->first()->position;
}
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
            style="display: none; position: fixed; z-index: 9999; background: rgba(0,0,0,0.5); top: 0; left: 0; width: 100%; height: 100%;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem" role="status"></div>
            </div>
        </div>

        <div id="untilLoaded"
            style="display:block; position: fixed; z-index: 9999; background: rgba(0,0,0,0.5); top: 0; left: 0; width: 100%; height: 100%;">
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
            function toggleLogosByTheme() {
                const currentTheme = document.documentElement.getAttribute('data-bs-theme');

                const $lightLogo = $('#light-logo');
                const $darkLogo = $('#dark-logo');
                const $loadingScreen = $('#LoadingScreen');
                const $untilLoadedScreen = $('#untilLoaded');

                const lightModeBg = 'rgba(255, 255, 255, 0.7)';
                const darkModeBg = 'rgba(0, 0, 0, 0.7)';

                const spinnerLightText = 'text-primary';
                const spinnerDarkText = 'text-primary';

                if (currentTheme === 'dark') {
                    $darkLogo.removeClass('d-none');
                    $lightLogo.addClass('d-none');

                    $loadingScreen.css('background', darkModeBg);
                    $untilLoadedScreen.css('background', darkModeBg);

                    $loadingScreen.find('.spinner-border').removeClass(spinnerLightText).addClass(spinnerDarkText);
                    $untilLoadedScreen.find('.spinner-border').removeClass(spinnerLightText).addClass(spinnerDarkText);

                } else {
                    $lightLogo.removeClass('d-none');
                    $darkLogo.addClass('d-none');

                    $loadingScreen.css('background', lightModeBg);
                    $untilLoadedScreen.css('background', lightModeBg);

                    $loadingScreen.find('.spinner-border').removeClass(spinnerDarkText).addClass(spinnerLightText);
                    $untilLoadedScreen.find('.spinner-border').removeClass(spinnerDarkText).addClass(spinnerLightText);
                }
            }

            $(document).ready(function () {
                toggleLogosByTheme();
            });
        </script>
        <script src="{{ asset('assets/js/main2.js') }}"></script>
        <script src=" {{ asset('assets/js/dark.js') }}"></script>
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
                    var href = $(this).attr("href");
                    if (!href || href === "#" || href.startsWith("javascript:")) return;

                    $("#LoadingScreen").fadeIn(200);

                    setTimeout(() => {
                        window.location.href = href;
                    }, 200);

                    e.preventDefault();
                }
            );
            window.addEventListener('load', function () {
                const loader = document.getElementById('untilLoaded');
                loader.style.opacity = '0';
                setTimeout(() => loader.style.display = 'none', 700);
            });

        </script>
        <script src="{{ asset('source/jquery/datatables.js') }}"></script>
        <script src="{{ asset('source/jquery/datatables.min.js') }}"></script>
        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('js/logout.js') }}"></script>
        @yield('scripts')
</body>

</html>
