<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Account Credentials</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 30px;">
    <div style="max-width: 600px; margin: auto; background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h2 style="color: #333;">Your Account Has Been Created</h2>

        <p>Hi <?php echo e($name); ?>,</p>

        <p>Your account for the <strong>Tinatangi Cafe ERP System</strong> has been successfully created.</p>

        <p>Here are your login credentials:</p>

        <ul style="background-color: #f0f0f0; padding: 15px; border-radius: 6px; list-style-type: none;">
            <li><strong>Email:</strong> <?php echo e($email); ?></li>
            <li><strong>Password:</strong> <?php echo e($password); ?></li>
        </ul>

        <p>You can log in at the following link:</p>

        <p style="text-align: center;">
            <a href="<?php echo e($login_link); ?>"
               style="display: inline-block; padding: 12px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">
                Login Now
            </a>
        </p>

        <p><strong>Important:</strong> For your security, please change your password after logging in for the first time.</p>

        <hr style="margin-top: 30px;">
        <p style="font-size: 12px; color: #aaa;">&copy; <?php echo e(date('Y')); ?> Tinatangi Cafe. All rights reserved.</p>
    </div>
</body>
</html>
<?php /**PATH C:\Users\Nathaniel\Documents\Nathaniel\Thesis A\tinatangi_erp\resources\views/emails/new-employee.blade.php ENDPATH**/ ?>