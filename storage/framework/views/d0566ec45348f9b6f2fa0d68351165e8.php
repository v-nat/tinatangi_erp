<div class="modal fade text-left w-100" id="employeeAttendance" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel20" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel20">Employee Attendance</h4>
            </div>
            <div class="modal-body p-4">
                <div class="card">
                    <div class="card-body">
                        <div class="card-body text-center">
                            <h1 id="realtimeClock" class="text-muted mb-3 fs-1 fw-bold"></h1>
                            <form id="attendanceForm">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="employee_id" id="employeeIdInput"
                                    value="<?php echo e(Auth::user()->id ?? ''); ?>">

                                <div class="row d-block mb-3">
                                    <div class="col-12 w-100">
                                        <button type="button" class="btn btn-lg btn-success col-12" id="timeInBtn">Time
                                            In</button>
                                    </div>
                                    <div class="col-12 w-100 mt-1">
                                        <button type="button" class="btn btn-lg btn-danger col-12"
                                            id="timeOutBtn">TimeOut</button>
                                    </div>
                                </div>

                                <p class="mt-4 fs-5">
                                    <span class="d-block d-md-inline me-md-5">
                                        Time In: <span id="timeInDisplay" class="text-success">--:--:--</span>
                                    </span>

                                    <span class="d-block d-md-inline">
                                        Time Out: <span id="timeOutDisplay" class="text-danger">--:--:--</span>
                                    </span>
                                </p>

                                <p class="text-muted fs-6 mt-2">
                                    Minutes Today: <span id="totalHours" class="text-primary">0 minutes</span>
                                </p>
                            </form>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="<?php echo e(route('employee.attendance.list', ['id' => Auth::user()->id])); ?>" class="self-center">My Attendance Records</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 576px) {
        #realtimeClock {
            font-size: 2rem;
            text-align: center;
        }

        .time-record {
            font-size: 16px;
            text-align: center;
        }

        .btn-lg {
            width: 100%;
            margin-bottom: 10px;
        }

        .card {
            padding: 15px;
            margin-bottom: 1rem;
        }

        .card-header {
            padding: 0.75rem 1rem;
            text-align: center;
        }
    }
</style>
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/layouts/modals/attendance-modal.blade.php ENDPATH**/ ?>