<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title'); ?></title>

    
    <link rel="stylesheet" href="<?php echo e(asset('css/font/Nunito/static/stylesheet.css')); ?>">
    
    
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.css')); ?>  ">

    <link rel="stylesheet" href="<?php echo e(asset('assets/vendors/iconly/bold.css')); ?>  ">

    <link rel="stylesheet" href="<?php echo e(asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css')); ?>  ">
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendors/bootstrap-icons/bootstrap-icons.css')); ?>  ">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/app.css')); ?>  ">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="<?php echo e(asset('source/jquery/jquery-3.6.0.min.js')); ?>"></script>
    <link rel="stylesheet" href="<?php echo e(asset('css/fontawesome-free-7.0.1-web/css/all.min.css')); ?>">

    <link rel="shortcut icon" href="<?php echo e(asset('assets/images/favicon.svg')); ?> " type="image/x-icon">


</head>

<body>

    
    <script>
        $(document).on('click', 'a[href]:not([target="_blank"]):not([href^="#"])', function (e) {
            // Optional: check if it's a same-page anchor or already loading
            var href = $(this).attr('href');
            if (!href || href === '#' || href.startsWith('javascript:')) return;

            // Show loader
            $('#load_screen').fadeIn();

            // Optional: delay navigation for a moment so loader shows clearly
            // Comment out if you want instant navigation
            setTimeout(() => {
                window.location.href = href;
            }, 200);

            // Prevent default to delay navigation (only if using setTimeout)
            e.preventDefault();
        });
    </script>

    <div id="app">
        <?php

$userId = auth()->user()->id;
$position = App\Models\Employee::where('id', $userId)->first()->position;    
            
        ?>
        <div id="sidebar" class="active">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header">
                    <div class="d-flex justify-content-between">
                        <div class="logo">
                            <a href=""><img style="height: 50px " src="<?php echo e(asset('tinatangilogo2 - Copy.png')); ?>  "
                                    alt="Logo" srcset=""></a>
                        </div>
                        <div class="toggler">
                            <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                        </div>
                    </div>
                </div>
                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title"><?php echo $__env->yieldContent('sidebar-title'); ?></li>

                        <li class="sidebar-item <?php echo $__env->yieldContent('dsh'); ?> ">
                            <a href="<?php echo e(route('hr.dashboard')); ?>" class='sidebar-link'>
                                <i class="bi bi-grid-1x2-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="sidebar-item <?php echo $__env->yieldContent('emplMngt'); ?> has-sub">
                            <a href="" class='sidebar-link '>
                                <i class="bi bi-people-fill"></i>
                                <span>Employee Management</span>
                            </a>
                            <ul class="submenu <?php echo $__env->yieldContent('emplMngt2'); ?>">
                                <li class="submenu-item <?php echo $__env->yieldContent('sbi1'); ?> ">
                                    <a href="<?php echo e(route('hr.employees')); ?>">Employee List</a>
                                </li>
                                <li class="submenu-item <?php echo $__env->yieldContent('sbi2'); ?> ">
                                    <a href="<?php echo e(route('hr.manage')); ?>">Manage Emloyee</a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item <?php echo $__env->yieldContent('appMngt'); ?> has-sub">
                            <a href="" class='sidebar-link '>
                                <i class="bi bi-person-check-fill"></i>
                                <span>Approval Management</span>
                            </a>
                            <ul class="submenu <?php echo $__env->yieldContent('appMngt2'); ?>">
                                <li class="submenu-item <?php echo $__env->yieldContent('sbi3'); ?> ">
                                    <a href="<?php echo e(route('hr.ot-app')); ?>">Overtime Approvals</a>
                                </li>
                                <li class="submenu-item <?php echo $__env->yieldContent('sbi4'); ?> ">
                                    <a href="<?php echo e(route('hr.leave-app')); ?>">Leave Approvals</a>
                                </li>
                            </ul>
                        </li>
                        
                        <li class="sidebar-item <?php echo $__env->yieldContent('payroll'); ?> ">
                            <a href="<?php echo e(route('hr.payroll')); ?>" class='sidebar-link'>
                                <i class="bi bi-credit-card-2-front-fill"></i>
                                <span>Payroll</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
            </div>
        </div>
        <div id="main" class='layout-navbar'>
            <nav class="navbar navbar-expand navbar-light ">
                <div class="container-fluid">
                    <a href="#" class="burger-btn d-block">
                        <i class="bi bi-justify fs-3"></i>
                    </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            <li class="nav-item dropdown me-1">
                                <a class="nav-link active dropdown-toggle" href="#" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class='bi bi-envelope bi-sub fs-4 text-gray-600'></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <h6 class="dropdown-header">Mail</h6>
                                    </li>
                                    <li><a class="dropdown-item" href="#">No new mail</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown me-3">
                                <a class="nav-link active dropdown-toggle" href="#" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class='bi bi-bell bi-sub fs-4 text-gray-600'></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <h6 class="dropdown-header">Notifications</h6>
                                    </li>
                                    <li><a class="dropdown-item">No notification available</a></li>
                                </ul>
                            </li>
                        </ul>
                        <div class="dropdown">
                            <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-menu d-flex">
                                    <div class="user-name text-end me-3">
                                        <h6 class="mb-0 text-gray-600">
                                            <?php echo e(auth()->user()->first_name . ' ' . auth()->user()->last_name); ?>

                                        </h6>
                                        <p class="mb-0 text-sm text-gray-600">
                                            <?php echo e(\Illuminate\Support\Str::upper($position->name)); ?>

                                        </p>
                                    </div>
                                    <div class="user-img d-flex align-items-center">
                                        
                                    </div>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                <li>
                                    <h6 class="dropdown-header">Hello, <?php echo e(auth()->user()->first_name); ?>!</h6>
                                </li>
                                <li><a class="dropdown-item" href="#"><i class="fa-solid fa-clock"></i>
                                        Attendance</a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('hr.ot-application', ['id' => Auth::user()->id])); ?>"><i class="fa-solid fa-business-time"></i>
                                        Apply Overtime</a></li>
                                <li><a class="dropdown-item" href="<?php echo e(route('hr.leave-application', ['id' => Auth::user()->id])); ?>"><i class="fa-solid fa-calendar-days"></i>
                                        Apply Leave</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user"></i>
                                        Prpfile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fa-solid fa-gear"></i>
                                        Settings</a></li>
                                <hr class="dropdown-divider">
                                </li>
                                <li id="logout-btn"><a class="dropdown-item" href="#">
                                    <i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            <div id="main-content">
                <div class="page-heading">
                    <h3><?php echo $__env->yieldContent('headings'); ?></h3>
                </div>

                <div class="page-content">
                    <?php echo $__env->yieldContent('content'); ?>
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
    </div>
    <div id="LoadingScreen"
        style="display: none; position: fixed; z-index: 9999; background: rgba(255,255,255,0.7); top: 0; left: 0; width: 100%; height: 100%;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
    <script src="<?php echo e(asset('source/jquery/datatables.js')); ?>"></script>
    <script src="<?php echo e(asset('source/jquery/datatables.min.js')); ?>"></script>
    
    

    <script src="<?php echo e(asset('js/logout.js')); ?>"></script>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>

</html><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/layouts/app.blade.php ENDPATH**/ ?>