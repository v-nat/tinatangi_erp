<?php echo $__env->make('partials.human-resources-heading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('emplMngt2'); ?><?php $__env->stopSection(); ?>
<?php $__env->startSection('appMngt'); ?>active
<?php $__env->stopSection(); ?>
<?php $__env->startSection('appMngt2'); ?>active
<?php $__env->stopSection(); ?>
<?php $__env->startSection('sbi4'); ?>active
<?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> Leave Approval <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('hr.employees')); ?>">Employee Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Leaves</li>
        </ol>
    </nav>
    <section class="section">
        <div class="card">
            <div class="card-header">
                Leaves Table
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="leaves_table" class="table table-hover dataTable no-footer" style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Date Start</th>
                                <th>Date End</th>
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

    <?php echo $__env->make('layouts.modals.hr-leave-mngmnt-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('js/leaveMngt.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/human_resources/leave-mngmnt.blade.php ENDPATH**/ ?>