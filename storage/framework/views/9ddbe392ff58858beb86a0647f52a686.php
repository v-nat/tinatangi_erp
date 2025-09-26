
<?php $__env->startSection('title'); ?> Procurement Management <?php $__env->stopSection(); ?>
<?php $__env->startSection('sidebar-title'); ?> Procurement Management <?php $__env->stopSection(); ?>
<?php $__env->startSection('human_resources'); ?> d-none <?php $__env->stopSection(); ?>
<?php $__env->startSection('finance'); ?> d-none <?php $__env->stopSection(); ?>
<?php $__env->startSection('procurement'); ?> d-block <?php $__env->stopSection(); ?>
<?php $__env->startSection('purchaseOrders'); ?> active <?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> Purchase Orders <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js')); ?>   "></script>
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>   "></script>
    
    <script src="<?php echo e(asset('assets/js/pages/dashboard.js')); ?>   "></script>

    <script src="<?php echo e(asset('assets/js/main2.js')); ?>   "></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/procurement/purchase-orders.blade.php ENDPATH**/ ?>