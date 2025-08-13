<?php

/**
 * Script to completely remove the installer components from the application
 * Run this script with: php remove_installer.php
 */

echo "Starting installer removal process...\n";

// Ensure the application is marked as installed
if (!file_exists(__DIR__ . '/public/uploads/installed')) {
    if (!is_dir(__DIR__ . '/public/uploads')) {
        mkdir(__DIR__ . '/public/uploads', 0755, true);
        echo "Created uploads directory\n";
    }
    file_put_contents(__DIR__ . '/public/uploads/installed', 'VERIFIED');
    echo "Created installation marker file\n";
}

// Remove installer routes from web.php
$webRoutesPath = __DIR__ . '/routes/web.php';
$webRoutesContent = file_get_contents($webRoutesPath);

// Replace installer routes with a simple redirect
$pattern = '/\/\/\*\*======================== Installler Route Group ====================\*\*\/\/.*?Route::post\(\'install\/migrate\',.*?\);/s';
$replacement = "// Redirect installer routes to home\nRoute::any('install/{any?}', function() { return redirect('/'); })->where('any', '.*');";

$newWebRoutesContent = preg_replace($pattern, $replacement, $webRoutesContent);
if ($newWebRoutesContent !== $webRoutesContent) {
    file_put_contents($webRoutesPath, $newWebRoutesContent);
    echo "Updated web routes to redirect installer routes to home\n";
} else {
    echo "No changes needed for web routes\n";
}

// Remove installer controller and views
$directories = [
    __DIR__ . '/app/Http/Controllers/Installer',
    __DIR__ . '/resources/views/installer',
];

foreach ($directories as $directory) {
    if (is_dir($directory)) {
        echo "Removing directory: $directory\n";
        removeDirectory($directory);
    }
}

// Remove installer JavaScript
$installerJs = __DIR__ . '/public/assets/js/installer.js';
if (file_exists($installerJs)) {
    unlink($installerJs);
    echo "Removed installer.js\n";
}

// Clear Laravel caches
echo "Clearing Laravel caches...\n";
passthru('php artisan config:clear');
passthru('php artisan cache:clear');
passthru('php artisan view:clear');
passthru('php artisan route:clear');

echo "\nInstaller removal completed successfully!\n";
echo "You can now access your application directly without the installer.\n";
echo "Default admin credentials:\n";
echo "Email: admin@admin.com\n";
echo "Password: password\n";

/**
 * Helper function to recursively remove a directory
 */
function removeDirectory($dir) {
    if (!is_dir($dir)) {
        return;
    }
    
    $files = array_diff(scandir($dir), ['.', '..']);
    
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        
        if (is_dir($path)) {
            removeDirectory($path);
        } else {
            unlink($path);
        }
    }
    
    return rmdir($dir);
}
