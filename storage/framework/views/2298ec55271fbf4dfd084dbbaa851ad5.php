<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/font/Nunito/static/stylesheet.css')); ?>">
    <title>Login - Tinatangi ERP</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="stylesheet" href="assets/css/pages/auth.css">
    <link rel="icon" href="<?php echo e(asset('logo.png')); ?> " type="image/x-icon">
    <script src="<?php echo e(asset('assets/js/swal/dist/sweetalert2.all.min.js')); ?>"></script>
    <script src="<?php echo e(asset('source/jquery/jquery-3.6.0.min.js')); ?>"></script>
</head>

<body>
 

    
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <div class="auth-logo">
                        <a href="<?php echo e(route('home')); ?>"><img src="tinatangilogo2 - Copy.png" alt="Logo" class="h-50"></a>
                    </div>
                    <h1 class="auth-title">Log in.</h1>
                    <p class="auth-subtitle mb-5">Log in with your credentials.</p>

                    <form id="login_form" action="<?php echo e(route('admin.login')); ?>" method="POST"
                        data-login-url="<?php echo e(route('admin.login')); ?>">
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
                        
                        <button class="login-btn btn btn-primary btn-block btn-lg shadow-lg mt-5" type="submit">Log
                            in</button>
                    </form>
                    <div class="text-center mt-5 text-lg fs-4">
                        
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
        const ADMIN_LOGIN_ROUTE = "<?php echo e(route('admin.login')); ?>";
    </script>
    <script src="<?php echo e(asset('js/login.js')); ?>"></script>
</body>

</html><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/auth/login.blade.php ENDPATH**/ ?>