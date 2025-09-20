
<?php $__env->startSection('title'); ?> Human Resources <?php $__env->stopSection(); ?>
<?php $__env->startSection('sidebar-title'); ?> Human Resources Management <?php $__env->stopSection(); ?>
<?php $__env->startSection('dsh'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('emplMngt'); ?><?php $__env->stopSection(); ?>
<?php $__env->startSection('emplMngt2'); ?><?php $__env->stopSection(); ?>
<?php $__env->startSection('appMngt'); ?>active
<?php $__env->stopSection(); ?>
<?php $__env->startSection('appMngt2'); ?>active
<?php $__env->stopSection(); ?>
<?php $__env->startSection('sbi1'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('sbi2'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('sbi3'); ?>
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
                    <table class="table table-striped" id="leaves_table" style="width:100%">
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

    <!--warning theme Modal -->
    <div class="modal fade text-left" id="ApprovalConfirmation" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel120" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title white" id="myModalLabel120">
                        Leave Request Confirmation
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>
                        Are you sure you want to approve this Leave Request?
                    </h6>
                    <form id="approvalForm">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="leave_id" id="approvalLeaveId">
                        <div class="form-group">
                            <label for="approvalNotes">Approval Notes (Optional)</label>
                            <textarea class="form-control" id="approvalNotes" name="reason" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>

                    <button id="approve-btn-confirmed" type="button" class="btn btn-warning ml-1" data-bs-dismiss="modal">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Approve</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!--Danger theme Modal -->
    <div class="modal fade text-left" id="RejectionConfirmation" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel120" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title white" id="myModalLabel120">
                        Leave Request Rejection
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>You are about to Reject this Leave Request</h6>
                    <form id="rejectionForm">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="leave_id" id="rejectionLeaveId">
                        <div class="form-group">
                            <label for="rejectionNotes">Rejection Notes (Optional)</label>
                            <textarea class="form-control" id="rejectionNotes" name="reason" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Cancel</span>
                    </button>
                    <button id="reject-btn-confirmed" type="button" class="btn btn-danger ml-1" data-bs-dismiss="modal">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Reject</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>   "></script>

    
    <script src="<?php echo e(asset('assets/js/pages/dashboard.js')); ?>   "></script>

    <script src="<?php echo e(asset('assets/js/main2.js')); ?>   "></script>
    <script src="<?php echo e(asset('js/leaveMngt.js')); ?>"></script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/human_resources/leave-app.blade.php ENDPATH**/ ?>