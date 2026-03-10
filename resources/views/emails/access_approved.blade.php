<!DOCTYPE html>
<html>
<head>
    <title>Access Approved</title>
</head>
<body>
    <p>Hi {{ $details['Department_Head_Name'] }},</p>
    <p>Access to <strong>{{ $details['Software_Name'] }}</strong> has been approved for <strong>{{ $details['Employee_Name'] }}</strong>.</p>
    <p><strong>Remarks:</strong> {{ $details['Remarks'] }}</p>
</body>
</html>
