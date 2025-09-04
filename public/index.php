<?php
/**
 * Comida SM - Front Controller (Public Directory)
 * Ponto de entrada principal da aplicação
 */

// Define constantes de ambiente
define('APP_ROOT', dirname(__DIR__));
define('PUBLIC_ROOT', __DIR__);
define('STORAGE_ROOT', APP_ROOT . '/storage');

// Load Composer autoloader
require_once APP_ROOT . '/vendor/autoload.php';

use App\Core\Router;
use App\Core\Database;
use App\Core\Config;

// Error reporting based on environment
$config = new Config();
if ($config->get('app.debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Initialize database
$database = new Database($config->get('database'));

// Initialize router
$router = new Router();

// Handle subdomain routing
$subdomain = $_GET['subdomain'] ?? null;
$path = $_GET['path'] ?? '/';

// Detect subdomain from HTTP_HOST for local testing
if (!$subdomain && isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
    if (preg_match('/^([^.]+)\.localhost(:[0-9]+)?$/', $host, $matches)) {
        $subdomain = $matches[1];
        $path = $_SERVER['REQUEST_URI'] ?? '/';
    }
}

// For testing without hosts file - use ?subdomain=teste
if (!$subdomain && isset($_GET['subdomain'])) {
    $subdomain = $_GET['subdomain'];
    $path = $_SERVER['REQUEST_URI'] ?? '/';
}

if ($subdomain) {
    // Public menu for establishment
    $router->handleSubdomain($subdomain, $path);
} else {
    // Admin panel routes
    $router->handleRequest();
}