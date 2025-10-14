<li class="sidebar-title"><?php echo $__env->yieldContent('inventory-admin'); ?></li>

<li class="sidebar-item <?php echo $__env->yieldContent('inventoryIndex'); ?> ">
    <a href="<?php echo e(route('inventory')); ?>" class='sidebar-link'>
        <?php echo $__env->yieldContent('inventory-dashboard'); ?>
    </a>
</li>
<li class="sidebar-item <?php echo $__env->yieldContent('inventoryItems'); ?> ">
    <a href="<?php echo e(route('inventory.all-items')); ?>" class='sidebar-link'>
        <i class="bi bi-archive-fill"></i>
        <span>All Items</span>
    </a>
</li>
<li class="sidebar-item <?php echo $__env->yieldContent('inventoryTransactions'); ?> ">
    <a href="<?php echo e(route('inventory.transactions')); ?>" class='sidebar-link'>
        <i class="bi bi-receipt"></i>
        <span>Stock Transactions</span>
    </a>
</li>
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/layouts/modules-sidebar/inventory-sidebar.blade.php ENDPATH**/ ?>