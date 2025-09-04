<?php
/**
 * Simple Autoloader for Comida SM
 * For production environments without Composer
 */

spl_autoload_register(function ($class) {
    // Remove the App\ prefix
    $class = str_replace('App\\', '', $class);
    
    // Convert namespace separators to directory separators
    $class = str_replace('\\', '/', $class);
    
    // Build the file path
    $file = __DIR__ . '/src/' . $class . '.php';
    
    // Check if the file exists and include it
    if (file_exists($file)) {
        require_once $file;
    }
});
