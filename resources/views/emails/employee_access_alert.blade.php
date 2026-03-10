<!DOCTYPE html>
<html>

<head>
    <title>Employee Access Alert</title>
</head>

<body>
    <p>Hello {{ $ownerName }},</p>
    <p>The employee <strong>{{ $employeeName }}</strong> still has access to the following systems you approved:</p>
    <ul>
        <li>{{ $accessList->software_name }}</li>
    </ul>
    <p>Please take necessary action if required.</p>

</body>

</html>