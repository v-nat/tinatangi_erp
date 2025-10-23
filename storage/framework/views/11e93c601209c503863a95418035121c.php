<?php echo $__env->make('partials.human-resources-heading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('emplMngt'); ?>active <?php $__env->stopSection(); ?>
<?php $__env->startSection('emplMngt2'); ?>active <?php $__env->stopSection(); ?>
<?php $__env->startSection('sbi1'); ?>active <?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> Employee List <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('hr.dashboard')); ?>">Human Resources</a></li>
            <li class="breadcrumb-item active" aria-current="page">Employees</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Employee List</h4>

                <div class="d-flex align-items-center" style="width: 320px;">
                  <label for="department_filter" class="form-label mb-0 me-2 flex-shrink-0">Filter by Department:</label>
                  <select id="department_filter" class="form-select form-select-sm">
                    <option value="">All Departments</option>

                  </select>
                </div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table id="employee_table" class="table table-hover dataTable no-footer" style="width:100% !important; table-layout:fixed">
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

    <?php echo $__env->make('layouts.modals.hr-employees-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        .action-btns {
            display: flex;
            justify-content: center
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script type="module" src="<?php echo e(asset('js/hrEmployees.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/human_resources/employees.blade.php ENDPATH**/ ?>