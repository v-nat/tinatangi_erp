<?php $__env->startSection('title'); ?> Point of Sale System | Tinatangi Cafe <?php $__env->stopSection(); ?>
<?php $__env->startSection('topTitle'); ?> Tinatangi Cafe | Point of Sale <?php $__env->stopSection(); ?>
<?php $__env->startSection('screen'); ?> POS <?php $__env->stopSection(); ?>
<?php $__env->startSection('posTopNav'); ?> active <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-0">

        <div class="row align-items-center min-vh-90 g-2 justify-content-center">

            <div class="col-12 col-md-9 d-flex">
                <div class="card w-100">
                    <div class="card-header">
                        <h4 class="card-title"></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-2">
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
                                    <a class="nav-link" id="v-pills-snacks-tab" data-bs-toggle="pill"
                                        href="#v-pills-snacks" role="tab" aria-controls="v-pills-snacks"
                                        aria-selected="false">Snacks & Sides</a>
                                </div>
                            </div>
                            <div class="col-10 overflow-y-auto vh-80">
                                <div class="tab-content" id="v-pills-tabContent">
                                    <div class="tab-pane fade show active" id="v-pills-all" role="tabpanel"
                                        aria-labelledby="v-pills-all-tab">

                                        <div class="tab-pane fade show active py-4" id="v-pills-all" role="tabpanel"
                                            aria-labelledby="v-pills-all-tab">

                                            <div id="allProducts" class="row row-cols-auto g-3 justify-content-start">

                                                

                                            </div>

                                        </div>
                                    </div>
                                    <div class="tab-pane fade py-4" id="v-pills-pastries" role="tabpanel"
                                        aria-labelledby="v-pills-pastries-tab">

                                        <div id="pastriesProducts" class="row row-cols-auto g-3 justify-content-start">

                                            

                                        </div>
                                    </div>
                                    <div class="tab-pane fade py-4" id="v-pills-beverages" role="tabpanel"
                                        aria-labelledby="v-pills-beverages-tab">

                                        <div id="beveragesProducts" class="row row-cols-auto g-3 justify-content-start">

                                            

                                        </div>
                                    </div>
                                    <div class="tab-pane fade py-4" id="v-pills-meals" role="tabpanel"
                                        aria-labelledby="v-pills-meals-tab">

                                        <div id="mealsProducts" class="row row-cols-auto g-3 justify-content-start">

                                            

                                        </div>
                                    </div>

                                    <div class="tab-pane fade py-4" id="v-pills-snacks" role="tabpanel"
                                        aria-labelledby="v-pills-snacks-tab">

                                        <div id="snacksProducts" class="row row-cols-auto g-3 justify-content-start">
                                            

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-2 d-flex overflow-y-auto vh-90">
                <div class="card w-100">
                    <div class="card-header">
                        <h2 class="card-title">Order</h2>
                    </div>

                    <div id="orderList" class="card-body overflow-y-auto">

                        

                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title mb-3">Total Cost:</h6>
                            <h6 class="mb-3" id="order-total-amount">₱ 0.00</h6>
                        </div>

                        <button id="submit-order-btn" class="btn btn-lg btn-primary w-100 -mt-2">
                            Complete Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $__env->make('layouts.modals.operations-pos-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <style>
        .col-md-2 {
            width: 20%;
        }
        .min-vh-90 {
            min-height: 90vh;
        }

        .vh-90 {
            height: 89vh;
        }

        .vh-80 {
            height: 77vh;
        }

        .product-card-fixed-size {
            height: 250px;
            width: 250px;
        }

        .product-card-fixed-size .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .product-card-fixed-size .prod-price {
            margin-top: auto;
            font-size: 1rem;
            font-weight: 700;
        }

        .product-card-fixed-size .prod-name {
            font-size: 0.9rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {

            .product-card-fixed-size {
                height: 220px;
                width: 100%;
            }

            .product-card-fixed-size .card-img-top {
                height: 150px;
            }

            .prod-name {
                font-size: 12px
            }
        }

        @media (max-width: 1080px) {

            .product-card-fixed-size {
                height: 170px;
                width: 170px;
            }

            .product-card-fixed-size .card-img-top {
                height: 120px;
            }

            .prod-name {
                font-size: 14px
            }
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script>
        const DEFAULT_PRODUCT_IMAGE = "<?php echo e(asset('img/default-product.png')); ?>";
    </script>
    <script type="module" src="<?php echo e(asset('js/pointOfSale.js')); ?>   "></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.modules-layouts.operations-screens-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/operations/point-of-sales.blade.php ENDPATH**/ ?>