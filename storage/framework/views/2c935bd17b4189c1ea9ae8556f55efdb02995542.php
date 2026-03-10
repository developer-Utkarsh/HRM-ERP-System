<!DOCTYPE html>
<html>
<head>
    <title>Coupon Approval Request - <?php echo e($details['coupon_title']); ?></title>
</head>
<body>
    <p>Dear <strong>IT DEO Team</strong>,</p>
    
    <p>I have reviewed and approved the coupon request in the HRM Portal.</p>
	<p>Please proceed with creating the coupon in the ERP Portal as per the details given below:</p>
	<p><strong>🏷️ Coupon Details</strong></p>
	<p><strong>Categaory Name:</strong> <?php echo e($details['categaory_name']); ?></p>
	<p><strong>Sub Categaory:</strong> <?php echo e($details['sub_category']); ?></p>
	<p><strong>Start Date:</strong> <?php echo e(date('d/M/Y h:i A',strtotime($details['start_date']))); ?></p>
	<p><strong>End Date:</strong> <?php echo e(date('d/M/Y h:i A',strtotime($details['end_date']))); ?></p>
	<p><strong>Discount Type:</strong> <?php echo e($details['discount_type']==2 ? "%" : "/-"); ?></p>
	<p><strong>Coupon Value:</strong> <?php echo e($details['coupon_value']); ?></p>
	<p><strong>Max Discount:</strong> <?php echo e($details['max_discount']); ?></p>
	<p><strong>Max Usage:</strong> <?php echo e($details['max_usage']); ?></p>
	<p><strong>Coupon Type:</strong> <?php
										if($details['coupon_type']==1){
											echo "User Dependent";
										}
										else if($details['coupon_type']==2){
											echo "User + Course Dependent";
										}
										else if($details['coupon_type']==0){
											echo "Course Dependent";
										}
										?></p>
	<p><strong>Coupon Mode:</strong> <?php echo e($details['coupon_mode']==1?'Manual':'Auto'); ?></p>
	<p><strong>Course Type:</strong> <?php
										if($details['course_type']==1){
											echo "Prime";
										}
										else if($details['course_type']==2){
											echo "Both";
										}
										else if($details['course_type']==0){
											echo "Standard";
										}
										?></p>
	<p><strong>🔗 Data Link Selection : </strong> <?php echo e($details['data_link']); ?></p>
	<p><strong>📌 Reason for Coupon : </strong> <?php echo e($details['reason']); ?></p>
	<p>✅ Kindly proceed with the creation of this coupon in the ERP Portal at the earliest.</p>
	<p>Regards,</p>
	<p><?php echo e($details['sender_name']); ?></p>
</body>
</html>
<?php /**PATH /var/www/html/laravel/resources/views/emails/approved_coupon.blade.php ENDPATH**/ ?>