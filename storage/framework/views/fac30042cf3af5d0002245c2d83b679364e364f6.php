<!DOCTYPE html>
<html>

<head>
    <title>Employee Access Alert</title>
</head>

<body>
    <p>Hello <?php echo e($ownerName); ?>,</p>
    <p>The employee <strong><?php echo e($employeeName); ?></strong> still has access to the following systems you approved:</p>
    <ul>
        <li><?php echo e($accessList->software_name); ?></li>
    </ul>
    <p>Please take necessary action if required.</p>

</body>

</html><?php /**PATH /var/www/html/laravel/resources/views/emails/employee_access_alert.blade.php ENDPATH**/ ?>