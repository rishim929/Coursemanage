<?php
/**
 * Blade Template Engine Configuration
 */

require 'vendor/autoload.php';

use Jenssegers\Blade\Blade;

// Create views and cache directories if they don't exist
$views_dir = __DIR__ . '/views';
$cache_dir = __DIR__ . '/storage/cache';

if (!is_dir($views_dir)) {
    mkdir($views_dir, 0755, true);
}

if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0755, true);
}

// Initialize Blade
$blade = new Blade($views_dir, $cache_dir);

// Return the blade instance for use in other files
return $blade;
?>
