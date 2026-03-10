<!DOCTYPE html>
<html>
<head>
    <title>Coupon Edit/User Assign Request – Approval Needed - {{ $details['coupon_title'] }}</title>
</head>
<body>
    <p>Dear <strong>{{$details['approver_name']}}</strong>,</p>
    
    <p>A new request has been submitted for the following coupon in the *Utkarsh App* through the *HRM Portal*.</p>
	<p>Once approved in the HRM Portal, the *IT DEO team* will proceed with the required request from the ERP Portal as per the details given below:</p>
	<p><strong>🏷️ Coupon Details</strong></p>
	<p><strong>Categaory Name:</strong> {{ $details['categaory_name'] }}</p>
	<p><strong>Sub Categaory:</strong> {{ $details['sub_category'] }}</p>
	<p><strong>Start Date:</strong> {{ date('d/M/Y h:i A',strtotime($details['start_date'])) }}</p>
	<p><strong>End Date:</strong> {{ date('d/M/Y h:i A',strtotime($details['end_date'])) }}</p>
	<p><strong>Discount Type:</strong> {{ $details['discount_type']==2 ? "%" : "/-" }}</p>
	<p><strong>Coupon Value:</strong> {{ $details['coupon_value'] }}</p>
	<p><strong>Max Discount:</strong> {{ $details['max_discount'] }}</p>
	<p><strong>Max Usage:</strong> {{ $details['max_usage'] }}</p>
	 
	<p><strong>Coupon Mode:</strong> {{ $details['coupon_mode']==1?'Manual':'Auto' }}</p>
	 
	<p><strong>Remark : </strong> {!! nl2br(e($details['remark'])) !!}</p>
	<p><strong>Date :</strong> {{ date('d-M-Y H:i A') }}</p>
	<p>✅ Kindly review and take the necessary action.</p>
	<p>Regards,</p>
	<p>{{$details['sender_name']}}</p>
</body>
</html>
