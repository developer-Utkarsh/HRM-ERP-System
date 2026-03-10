<!DOCTYPE html>
<html>
<head>
    <title>Access Rejected</title>
</head>
<body>
    <p>Hi {{ $details['Department_Head_Name'] }},</p>
    <p>The access request for <strong>{{ $details['Employee_Name'] }}</strong> to <strong>{{ $details['Software_Name'] }}</strong> has been rejected.</p>
    <p><strong>Reason:</strong> {{ $details['Rejection_Reason'] }}</p>
</body>
</html>
