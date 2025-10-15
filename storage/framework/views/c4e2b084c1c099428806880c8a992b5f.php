<?php $__env->startSection('title'); ?> Kitchen Display System | Tinatangi Cafe <?php $__env->stopSection(); ?>
<?php $__env->startSection('topTitle'); ?> Tinatangi Cafe | Kitchen Display <?php $__env->stopSection(); ?>
<?php $__env->startSection('screen'); ?> KDS <?php $__env->stopSection(); ?>
<?php $__env->startSection('posTopNav'); ?> d-none <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script type="module" src="<?php echo e(asset('js/kitchenDisplay.js')); ?>   "></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.modules-layouts.operations-screens-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/operations/kitchen-display.blade.php ENDPATH**/ ?>