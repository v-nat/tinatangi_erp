
<?php $__env->startSection('title'); ?> Human Resources Dashboard <?php $__env->stopSection(); ?>
<?php $__env->startSection('sidebar-title'); ?> Human Resources Management <?php $__env->stopSection(); ?>
<?php $__env->startSection('dsh'); ?> active <?php $__env->stopSection(); ?>
<?php $__env->startSection('emplMngt'); ?><?php $__env->stopSection(); ?>
<?php $__env->startSection('appMngt'); ?><?php $__env->stopSection(); ?>
<?php $__env->startSection('sbi1'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('sbi2'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('sbi3'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('sbi4'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> Human Resources Dashboard <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('hr.dashboard')); ?>">Human Resources</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
    <section class="section">
        <div class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon purple">
                                    <i class="iconly-boldShow"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Profile Views</h6>
                                <h6 class="font-extrabold mb-0">112.000</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon blue">
                                    <i class="iconly-boldProfile"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Total Employees</h6>
                                <h6 class="font-extrabold mb-0">183.000</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon green">
                                    <i class="iconly-boldAdd-User"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">New Hires</h6>
                                <h6 class="font-extrabold mb-0">80.000</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon red">
                                    <i class="iconly-boldBookmark"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Saved Post</h6>
                                <h6 class="font-extrabold mb-0">112</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="card-body text-center">
                    <h1 id="realtimeClock" class="text-muted mb-3"></h1>

                    <form id="attendanceForm">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="employee_id" id="employeeIdInput" value="<?php echo e(Auth::user()->id ?? ''); ?>">

                        <div class="d-flex justify-content-between mb-3">
                            <button type="button" class="btn btn-success btn-lg" id="timeInBtn">Time In</button>
                            <button type="button" class="btn btn-danger btn-lg" id="timeOutBtn">Time Out</button>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 px-3">
                            <p class="time-record">
                                Time In: <span id="timeInDisplay" class="text-success">--:--:--</span>
                            </p>
                            <p class="time-record">
                                Time Out: <span id="timeOutDisplay" class="text-danger">--:--:--</span>
                            </p>
                        </div>

                        <p class="time-record mt-2">
                            Minutes Rendered Today: <span id="totalHours" class="text-primary">0 minutes</span>
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Employees Attendance</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="attendanceTable" class="table table-striped table-hover" style="width:100%">
                        <thead >
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>Total Minutes</th>
                                <th>Overtime Minutes</th>
                                <th>Tardiness Minutes</th>
                                <th>Leave Date</th>
                                <th>Status</th>
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
    <style>
        #realtimeClock {
            font-size: 3rem;
            font-weight: bold;
            color:
                #007bff;
        }

        .time-record {
            font-size: 18px;
            font-weight: bold;
        }

        .btn-lg {
            width: 48%;
        }

        .card {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .card-header {
            padding: 1rem 1.25rem;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js')); ?>   "></script>
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>   "></script>
    
    <script src="<?php echo e(asset('assets/js/pages/dashboard.js')); ?>   "></script>

    <script src="<?php echo e(asset('assets/js/main2.js')); ?>   "></script>
    <script src="<?php echo e(asset('js/employeeAttendance.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/human_resources/dashboard.blade.php ENDPATH**/ ?>