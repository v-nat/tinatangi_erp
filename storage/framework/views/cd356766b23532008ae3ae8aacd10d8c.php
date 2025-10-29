<!--primary theme Modal -->
<div class="modal fade text-left" id="generatePayroll" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
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
                    <button type="submit" class="btn btn-info ml-1">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block">Generate</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/layouts/modals/hr-employees-modal.blade.php ENDPATH**/ ?>