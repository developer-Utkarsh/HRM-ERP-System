<!DOCTYPE html>
<html>
<head>
    <title>Access Revoked</title>
</head>
<body>
    <p>Hi <?php echo e($details['Department_Head_Name']); ?>,</p>
    <p>Access for <strong><?php echo e($details['Employee_Name']); ?></strong> to <strong><?php echo e($details['Software_Name']); ?></strong> has been revoked.</p>
    <p><strong>Reason:</strong> <?php echo e($details['Revoke_Remark']); ?></p>
</body>
</html>
<?php /**PATH /var/www/html/laravel/resources/views/emails/access_revoked.blade.php ENDPATH**/ ?>