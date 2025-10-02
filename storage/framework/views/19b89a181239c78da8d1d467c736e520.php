<?php $__env->startSection('title'); ?> Finance and Accounting Management <?php $__env->stopSection(); ?>
<?php $__env->startSection('sidebar-title'); ?> Finance and Accounting Management <?php $__env->stopSection(); ?>
<?php $__env->startSection('human_resources'); ?> d-none <?php $__env->stopSection(); ?>
<?php $__env->startSection('finance'); ?> d-block <?php $__env->stopSection(); ?>
<?php $__env->startSection('procurement'); ?> d-none <?php $__env->stopSection(); ?>
<?php $__env->startSection('financeBudgets'); ?> active
<?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> Budget Releasing <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('finance.payroll')); ?>">Finance</a></li>
            <li class="breadcrumb-item active" aria-current="page">Budgets</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Approved Requests - Awaiting Budget Release</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="approvalTable" class="table table-hover dataTable no-footer" style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Requested by</th>
                                <th>Requested at</th>
                                <th>Department</th>
                                <th>Notes</th>
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
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Released Budget History</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="historyTable" class="table table-hover dataTable no-footer" style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Release ID</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Requested by</th>
                                <th>Requested at</th>
                                <th>Department</th>
                                <th>Released by</th>
                                <th>Released at</th>
                                <th class="no-print">Status</th>
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

    <?php echo $__env->make('layouts.modals.finance-budgets-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('js/budgetRelease.js')); ?>   "></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/finance/budget-releasing.blade.php ENDPATH**/ ?>