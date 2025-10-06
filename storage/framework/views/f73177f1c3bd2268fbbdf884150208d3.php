<?php echo $__env->make('partials.inventory-heading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('inventoryItems'); ?> active
<?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> Inventory <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('inventory')); ?>">Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">All Items</li>
        </ol>
    </nav>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/inventory/all-items.blade.php ENDPATH**/ ?>