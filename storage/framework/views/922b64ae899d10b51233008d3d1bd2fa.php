<!DOCTYPE html>
<html lang="en">

<head>
    <?php echo $__env->make('partials.app-head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>

<body>
    <?php if(session('success')): ?>
        <script>
            Toast.fire({
                icon: "success",
                title: "<?php echo e(session('success')); ?>"
            });
        </script>
    <?php endif; ?>
    <?php if(session('failed')): ?>
        <script>
            Toast.fire({
                icon: "error",
                title: "<?php echo e(session('failed')); ?>"
            });
        </script>
    <?php endif; ?>

    <div id="app">
        <?php
$userId = auth()->user()->id;
if (auth()->user()->user_type == 'supplier') {} 
else if (auth()->user()->user_type == 'employee') {$position = App\Models\Employee::where('id', $userId)->first()->position;}
        ?>
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
        <script src="<?php echo e(asset('source/jquery/datatables.js')); ?>"></script>
        <script src="<?php echo e(asset('source/jquery/datatables.min.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/js/main2.js')); ?>"></script>
        <script src="<?php echo e(asset('js/logout.js')); ?>"></script>
        <?php echo $__env->yieldContent('scripts'); ?>
</body>

</html><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/layouts/app.blade.php ENDPATH**/ ?>