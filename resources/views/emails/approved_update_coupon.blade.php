<!DOCTYPE html>
<html>
<head>
    <title>Approved – Coupon Edit/User Assign Request - {{ $details['coupon_title'] }}</title>
</head>
<body>
    <p>Dear <strong>IT DEO Team</strong>,</p>
    
    <p>The following coupon request has been *approved in the HRM Portal*.  </p>
	<p>You may now proceed with the required action from the *ERP Portal* as per the details given below:</p>
	<p><strong>🏷️ Coupon Details</strong></p>
	<p><strong>Categaory Name:</strong> {{ $details['categaory_name'] }}</p>
	<p><strong>Sub Categaory:</strong> {{ $details['sub_category'] }}</p>
	<p><strong>Start Date:</strong> {{ date('d/M/Y h:i A',strtotime($details['start_date'])) }}</p>
	<p><strong>End Date:</strong> {{ date('d/M/Y h:i A',strtotime($details['end_date'])) }}</p>
	<p><strong>Discount Type:</strong> {{ $details['discount_type']==2 ? "%" : "/-" }}</p>
	<p><strong>Coupon Value:</strong> {{ $details['coupon_value'] }}</p>
	<p><strong>Max Discount:</strong> {{ $details['max_discount'] }}</p>
	<p><strong>Max Usage:</strong> {{ $details['max_usage'] }}</p>
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
	<p><strong>Coupon Mode:</strong> {{ $details['coupon_mode']==1?'Manual':'Auto' }}</p>
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
	<p><strong>Data Link Selection:</strong> {{ $details['data_link'] }}</p>
	<p>&nbsp;</p>
	<p><strong>Remark : </strong> {!! nl2br(e($details['remark'])) !!}</p>
	<p><strong>Date :</strong> {{ date('d-M-Y H:i A') }}</p>
	<p>&nbsp;</p>
	<p>Regards,</p>
	<p>{{$details['sender_name']}}</p>
</body>
</html>
