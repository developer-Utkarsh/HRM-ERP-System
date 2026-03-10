<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Access Request</title>
</head>
<body>

    <div>

        <p>Hi <?php echo e($softwareOwnerName); ?>,</p>

        <p>You’ve received a new access request for <strong><?php echo e($softwareName); ?></strong> by <strong><?php echo e($employeeName); ?></strong>.</p>

        <p>Please review and take action using the link below:</p>

        <p style="text-align: center; margin: 30px 0;">
            <a href="<?php echo e($deeplinkUrl); ?>" style="background-color: #007BFF; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">➡️ Review Request</a>
        </p>

    </div>

</body>
</html>
<?php /**PATH /var/www/html/laravel/resources/views/emails/create_access_request.blade.php ENDPATH**/ ?>