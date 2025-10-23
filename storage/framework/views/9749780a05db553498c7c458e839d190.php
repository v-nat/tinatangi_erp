<?php echo $__env->make('partials.finance-accounting-heading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('financePayroll'); ?> active <?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> Payroll List <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('finance.payroll')); ?>">Finance</a></li>
            <li class="breadcrumb-item active" aria-current="page">Payroll</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Payroll Table</h4>

                    <div class="d-flex align-items-center">
                        <div class="me-2">
                            <label for="payroll_department_filter" class="form-label mb-1">Department:</label>
                            <select id="payroll_department_filter" class="form-select form-select-sm" style="width: 200px;">
                                <option value="">All Departments</option>
                                <!-- Auto-populated -->
                            </select>
                        </div>

                        <div>
                            <label for="payroll_period_filter" class="form-label mb-1">Pay Period:</label>
                            <select id="payroll_period_filter" class="form-select form-select-sm" style="width: 200px;">
                                <option value="">All Periods</option>
                                <!-- Auto-populated -->
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="payrollsTable" class="table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Pay Period</th>
                                <th>Gross Pay</th>
                                <th>Deductions</th>
                                <th>Net Pay</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <?php echo $__env->make('layouts.modals.finance-payroll-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <style>
        .action-btns {
            display: flex;
            justify-content: center
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script type="module" src="<?php echo e(asset('js/financePayroll.js')); ?>   "></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/finance/finance-payroll.blade.php ENDPATH**/ ?>