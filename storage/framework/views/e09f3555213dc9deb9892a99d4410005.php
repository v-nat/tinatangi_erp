<?php echo $__env->make('partials.human-resources-heading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('payroll'); ?> active <?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> Payroll List <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-title mb-2">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-first pt-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('hr.dashboard')); ?>">Human Resources</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Payroll</li>
                    </ol>
                </nav>
            </div>
            <?php if(\App\Http\Controllers\AuthController::checkAuthorization()): ?>
            <div class="col-12 col-md-6 order-md-2 order-last float-end d-flex justify-content-end">
                <button id="payrollSettingsBtn" class="btn btn-primary">Payroll Settings</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">

                <h4 class="card-title mb-0">Payroll Table</h4>

                <div class="d-flex align-items-center">
                    <div class="me-2">
                        <label for="departmentFilter" class="form-label mb-0 me-1">Department:</label>
                        <select id="departmentFilter" class="form-select form-select-sm" style="width: auto;">
                            <option value="">All</option>
                        </select>
                    </div>

                    <div>
                        <label for="periodFilter" class="form-label mb-0 me-1">Pay Period:</label>
                        <select id="periodFilter" class="form-select form-select-sm" style="width: auto;">
                            <option value="">All</option>
                        </select>
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
                                <th>Regular Pay</th>
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

    <?php echo $__env->make('layouts.modals.hr-payroll-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <style>
        .action-btns {
            display: flex;
            justify-content: center
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script type="module" src="<?php echo e(asset('js/hrPayroll.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/human_resources/payroll.blade.php ENDPATH**/ ?>