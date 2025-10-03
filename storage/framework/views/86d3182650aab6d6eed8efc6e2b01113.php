<?php if(auth()->user()->employeeRS?->department == 1): ?>

    <?php $__env->startSection('title'); ?>Tinatangi Cafe ERP Management System <?php $__env->stopSection(); ?>
    <?php $__env->startSection('sidebar-title'); ?> <?php $__env->stopSection(); ?>
    <?php $__env->startSection('hr-admin'); ?> Human Resources Management <?php $__env->stopSection(); ?>
    <?php $__env->startSection('finance-admin'); ?> Finance and Accounting Management <?php $__env->stopSection(); ?>
    <?php $__env->startSection('procurement-admin'); ?> Procurement Management <?php $__env->stopSection(); ?>
    <?php $__env->startSection('human_resources'); ?> d-block <?php $__env->stopSection(); ?>
    <?php $__env->startSection('finance'); ?> d-block <?php $__env->stopSection(); ?>
    <?php $__env->startSection('procurement'); ?> d-block <?php $__env->stopSection(); ?>
    <?php $__env->startSection('supplierPage'); ?> d-none <?php $__env->stopSection(); ?>
   
    <?php $__env->startSection('human_resources-admin'); ?>
        <i class="bi bi-person-lines-fill"></i>
        <span>Human Resources Dashboard</span>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('procurement-dashboard'); ?>
        <i class="bi bi-shop"></i>
        <span>Procurement Dashboard</span>
    <?php $__env->stopSection(); ?>
<?php endif; ?>

<?php $__env->startSection('title'); ?> Finance and Accounting Management <?php $__env->stopSection(); ?>
<?php $__env->startSection('sidebar-title'); ?> Finance and Accounting Management <?php $__env->stopSection(); ?>
<?php $__env->startSection('human_resources'); ?> d-none <?php $__env->stopSection(); ?>
<?php $__env->startSection('finance'); ?> d-block <?php $__env->stopSection(); ?>
<?php $__env->startSection('procurement'); ?> d-none <?php $__env->stopSection(); ?>
<?php $__env->startSection('supplierPage'); ?> d-none <?php $__env->stopSection(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/partials/finance-accounting-heading.blade.php ENDPATH**/ ?>