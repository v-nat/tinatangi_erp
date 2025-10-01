<?php $__env->startSection('title'); ?> Procurement Management <?php $__env->stopSection(); ?>
<?php $__env->startSection('sidebar-title'); ?> Procurement Management <?php $__env->stopSection(); ?>
<?php $__env->startSection('human_resources'); ?> d-none <?php $__env->stopSection(); ?>
<?php $__env->startSection('finance'); ?> d-none <?php $__env->stopSection(); ?>
<?php $__env->startSection('procurement'); ?> d-block <?php $__env->stopSection(); ?>
<?php $__env->startSection('purchaseOrders'); ?> active <?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> Purchase Orders <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('procurement.index')); ?>">Procurement</a></li>
            <li class="breadcrumb-item active" aria-current="page">Purchase Orders</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Purchase  Table</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="purchaseOrderTable" class="table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order ID Number</th>
                                <th>Type</th>
                                <th>Order Ddate</th>
                                <th>Supplier</th>
                                <th>Expected Date</th>
                                <th>Delivery Date</th>
                                <th>Delivery Name</th>
                                <th>Created by</th>
                                <th>Remarks</th>
                                <th>Status</th>
                                <th>Action</th>
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

<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js')); ?>   "></script>
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>   "></script>
    
    <script src="<?php echo e(asset('assets/js/pages/dashboard.js')); ?>   "></script>

    <script src="<?php echo e(asset('assets/js/main2.js')); ?>   "></script>
    <script src="<?php echo e(asset('js/purchaseOrders.js')); ?>   "></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/procurement/purchase-orders.blade.php ENDPATH**/ ?>