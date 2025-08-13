<!DOCTYPE html>
<html>
<head>
    <title>Test Email from Jua</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #297d59;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Jua Email Test</h1>
    </div>
    <div class="content">
        <h2>Hello from Jua!</h2>
        <p>This is a test email to verify that your email configuration is working correctly.</p>
        <p>If you're seeing this email in Mailhog, your email configuration is set up properly!</p>
        <p>Current time: {{ date('Y-m-d H:i:s') }}</p>
    </div>
    <div class="footer">
        <p>This is an automated message from Jua. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} Jua. All rights reserved.</p>
    </div>
</body>
</html>
