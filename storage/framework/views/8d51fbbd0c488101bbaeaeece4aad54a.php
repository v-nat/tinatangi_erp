<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Tinatangi Cafe ERP Management System</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/font/Nunito/static/stylesheet.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.css')); ?>  ">
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendors/bootstrap-icons/bootstrap-icons.css')); ?>  ">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/app.css')); ?>  ">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/pages/error.css')); ?>  ">
</head>

<body>
    <div id="error">
        <div class="error-page container">
            <div class="col-md-8 col-12 offset-md-2">
                <img class="img-error" src="<?php echo e(asset('assets/images/samples/error-403.png')); ?>" alt="Not Found">
                <div class="text-center">
                    <h1 class="error-title">Forbidden</h1>
                    <p class="fs-5 text-gray-600">You are forbbiden to see this page.</p>
                    <a href="<?php echo e(route('admin')); ?>" class="btn btn-lg btn-outline-primary mt-3">Go Home</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/errors/403.blade.php ENDPATH**/ ?>