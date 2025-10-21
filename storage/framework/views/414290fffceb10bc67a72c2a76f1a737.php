<?php echo $__env->make('partials.human-resources-heading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('appMngt'); ?>active <?php $__env->stopSection(); ?>
<?php $__env->startSection('appMngt2'); ?>active <?php $__env->stopSection(); ?>
<?php $__env->startSection('sbi3'); ?>active <?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> Overtime Approval <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('hr.employees')); ?>">Employee Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Overtimes</li>
        </ol>
    </nav>
    <section class="section">
        <div class="card">
            <div class="card-header">
                Overtimes Table
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="overtime_table" class="table table-hover dataTable no-footer" style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Date</th>
                                <th>Time Start</th>
                                <th>Time End</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <?php echo $__env->make('layouts.modals.hr-ot-mngmnt-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script type="module" src="<?php echo e(asset('js/overtimeMngt.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/human_resources/ot-mngmnt.blade.php ENDPATH**/ ?>