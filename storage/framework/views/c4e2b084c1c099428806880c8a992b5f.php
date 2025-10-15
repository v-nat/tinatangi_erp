<?php $__env->startSection('title'); ?> Kitchen Display System | Tinatangi Cafe <?php $__env->stopSection(); ?>
<?php $__env->startSection('topTitle'); ?> Tinatangi Cafe | Kitchen Display <?php $__env->stopSection(); ?>
<?php $__env->startSection('screen'); ?> KDS <?php $__env->stopSection(); ?>
<?php $__env->startSection('posTopNav'); ?> d-none <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <section class="section p-4">
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
    <script src="<?php echo e(asset('js/kitchenDisplay.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.modules-layouts.operations-screens-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/operations/kitchen-display.blade.php ENDPATH**/ ?>