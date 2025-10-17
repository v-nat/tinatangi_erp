<li class="sidebar-title"><?php echo $__env->yieldContent('finance-admin'); ?></li>

<li class="sidebar-item <?php echo $__env->yieldContent('financeIndex'); ?> ">
    <a href="<?php echo e(route('finance')); ?>" class='sidebar-link'>
        <?php echo $__env->yieldContent('finance-dashboard'); ?>
    </a>
</li>
<li class="sidebar-item <?php echo $__env->yieldContent('financePayroll'); ?> ">
    <a href="<?php echo e(route('finance.payroll')); ?>" class='sidebar-link'>
        <i class="bi bi-credit-card-2-front-fill"></i>
        <span>Payroll Approvals</span>
    </a>
</li>
<li class="sidebar-item <?php echo $__env->yieldContent('financePurchases'); ?> ">
    <a href="<?php echo e(route('finance.purchases')); ?>" class='sidebar-link'>
        <i class="fa-solid fa-clipboard-check"></i>
        <span>Purchase Order Approvals</span>
    </a>
</li>
<li class="sidebar-item <?php echo $__env->yieldContent('financeBudgets'); ?> ">
    <a href="<?php echo e(route('finance.budgets')); ?>" class='sidebar-link'>
        <i class="fa-solid fa-money-check-dollar"></i>
        <span>Budget Releasing</span>
    </a>
</li>
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/layouts/modules-sidebar/finance-sidebar.blade.php ENDPATH**/ ?>