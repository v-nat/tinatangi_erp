<?php $__env->startSection('title'); ?> Kitchen Display System | Tinatangi Cafe <?php $__env->stopSection(); ?>
<?php $__env->startSection('topTitle'); ?> Tinatangi Cafe | Kitchen Display <?php $__env->stopSection(); ?>
<?php $__env->startSection('screen'); ?> KDS <?php $__env->stopSection(); ?>
<?php $__env->startSection('posTopNav'); ?> d-none <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

    <div class="row align-items-center min-vh-90 g-2 justify-content-center">

        <div class="card col-11 overflow-y-auto vh-80 p-4 ps-5">

            <div id="ordersToday" class="row row-cols-auto g-3 justify-content-start">

                

            </div>
        </div>
    </div>

    <style>
        .product-card-fixed-size .ord-items-container {
            max-height: 79px;
            overflow-y: auto;
            margin-bottom: 5px;
        }

        .card {
            transition: background-color 1s ease-in-out;
        }

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
            height: 300px;
            width: 300px;
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

            h6 {
                font-size: 12px
            }
        }

        @media (max-width: 1080px) {

            .product-card-fixed-size {
                height: 250px;
                width: 250px;
            }

            .product-card-fixed-size .ord-items-container {
                min-height: 49px;
                max-height: 49px;
                overflow-y: auto;
                margin-bottom: 5px;
            }

            h6 {
                font-size: 14px
            }
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('js/kitchenDisplay.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.modules-layouts.operations-screens-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/operations/kitchen-display.blade.php ENDPATH**/ ?>