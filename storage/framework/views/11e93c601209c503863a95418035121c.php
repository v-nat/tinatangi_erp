
<?php $__env->startSection('title'); ?> Human Resources <?php $__env->stopSection(); ?>
<?php $__env->startSection('sidebar-title'); ?> Human Resources Management <?php $__env->stopSection(); ?>
<?php $__env->startSection('dsh'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('emplMngt'); ?>active
<?php $__env->stopSection(); ?>
<?php $__env->startSection('emplMngt2'); ?>active
<?php $__env->stopSection(); ?>
<?php $__env->startSection('appMngt'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('appMngt2'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('sbi1'); ?>active
<?php $__env->stopSection(); ?>
<?php $__env->startSection('sbi2'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('sbi3'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('sbi4'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Employee List</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Employee Management</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Employees</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                Employee Table
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="employee_table" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Email</th>
                                <th>Reporting Manager</th>
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

    <!--primary theme Modal -->
    <div class="modal fade text-left" id="generatePayroll" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title white" id="myModalLabel160">
                        Generate Payroll
                    </h5>
                </div>
                <form id="payrollForm" method="POST" action="<?php echo e(route('hr.payroll.generate')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="text-left">
                            <p>You are about to generate payroll for:</p>
                            <ul class="list-unstyled">
                                <li><strong>Employee ID:</strong> <span id="empId"></span></li>
                                <li><strong>Name:</strong> <span id="empName"></span></li>
                            </ul>
                            <p class="text-info"><i class="fas fa-info-circle"></i> Please ensure the pay period dates are
                                correct.</p>
                        </div>
                        <div class="mb-3">
                            <input type="hidden" name="employee_id" id="modalEmployeeId" value="">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-danger" data-bs-dismiss="modal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Cancel</span>
                        </button>
                        <button type="submit" class="btn btn-primary ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Generate</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>

    <script src="<?php echo e(asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js')); ?>   "></script>

    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>   "></script>
    
    
    <script src="<?php echo e(asset('assets/vendors/simple-datatables/simple-datatables.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/main2.js')); ?>"></script>
    <script src="<?php echo e(asset('js/hrEmployees.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/human_resources/employees.blade.php ENDPATH**/ ?>