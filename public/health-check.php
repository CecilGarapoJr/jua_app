<?php

/**
 * Health Check Endpoint for Jua Application
 * 
 * This file provides a simple health check endpoint that can be used by Coolify
 * to monitor the health of the application.
 */

// Set content type to JSON
header('Content-Type: application/json');

// Initialize status array
$status = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'environment' => getenv('APP_ENV') ?: 'unknown',
    'version' => '1.0.0',
    'components' => []
];

// Check database connection
try {
    $dbHost = getenv('DB_HOST') ?: '127.0.0.1';
    $dbPort = getenv('DB_PORT') ?: '3306';
    $dbName = getenv('DB_DATABASE') ?: 'JuaV1';
    $dbUser = getenv('DB_USERNAME') ?: 'root';
    $dbPass = getenv('DB_PASSWORD') ?: '';
    
    $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName";
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Simple query to test connection
    $pdo->query('SELECT 1');
    
    $status['components']['database'] = [
        'status' => 'ok',
        'message' => 'Database connection successful'
    ];
} catch (PDOException $e) {
    $status['components']['database'] = [
        'status' => 'error',
        'message' => 'Database connection failed: ' . $e->getMessage()
    ];
    $status['status'] = 'error';
}

// Check file storage
try {
    $storageDir = '../storage/app';
    if (is_dir($storageDir) && is_writable($storageDir)) {
        $status['components']['storage'] = [
            'status' => 'ok',
            'message' => 'Storage directory is writable'
        ];
    } else {
        throw new Exception('Storage directory is not writable');
    }
} catch (Exception $e) {
    $status['components']['storage'] = [
        'status' => 'error',
        'message' => 'Storage check failed: ' . $e->getMessage()
    ];
    $status['status'] = 'error';
}

// Check Redis connection if configured
if (getenv('CACHE_DRIVER') === 'redis' || getenv('SESSION_DRIVER') === 'redis') {
    try {
        $redisHost = getenv('REDIS_HOST') ?: '127.0.0.1';
        $redisPort = getenv('REDIS_PORT') ?: 6379;
        $redisPassword = getenv('REDIS_PASSWORD') ?: null;
        
        $redis = new Redis();
        $redis->connect($redisHost, $redisPort);
        
        if ($redisPassword) {
            $redis->auth($redisPassword);
        }
        
        $redis->ping();
        
        $status['components']['redis'] = [
            'status' => 'ok',
            'message' => 'Redis connection successful'
        ];
    } catch (Exception $e) {
        $status['components']['redis'] = [
            'status' => 'warning',
            'message' => 'Redis connection failed: ' . $e->getMessage()
        ];
        // Redis failure is a warning, not a critical error
    }
}

// Check disk space
$diskFree = disk_free_space('/');
$diskTotal = disk_total_space('/');
$diskUsed = $diskTotal - $diskFree;
$diskPercent = round(($diskUsed / $diskTotal) * 100);

$status['components']['disk'] = [
    'status' => $diskPercent > 90 ? 'warning' : 'ok',
    'message' => "Disk usage: $diskPercent% ($diskFree bytes free)",
];

// Return status with appropriate HTTP code
if ($status['status'] === 'error') {
    http_response_code(503); // Service Unavailable
} else {
    http_response_code(200); // OK
}

echo json_encode($status, JSON_PRETTY_PRINT);
