<?php $__env->startComponent('mail::message'); ?>
# Notice of Salary Update

Hello <?php echo new \Illuminate\Support\EncodedHtmlString($employeeName); ?>,

This is to inform you that the base salary for your position, **<?php echo new \Illuminate\Support\EncodedHtmlString($positionName); ?>**, has been updated.

Your new base monthly salary is now **₱<?php echo new \Illuminate\Support\EncodedHtmlString(number_format($newBaseSalary, 2)); ?>**.

This change will be reflected in the upcoming payroll period.

Thanks,<br>
<?php echo new \Illuminate\Support\EncodedHtmlString(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/emails/salary-updated.blade.php ENDPATH**/ ?>