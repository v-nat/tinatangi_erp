<?php echo $__env->make('partials.inventory-heading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('inventoryProducts'); ?> active <?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> Products <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-first pt-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(route('inventory')); ?>">Inventory</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Products</li>
                    </ol>
                </nav>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-last float-end d-flex justify-content-end">
                <button id="addProductBtn" class="btn btn-primary">Add New Product</button>
            </div>
        </div>
        <section class="section mt-2">
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Products</h4>

                    <div class="d-flex align-items-center" style="width: 320px;">
                        <label for="category_filter" class="form-label mb-0 me-2 flex-shrink-0">Filter by Category:</label>
                        <select id="category_filter" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="products-table" class="table table-hover dataTable no-footer"
                            style="width:100% !important; table-layout:fixed">

                        </table>
                    </div>
                </div>
            </div>
        </section>

        <?php echo $__env->make('layouts.modals.inventory-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>
    <?php $__env->startSection('scripts'); ?>
        <script type="module" src="<?php echo e(asset('js/inventoryProducts.js')); ?>"></script>
        <script type="module" src="<?php echo e(asset('js/recipeManagement.js')); ?>"></script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/inventory/products.blade.php ENDPATH**/ ?>