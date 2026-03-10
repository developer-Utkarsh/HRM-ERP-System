<!DOCTYPE html>
<html>
<head>
    <title>Access Approved</title>
</head>
<body>
    <p>Hi <?php echo e($details['Department_Head_Name']); ?>,</p>
    <p>Access to <strong><?php echo e($details['Software_Name']); ?></strong> has been approved for <strong><?php echo e($details['Employee_Name']); ?></strong>.</p>
    <p><strong>Remarks:</strong> <?php echo e($details['Remarks']); ?></p>
</body>
</html>
<?php /**PATH /var/www/html/laravel/resources/views/emails/access_approved.blade.php ENDPATH**/ ?>