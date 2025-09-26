
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
                Supplier Table
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="supplier_table">
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

    <div class="modal fade text-left" id="addSupplier" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title white" id="myModalLabel160">
                        Add Supplier
                    </h5>
                </div>
                <form id="supplierForm" method="POST" action="">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <div class="col-md-12 col-12">
                            <div class="form-group">
                                <label for="supplier_name">Supplier Name</label>
                                <input type="input" id="supplier_name" class="form-control" required
                                    placeholder="Enter Name" name="supplier_name">
                                <div class="invalid-feedback">Supplier Name is required.</div>
                            </div>
                        </div>
                        <div class="col-md-12 col-12">
                            <div class="form-group">
                                <label for="email">Supplier Email</label>
                                <input type="email" id="email" class="form-control" required placeholder="example@mail.com"
                                    name="email">
                                <div class="invalid-feedback">Supplier Email is required.</div>
                            </div>
                        </div>
                        <div class="col-md-12 col-12">
                            <div class="form-group">
                                <label for="phone_number">Phone Number</label>
                                <input type="tel" id="phone_number" class="form-control" name="phone_number"
                                    pattern="^(09|\+639)\d{9}$" maxlength="11" minlength="11" required
                                    placeholder="09123456789" value="">
                                <div class="invalid-feedback">Phone Number is required.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="cancel" class="btn btn-light-danger" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button id="submitBtn" type="submit" class="btn btn-primary ml-1">Add Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js')); ?>   "></script>
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>   "></script>
    <script src="<?php echo e(asset('assets/js/pages/dashboard.js')); ?>   "></script>

    <script src="<?php echo e(asset('assets/js/main2.js')); ?>   "></script>
    <script src="<?php echo e(asset('js/manageSupplier.js')); ?>   "></script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/procurement/manage-supplier.blade.php ENDPATH**/ ?>