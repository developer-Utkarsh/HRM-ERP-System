<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Access Request</title>
</head>
<body>

    <div>

        <p>Hi {{ $softwareOwnerName }},</p>

        <p>You’ve received a new access request for <strong>{{ $softwareName }}</strong> by <strong>{{ $employeeName }}</strong>.</p>

        <p>Please review and take action using the link below:</p>

        <p style="text-align: center; margin: 30px 0;">
            <a href="{{ $deeplinkUrl }}" style="background-color: #007BFF; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">➡️ Review Request</a>
        </p>

    </div>

</body>
</html>
