<?php echo $__env->make('partials.crm-heading', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->startSection('crmIndex'); ?> active <?php $__env->stopSection(); ?>
<?php $__env->startSection('headings'); ?> Customer Relationship Dashboard <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('crm')); ?>">Customer Relationship</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <div class="section p-6 mt-4">
        <h2>Customer Feedback</h2>

        <div id="feedback-container" class="row">
            <p>Loading feedback...</p>
        </div>

        <div id="pagination-links" class="mt-4 d-flex justify-content-center"></div>
    </div>

    <style>
        .feedback-card {
            background-color: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            padding: 20px;
            transition: box-shadow 0.3s ease;
        }

        .feedback-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .card-header h5 {
            margin: 0;
            color: #333;
        }

        .card-rating {
            font-size: 1.2rem;
            font-weight: bold;
            color: #ffc107;
            /* Gold color for stars */
        }

        .card-body p {
            color: #555;
        }

        .card-photo a {
            font-weight: 500;
        }

        .card-footer {
            font-size: 0.85rem;
            color: #777;
            border-top: 1px solid #e9ecef;
            padding-top: 10px;
            margin-top: 15px;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('js/customerService.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/pages/admin/crm/index.blade.php ENDPATH**/ ?>