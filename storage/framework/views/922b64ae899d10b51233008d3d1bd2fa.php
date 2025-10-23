<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <?php echo $__env->make('partials.app-head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>

<body class="dark light">
    <script src="<?php echo e(asset('assets/js/initTheme.js')); ?>"></script>
    <?php if(session('error')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Access Denied',
                    text: "<?php echo e(session('error')); ?>",
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            });
        </script>
    <?php endif; ?>
    <div id="app">
        <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <div id="main" class="layout-navbar">
            <?php echo $__env->make('layouts.top-navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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

        <?php echo $__env->make('layouts.loading-state', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('layouts.dark-mode-toggler-setting', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('layouts.modals.attendance-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <script src="<?php echo e(asset('assets/js/main2.js')); ?>"></script>
        <script src=" <?php echo e(asset('assets/js/dark.js')); ?>"></script>

        <?php echo $__env->make('layouts.toast-swal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <script src="<?php echo e(asset('source/jquery/datatables.js')); ?>"></script>
        <script src="<?php echo e(asset('source/jquery/datatables.min.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>"></script>
        <script src="<?php echo e(asset('js/logout.js')); ?>"></script>
        <?php echo $__env->yieldContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/layouts/app.blade.php ENDPATH**/ ?>