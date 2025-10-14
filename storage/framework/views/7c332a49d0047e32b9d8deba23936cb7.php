<li class="sidebar-title"><?php echo $__env->yieldContent('procurement-admin'); ?></li>

<li class="sidebar-item <?php echo $__env->yieldContent('procurementIndex'); ?> ">
    <a href="<?php echo e(route('procurement.index')); ?>" class='sidebar-link'>
        <?php echo $__env->yieldContent('procurement-dashboard'); ?>
    </a>
</li>
<li class="sidebar-item <?php echo $__env->yieldContent('createPR'); ?> ">
    <a href="<?php echo e(route('procurement.createPR')); ?>" class='sidebar-link'>
        <i class="bi bi-receipt-cutoff"></i>
        <span>Purchase Request</span>
    </a>
</li>
<li class="sidebar-item <?php echo $__env->yieldContent('purchaseOrders'); ?> ">
    <a href="<?php echo e(route('procurement.purchaseOrders')); ?>" class='sidebar-link'>
        <i class="bi bi-cart"></i>
        <span>Purchase Orders</span>
    </a>
</li>
<li class="sidebar-item <?php echo $__env->yieldContent('supplier'); ?> ">
    <a href="<?php echo e(route('procurement.supplier')); ?>" class='sidebar-link'>
        <i class="bi bi-truck"></i>
        <span>Suppliers</span>
    </a>
</li>
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/layouts/modules-sidebar/procurement-sidebar.blade.php ENDPATH**/ ?>