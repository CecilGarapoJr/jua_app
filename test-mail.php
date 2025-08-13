<?php

// Set error reporting to show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing direct mail sending...\n";

// Mail server settings from .env
$host = '127.0.0.1';
$port = 1025;
$from = 'noreply@jua.test';
$to = 'test@example.com';
$subject = 'Direct PHP Mail Test';

// Create message
$message = "
<html>
<head>
    <title>Test Email</title>
</head>
<body>
    <h1>Test Email from Direct PHP Script</h1>
    <p>This is a test email sent directly using PHP's mail function.</p>
    <p>Time: " . date('Y-m-d H:i:s') . "</p>
</body>
</html>
";

// Headers
$headers = [
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=utf-8',
    'From: Jua Test <' . $from . '>',
    'X-Mailer: PHP/' . phpversion()
];

// For SMTP debugging
echo "Attempting to connect to SMTP server at {$host}:{$port}...\n";

// Try to send mail using PHP's mail function
$result = mail($to, $subject, $message, implode("\r\n", $headers));

if ($result) {
    echo "Mail sent successfully!\n";
} else {
    echo "Failed to send mail.\n";
    
    // Check if mail function is available
    if (!function_exists('mail')) {
        echo "PHP mail function is not available.\n";
    }
}

// Try using socket connection to test SMTP directly
echo "\nTesting direct SMTP connection...\n";
try {
    $socket = fsockopen($host, $port, $errno, $errstr, 30);
    if (!$socket) {
        echo "Socket error: $errstr ($errno)\n";
    } else {
        echo "Successfully connected to SMTP server!\n";
        fwrite($socket, "EHLO localhost\r\n");
        $response = fgets($socket, 515);
        echo "Server response: $response\n";
        fclose($socket);
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";
