
<?php $__env->startSection('title'); ?> Human Resources <?php $__env->stopSection(); ?>
<?php $__env->startSection('sidebar-title'); ?> Human Resources Management <?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> Leave Application <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Human Resources</a></li>
            <li class="breadcrumb-item active" aria-current="page">Leave</li>
        </ol>
    </nav>

    <section id="multiple-column-form">
        <div class="row match-height">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form" id="leaveApplication" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" id="employee_id" name="employee_id" value="<?php echo e($id); ?>">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="start_date">Start Date</label>
                                            <input type="date" id="start_date" class="form-control py-3" placeholder=""
                                                required name="start_date">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="end_date">End Date</label>
                                            <input type="date" id="end_date" class="form-control py-3" placeholder=""
                                                required name="end_date">
                                        </div>
                                    </div>
                                    <div id="reasonDiv" class="w-100 mb-10">
                                        <label for="reason" class="form-label">Reason</label>
                                        <select class="form-select py-3" id="reason" name="reason" required>
                                            <option value="" selected disabled>Select leave reason</option>
                                            <option value="Sick Leave">Sick Leave</option>
                                            <option value="Accident Leave">Accident Leave</option>
                                            <option value="Vacation Leave">Vacation Leave</option>
                                            <option value="Maternity Leave">Maternity Leave</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="w-100 mb-10" id="textAreaDiv">
                                        <label for="textAreaReason" class="form-label">Provide Reason</label>
                                        <textarea class="form-control" name="textAreaReason" id="textAreaReason"
                                            rows="3"></textarea>
                                    </div>

                                    <div class="d-flex justify-content-start">
                                        <button id="reqLeave" type="submit"
                                            class="btn icon icon-left btn-primary me-1 mb-1 w-100 py-3">
                                            <i class="fa-solid fa-paper-plane"></i>
                                            Submit Application</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="card">
            <div class="card-header">
                My Leave Requests
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="leaveRequests" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date Start</th>
                                <th>Date End</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>

    <script src="<?php echo e(asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js')); ?>   "></script>

    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>   "></script>
    
    
    <script src="<?php echo e(asset('assets/vendors/simple-datatables/simple-datatables.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/main2.js')); ?>"></script>
    <script src="<?php echo e(asset('js/leaveApplication.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/human_resources/leave-application.blade.php ENDPATH**/ ?>