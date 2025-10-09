<?php echo $__env->make('partials.inventory-heading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('inventoryTransactions'); ?> active
<?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> Transaction Records <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('inventory')); ?>">Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Stock Transactions</li>
        </ol>
    </nav>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Transactions Table</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="allTransactions" class="table table-hover dataTable no-footer"
                        style="width:100% !important; table-layout:fixed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Batch</th>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Quantity</th>
                                <th>Item</th>
                                <th>Receive</th>
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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script type="module" src="<?php echo e(asset('js/stockTransactions.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/inventory/stock-transactions.blade.php ENDPATH**/ ?>