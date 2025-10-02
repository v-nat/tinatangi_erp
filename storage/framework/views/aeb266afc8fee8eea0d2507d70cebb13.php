<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="" class="justify-content-center align-items-center"><img style="height: 50px " src="<?php echo e(asset('tinatangilogo2 - Copy.png')); ?>  " alt="Logo"
                            srcset=""></a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title"><?php echo $__env->yieldContent('sidebar-title'); ?></li>

                <div class="<?php echo $__env->yieldContent('human_resources'); ?>">
                    <li class="sidebar-item <?php echo $__env->yieldContent('dsh'); ?> ">
                        <a href="<?php echo e(route('hr.dashboard')); ?>" class='sidebar-link'>
                            <i class="bi bi-grid-1x2-fill"></i>
                            <span>Dashboard</span>
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
                </div>

                <div class="<?php echo $__env->yieldContent('finance'); ?>">
                    <li class="sidebar-item <?php echo $__env->yieldContent('financePayroll'); ?> ">
                        <a href="<?php echo e(route('finance.payroll')); ?>" class='sidebar-link'>
                            <i class="bi bi-credit-card-2-front-fill"></i>
                            <span>Payroll Approvals</span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo $__env->yieldContent('financePurchases'); ?> ">
                        <a href="<?php echo e(route('finance.purchases')); ?>" class='sidebar-link'>
                            <i class="bi bi-bag-fill"></i>
                            <span>Purchase Order Approvals</span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo $__env->yieldContent('financeBudgets'); ?> ">
                        <a href="<?php echo e(route('finance.budgets')); ?>" class='sidebar-link'>
                            <i class="bi bi-cash"></i>
                            <span>Budget Releasing</span>
                        </a>
                    </li>
                </div>

                <div class="<?php echo $__env->yieldContent('procurement'); ?>">
                    <li class="sidebar-item <?php echo $__env->yieldContent('procurementIndex'); ?> ">
                        <a href="<?php echo e(route('procurement.index')); ?>" class='sidebar-link'>
                            <i class="bi bi-grid-1x2-fill"></i>
                            <span>Dashboard</span>
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
                </div>
            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>