<?php $__env->startSection('title'); ?> Point of Sale System | Tinatangi Cafe <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-0">

        <div class="row align-items-stretch min-vh-90 g-2">

            <div class="col-12 col-md-9 d-flex">
                <div class="card w-100 h-100 d-flex flex-column">
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                    </div>
                    <div class="card-body flex-grow-1">
                        <div class="row h-100">
                            <div class="col-2 h-100">
                                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                    aria-orientation="vertical">
                                    <a class="nav-link active" id="v-pills-all-tab" data-bs-toggle="pill"
                                        href="#v-pills-all" role="tab" aria-controls="v-pills-all"
                                        aria-selected="true">All</a>
                                    <a class="nav-link" id="v-pills-pastries-tab" data-bs-toggle="pill"
                                        href="#v-pills-pastries" role="tab" aria-controls="v-pills-pastries"
                                        aria-selected="false">Pastries</a>
                                    <a class="nav-link" id="v-pills-beverages-tab" data-bs-toggle="pill"
                                        href="#v-pills-beverages" role="tab" aria-controls="v-pills-beverages"
                                        aria-selected="false">Beverages</a>
                                    <a class="nav-link" id="v-pills-meals-tab" data-bs-toggle="pill" href="#v-pills-meals"
                                        role="tab" aria-controls="v-pills-meals" aria-selected="false">Meals</a>
                                </div>
                            </div>
                            <div class="col-10 h-100">
                                <div class="tab-content h-100 overflow-auto" id="v-pills-tabContent">
                                    <div class="tab-pane fade show active" id="v-pills-all" role="tabpanel"
                                        aria-labelledby="v-pills-all-tab">

                                        <div class="tab-pane fade show active" id="v-pills-all" role="tabpanel"
                                            aria-labelledby="v-pills-all-tab">

                                            <div class="row row-cols-auto g-3 justify-content-start">

                                                <div class="col">
                                                    <div
                                                        class="card shadow product-card-fixed-size d-flex flex-column h-100 p-2 m-2">

                                                        <img src="<?php echo e(asset('img/coffee-tinatangilatte.png')); ?>"
                                                            class="card-img-top img-fluid prod-img" alt="Product Image">

                                                        <div class="card-body p-2 flex-grow-1">
                                                            <h6 class="card-title mb-1 prod-name">Espresso Delight Long
                                                                Name</h6>
                                                            <h6 class="text-success mb-0 prod-price">$3.50</h6>
                                                        </div>

                                                        <div class="card-footer p-1">
                                                            <button class="btn btn-sm btn-primary w-100">Add</button>
                                                        </div>

                                                    </div>
                                                </div>


                                            </div>
                                        </div>


                                    </div>
                                    <div class="tab-pane fade" id="v-pills-pastries" role="tabpanel"
                                        aria-labelledby="v-pills-pastries-tab">
                                        Integer interdum diam eleifend metus lacinia, quis gravida eros
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-beverages" role="tabpanel"
                                        aria-labelledby="v-pills-beverages-tab">
                                        Integer pretium dolor at sapien laoreet ultricies. Fusce congue et
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-meals" role="tabpanel"
                                        aria-labelledby="v-pills-meals-tab">
                                        Sed lacus quam, convallis quis condimentum ut, accumsan congue
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-3 d-flex">
                <div class="card w-100 h-100 d-flex flex-column">
                    <div class="card-header">
                        <h2 class="card-title">Order</h2>
                    </div>

                    <div class="card-body flex-grow-1 overflow-auto">
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">

                            <h6 class="card-title mb-3">
                                Total Cost:
                            </h6>

                            <h6 class="mb-3">
                                100.00
                            </h6>

                        </div>

                        <button class="btn btn-lg btn-primary w-100 -mt-2">
                            Complete Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .min-vh-90 {
            min-height: 90vh;
        }

        .product-card-fixed-size {
            height: 250px;
            width: 250px;
        }

        .product-card-fixed-size .card-img-top {
            height: 200px;
            object-fit: cover;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script type="module" src="<?php echo e(asset('js/pointOfSale.js')); ?>   "></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.modules-layouts.pos-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/operations/point-of-sales.blade.php ENDPATH**/ ?>