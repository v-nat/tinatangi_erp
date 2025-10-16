<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <?php echo $__env->make('partials.app-head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>

<body class="dark light">
    <div class="layout-navbar overflow-auto" style="max-height: 100vh; min-height: 90vh;">
        <nav class="navbar navbar-expand navbar-light">
            <div class="container-fluid">
                <div class="collapse navbar-collapse justify-content-start m-0">
                    <h3 class="card-title"><?php echo $__env->yieldContent('topTitle'); ?></h3>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <div class="<?php echo $__env->yieldContent('posTopNav'); ?>">
                            <li class="nav-item dropdown me-3">
                                <a class="nav-link active showTransactionsModal" href="#">
                                    <i class="fa-solid fa-receipt fs-4 text-gray-600"></i>
                                </a>
                            </li>
                        </div>
                    </ul>
                    <div class="dropdown">
                        <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-menu d-flex">
                                <div class="user-name text-end me-3">
                                    <h6 class="mb-0 text-gray-600">
                                        <?php echo e(auth()->user()->full_name); ?>

                                    </h6>
                                    <p class="mb-0 text-sm text-gray-600">
                                        <?php if(auth()->user()->user_type == 'employee'): ?>
                                                                                <?php
                                            $userId = auth()->user()->id;
                                            $position = null;
                                            $userType = auth()->user()->user_type;

                                            switch ($userType) {
                                                case 'supplier':
                                                    break;

                                                case 'employee':
                                                    $employee = App\Models\Employee::where('user_id', $userId)->first();

                                                    if ($employee) {
                                                        $position = $employee->position->name;
                                                                                                                                                                                                                    ?>
                                                                                <?php echo e(\Illuminate\Support\Str::upper($position)); ?>

                                                                                <?php
                                                    }
                                                    break;

                                                default:
                                                    break;
                                            } ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="user-img d-flex align-items-center">
                                    
                                </div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            </li>
                            <li id="exit-pos-btn"><a class="dropdown-item" href="#">
                                    <i class="fa-solid fa-right-from-bracket"></i> End <?php echo $__env->yieldContent('screen'); ?> Session</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="p-2 overflow-y-auto">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
    <?php echo $__env->make('layouts.loading-state', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('layouts.toast-swal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script src="<?php echo e(asset('source/jquery/datatables.js')); ?>"></script>
    <script src="<?php echo e(asset('source/jquery/datatables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>"></script>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/layouts/modules-layouts/operations-screens-layout.blade.php ENDPATH**/ ?>