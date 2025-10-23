<?php echo $__env->make('partials.procurement-heading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('createPR'); ?> active <?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> Purchase Request <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="page-title mb-4">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-first pt-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('procurement.index')); ?>">Procurement</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Purchase Requests</li>
                    </ol>
                </nav>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-last float-end d-flex justify-content-end">
                <button id="createPR" class="btn btn-primary">Create Purchase Request</button>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="card-title mb-3">Purchase Requests</h4>
                    <div class="d-flex align-items-center"  style="width: 320px;">
                        <label for="status_filter" class="form-label mb-0 me-2 flex-shrink-0">Filter by Status:</label>
                        <select id="status_filter" class="form-select form-select-sm">
                            <option value="">All Statuses</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label for="pr_type_filter" class="form-label mb-1">Filter by Type:</label>
                        <select id="pr_type_filter" class="form-select form-select-sm">
                            <option value="">All Types</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="pr_order_date_filter" class="form-label mb-1">Filter by Order Date:</label>
                        <input type="date" id="pr_order_date_filter" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label for="pr_supplier_filter" class="form-label mb-1">Filter by Supplier:</label>
                        <select id="pr_supplier_filter" class="form-select form-select-sm">
                            <option value="">All Suppliers</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="purchaseRequestTable" class="table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order No.</th>
                                <th>Type</th>
                                <th>Order Ddate</th>
                                <th>Supplier</th>
                                
                                <th>Delivery Date</th>
                                
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

    <?php echo $__env->make('layouts.modals.procurement-purchase-request-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('layouts.modals.invoice-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script type="module" src="<?php echo e(asset('js/createPurchaseRequest.js')); ?>   "></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/procurement/create-purchase-request.blade.php ENDPATH**/ ?>