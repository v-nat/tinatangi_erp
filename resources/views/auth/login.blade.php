<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/font/Nunito/static/stylesheet.css')}}">
    <title>Login - Tinatangi ERP</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="stylesheet" href="assets/css/pages/auth.css">
    <link rel="icon" href="{{ asset('logo.png') }} " type="image/x-icon">
    <script src="{{ asset('assets/js/swal/dist/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('source/jquery/jquery-3.6.0.min.js') }}"></script>
</head>

<body>
    <script>
        $(document).on(
            "click",
            'a[href]:not([target="_blank"]):not([href^="#"])',
            function (e) {
                // Optional: check if it's a same-page anchor or already loading
                var href = $(this).attr("href");
                if (!href || href === "#" || href.startsWith("javascript:")) return;

                // Show loader
                $("#LoadingScreen").fadeIn();

                // Optional: delay navigation for a moment so loader shows clearly
                // Comment out if you want instant navigation
                setTimeout(() => {
                    window.location.href = href;
                }, 200);

                // Prevent default to delay navigation (only if using setTimeout)
                e.preventDefault();
            }
        );
    </script>
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <div class="auth-logo">
                        <a href="{{ route('home') }}"><img src="tinatangilogo2 - Copy.png" alt="Logo" class="h-50"></a>
                    </div>
                    <h1 class="auth-title">Log in.</h1>
                    <p class="auth-subtitle mb-5">Log in with your credentials.</p>

                    <form id="login_form" method="POST">
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="email" class="form-control form-control-xl" placeholder="Username" name="email"
                                id="email" required autofocus>
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl" placeholder="Password"
                                name="password" id="password" required>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                        </div>
                        {{-- <div class="form-check form-check-lg d-flex align-items-end">
                            <input class="form-check-input me-2" type="checkbox" value="" id="flexCheckDefault">
                            <label class="form-check-label text-gray-600" for="flexCheckDefault">
                                Keep me logged in
                            </label>
                        </div> --}}
                        <button class="login-btn btn btn-primary btn-block btn-lg shadow-lg mt-5" type="submit">Log
                            in</button>
                    </form>
                    <div class="text-center mt-5 text-lg fs-4">
                        {{-- <p class="text-gray-600">Don't have an account? <a href="auth-register.html"
                                class="font-bold">Sign
                                up</a>.</p> --}}
                        <p><a class="font-bold" href="auth-forgot-password.html">Forgot password?</a>.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right">
                    <img src="/img/img2.png" alt="" class="h-100">
                </div>
            </div>
        </div>
        <div id="LoadingScreen"
            style="display: none; position: fixed; z-index: 9999; background: rgba(255,255,255,0.7); top: 0; left: 0; width: 100%; height: 100%;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: "bottom",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            },
        });
    </script>
    <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>