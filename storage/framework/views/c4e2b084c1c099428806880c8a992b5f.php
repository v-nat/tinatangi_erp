<?php $__env->startSection('content'); ?>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="kdsOrders" class="display table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
                        
                    </table>
                </div>
            </div>
        </div>
    </section>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    
    <script src="<?php echo e(asset('source/laravel_echo/pusher.min.js')); ?>"></script>
    <script src="<?php echo e(asset('source/laravel_echo/echo.iife.js')); ?>"></script>
    <script>
        // Echo Configuration (Reads .env keys)
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '<?php echo e(env('PUSHER_APP_KEY')); ?>',
            cluster: '<?php echo e(env('PUSHER_APP_CLUSTER')); ?>',
            forceTLS: true
        });

        console.log('Laravel Echo environment configured.');
    </script>
    <script src="<?php echo e(asset('js/kitchenDisplay.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.modules-layouts.operations-screens-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/operations/kitchen-display.blade.php ENDPATH**/ ?>