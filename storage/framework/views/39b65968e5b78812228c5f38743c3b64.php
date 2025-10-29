<?php $__env->startComponent('mail::message'); ?>
# Notice of Update to Mandatory Contributions

Hello,

Please be advised that the company-wide mandatory contributions have been updated. The new monthly amounts are as follows:

- **SSS:** ₱<?php echo new \Illuminate\Support\EncodedHtmlString(number_format($contributions['sss'], 2)); ?>

- **PhilHealth:** ₱<?php echo new \Illuminate\Support\EncodedHtmlString(number_format($contributions['philhealth'], 2)); ?>

- **Pag-IBIG:** ₱<?php echo new \Illuminate\Support\EncodedHtmlString(number_format($contributions['pagibig'], 2)); ?>


These changes will be reflected in the upcoming payroll period.

Thanks,<br>
<?php echo new \Illuminate\Support\EncodedHtmlString(config('app.name')); ?>

<?php echo $__env->renderComponent(); ?>
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/emails/contribution-updated.blade.php ENDPATH**/ ?>