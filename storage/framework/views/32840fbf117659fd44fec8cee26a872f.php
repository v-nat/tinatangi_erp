<li class="sidebar-title"><?php echo $__env->yieldContent('hr-admin'); ?></li>

<li class="sidebar-item <?php echo $__env->yieldContent('dsh'); ?> ">
    <a href="<?php echo e(route('hr.dashboard')); ?>" class='sidebar-link'>
        <?php echo $__env->yieldContent('human_resources-admin'); ?>
    </a>
</li>
<li class="sidebar-item <?php echo $__env->yieldContent('emplMngt'); ?> has-sub">
    <a href=" " class='sidebar-link '>
        <i class="bi bi-people-fill"></i>
        <span>Employee Management</span>
    </a>
    <ul class="submenu <?php echo $__env->yieldContent('emplMngt2'); ?>">
        <li class="submenu-item <?php echo $__env->yieldContent('sbi1'); ?> ">
            <a href="<?php echo e(route('hr.employees')); ?>">Employee List</a>
        </li>
        <li class="submenu-item <?php echo $__env->yieldContent('sbi2'); ?> ">
            <a href="<?php echo e(route('hr.manage')); ?>">Manage Emloyee</a>
        </li>
    </ul>
</li>
<li class="sidebar-item <?php echo $__env->yieldContent('appMngt'); ?> has-sub">
    <a href=" " class='sidebar-link '>
        <i class="bi bi-person-check-fill"></i>
        <span>Approval Management</span>
    </a>
    <ul class="submenu <?php echo $__env->yieldContent('appMngt2'); ?>">
        <li class="submenu-item <?php echo $__env->yieldContent('sbi3'); ?> ">
            <a href="<?php echo e(route('hr.ot-app')); ?>">Overtime Approvals</a>
        </li>
        <li class="submenu-item <?php echo $__env->yieldContent('sbi4'); ?> ">
            <a href="<?php echo e(route('hr.leave-app')); ?>">Leave Approvals</a>
        </li>
    </ul>
</li>
<li class="sidebar-item <?php echo $__env->yieldContent('payroll'); ?> ">
    <a href="<?php echo e(route('hr.payroll')); ?>" class='sidebar-link'>
        <i class="bi bi-credit-card-2-front-fill"></i>
        <span>Payroll</span>
    </a>
</li>
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/layouts/modules-sidebar/hr-sidebar.blade.php ENDPATH**/ ?>