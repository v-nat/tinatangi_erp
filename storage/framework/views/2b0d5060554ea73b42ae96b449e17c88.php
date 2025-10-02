<?php $__env->startSection('title'); ?> Procurement Management <?php $__env->stopSection(); ?>
<?php $__env->startSection('sidebar-title'); ?> Procurement Management <?php $__env->stopSection(); ?>
<?php $__env->startSection('human_resources'); ?> d-none <?php $__env->stopSection(); ?>
<?php $__env->startSection('finance'); ?> d-none <?php $__env->stopSection(); ?>
<?php $__env->startSection('procurement'); ?> d-block <?php $__env->stopSection(); ?>
<?php $__env->startSection('supplier'); ?> active <?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> Supplier List <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-first pt-3">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-start">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Procurement</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Suppliers</li>
                    </ol>
                </nav>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-last float-end d-flex justify-content-end">
                <button id="add_supplier" class="btn btn-lg btn-primary">Add Supplier</button>
            </div>
        </div>
    </div>

    <section class="section mt-3">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Supplier  Table</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover dataTable no-footer" style="width:100% !important; table-layout:fixed" id="supplier_table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Supplier</th>
                                <th>Email Address</th>
                                <th>Phone Number</th>
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

    <?php echo $__env->make('layouts.modals.procurement-mng-supplier-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('js/manageSupplier.js')); ?>   "></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/procurement/manage-supplier.blade.php ENDPATH**/ ?>