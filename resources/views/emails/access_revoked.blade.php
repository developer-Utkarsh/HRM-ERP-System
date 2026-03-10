<!DOCTYPE html>
<html>
<head>
    <title>Access Revoked</title>
</head>
<body>
    <p>Hi {{ $details['Department_Head_Name'] }},</p>
    <p>Access for <strong>{{ $details['Employee_Name'] }}</strong> to <strong>{{ $details['Software_Name'] }}</strong> has been revoked.</p>
    <p><strong>Reason:</strong> {{ $details['Revoke_Remark'] }}</p>
</body>
</html>
