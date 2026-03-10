<!DOCTYPE html>
<html>
<head>
    <title>Access Rejected</title>
</head>
<body>
    <p>Hi <?php echo e($details['Department_Head_Name']); ?>,</p>
    <p>The access request for <strong><?php echo e($details['Employee_Name']); ?></strong> to <strong><?php echo e($details['Software_Name']); ?></strong> has been rejected.</p>
    <p><strong>Reason:</strong> <?php echo e($details['Rejection_Reason']); ?></p>
</body>
</html>
<?php /**PATH /var/www/html/laravel/resources/views/emails/access_rejected.blade.php ENDPATH**/ ?>