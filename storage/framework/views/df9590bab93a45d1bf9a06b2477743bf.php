<?php if(auth()->user()->employeeRS?->department == 1): ?>

    <?php $__env->startSection('title'); ?>Tinatangi Cafe ERP Management System <?php $__env->stopSection(); ?>
    <?php $__env->startSection('sidebar-title'); ?> <?php $__env->stopSection(); ?>
    <?php $__env->startSection('hr-admin'); ?> Human Resources Management <?php $__env->stopSection(); ?>
    <?php $__env->startSection('finance-admin'); ?> Finance and Accounting Management <?php $__env->stopSection(); ?>
    <?php $__env->startSection('procurement-admin'); ?> Procurement Management <?php $__env->stopSection(); ?>
    <?php $__env->startSection('inventory-admin'); ?> Inventory Management <?php $__env->stopSection(); ?>
    <?php $__env->startSection('operations-admin'); ?> Service Operations <?php $__env->stopSection(); ?>
    <?php $__env->startSection('human_resources'); ?> d-block <?php $__env->stopSection(); ?>
    <?php $__env->startSection('finance'); ?> d-block <?php $__env->stopSection(); ?>
    <?php $__env->startSection('procurement'); ?> d-block <?php $__env->stopSection(); ?>
    <?php $__env->startSection('inventory'); ?> d-block <?php $__env->stopSection(); ?>
    <?php $__env->startSection('operations'); ?> d-block <?php $__env->stopSection(); ?>
    <?php $__env->startSection('supplierPage'); ?> d-none <?php $__env->stopSection(); ?>
    <?php $__env->startSection('general_employee'); ?> d-none <?php $__env->stopSection(); ?>

    <?php $__env->startSection('human_resources-admin'); ?>
        <i class="bi bi-person-lines-fill"></i>
        <span>Human Resources Dashboard</span>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('finance-dashboard'); ?>
        <i class="fa-solid fa-chart-pie"></i>
        <span>Finance Dashboard</span>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('procurement-dashboard'); ?>
        <i class="fa-solid fa-cart-flatbed"></i>
        <span>Procurement Dashboard</span>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('inventory-dashboard'); ?>
        <i class="fa-solid fa-warehouse"></i>
        <span>Inventory Dashboard</span>
    <?php $__env->stopSection(); ?>

    <?php $__env->startSection('operations-dashboard'); ?>
        <i class="bi bi-grid-1x2-fill"></i>
        <span>Operations Dashboard</span>
    <?php $__env->stopSection(); ?>

<?php endif; ?>
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/partials/admin-heading.blade.php ENDPATH**/ ?>