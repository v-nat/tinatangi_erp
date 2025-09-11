<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Tinatangi ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="stylesheet" href="assets/css/pages/auth.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('source/jquery/jquery-3.6.0.min.js') }}"></script>
</head>

<body>
 {{-- use App\Models\User;
 use Illuminate\Support\Facades\Hash;
 DB::beginTransaction();
 $user = User::create([
     'first_name' => 'username',
     'email' => 'admin@gmail.com',
     'password' => Hash::make('admin123'),
     'middle_name' => 'middle_name',
     'last_name' => 'last_name',
     'phone_number' => '09162627995',
     'user_type' => 'unknown',
     'status' => 1,
 ]);
 DB::commit(); --}}

    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <div class="auth-logo">
                        <a href="{{ route('home') }}"><img src="tinatangilogo2 - Copy.png" alt="Logo" class="h-50"></a>
                    </div>
                    <h1 class="auth-title">Log in.</h1>
                    <p class="auth-subtitle mb-5">Log in with your credentials.</p>

                    <form id="login_form" action="{{ route('admin.login') }}" method="POST"
                        data-login-url="{{ route('admin.login') }}">
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="email" class="form-control form-control-xl" placeholder="Username"
                                name="email" id="email" required autofocus>
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
                        <p class="text-gray-600">Don't have an account? <a href="auth-register.html"
                                class="font-bold">Sign
                                up</a>.</p>
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

    </div>
    <script>
        const ADMIN_LOGIN_ROUTE = "{{ route('admin.login') }}";
    </script>
    <script src="{{ asset('js/login.js') }}"></script>
</body>

</html>