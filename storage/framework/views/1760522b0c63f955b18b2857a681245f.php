<li class="sidebar-title"><?php echo $__env->yieldContent('operations-admin'); ?></li>

<li class="sidebar-item <?php echo $__env->yieldContent('operationsIndex'); ?> ">
    <a href="<?php echo e(route('op.dashboard')); ?>" class='sidebar-link'>
        <?php echo $__env->yieldContent('operations-dashboard'); ?>
    </a>
</li>

<li class="sidebar-item <?php echo $__env->yieldContent('operationsPOS'); ?> ">
    <a href="<?php echo e(route('op.pos')); ?>" class='sidebar-link'>
        <i class="fa-solid fa-desktop"></i>
        <span>Point of Sales</span>
    </a>
</li>
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/layouts/modules-sidebar/operations-sidebar.blade.php ENDPATH**/ ?>