<!DOCTYPE html>
<html>
<head>
    <title>Coupon Edit/User Assign Request – Approval Needed - <?php echo e($details['coupon_title']); ?></title>
</head>
<body>
    <p>Dear <strong><?php echo e($details['approver_name']); ?></strong>,</p>
    
    <p>A new request has been submitted for the following coupon in the *Utkarsh App* through the *HRM Portal*.</p>
	<p>Once approved in the HRM Portal, the *IT DEO team* will proceed with the required request from the ERP Portal as per the details given below:</p>
	<p><strong>🏷️ Coupon Details</strong></p>
	<p><strong>Categaory Name:</strong> <?php echo e($details['categaory_name']); ?></p>
	<p><strong>Sub Categaory:</strong> <?php echo e($details['sub_category']); ?></p>
	<p><strong>Start Date:</strong> <?php echo e(date('d/M/Y h:i A',strtotime($details['start_date']))); ?></p>
	<p><strong>End Date:</strong> <?php echo e(date('d/M/Y h:i A',strtotime($details['end_date']))); ?></p>
	<p><strong>Discount Type:</strong> <?php echo e($details['discount_type']==2 ? "%" : "/-"); ?></p>
	<p><strong>Coupon Value:</strong> <?php echo e($details['coupon_value']); ?></p>
	<p><strong>Max Discount:</strong> <?php echo e($details['max_discount']); ?></p>
	<p><strong>Max Usage:</strong> <?php echo e($details['max_usage']); ?></p>
	 
	<p><strong>Coupon Mode:</strong> <?php echo e($details['coupon_mode']==1?'Manual':'Auto'); ?></p>
	 
	<p><strong>Remark : </strong> <?php echo nl2br(e($details['remark'])); ?></p>
	<p><strong>Date :</strong> <?php echo e(date('d-M-Y H:i A')); ?></p>
	<p>✅ Kindly review and take the necessary action.</p>
	<p>Regards,</p>
	<p><?php echo e($details['sender_name']); ?></p>
</body>
</html>
<?php /**PATH /var/www/html/laravel/resources/views/emails/remark_coupon.blade.php ENDPATH**/ ?>