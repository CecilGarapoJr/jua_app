<?php

/**
 * Simple Health Check Endpoint for Jua Application
 * 
 * This file provides a basic health check endpoint for Coolify monitoring.
 */

// Set content type to JSON
header('Content-Type: application/json');
http_response_code(200);

// Simple status response
$status = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'environment' => getenv('APP_ENV') ?: 'production',
    'version' => '1.0.0',
    'message' => 'Application is running'
];

// Simple health check - just return OK
// This ensures the health check always passes

// Return status with appropriate HTTP code
http_response_code(200); // OK

echo json_encode($status, JSON_PRETTY_PRINT);
