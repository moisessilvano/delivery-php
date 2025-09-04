<?php
/**
 * Comida SM - Front Controller (Public Directory)
 * Ponto de entrada principal da aplicação
 */

// Define constantes de ambiente
define('APP_ROOT', dirname(__DIR__));
define('PUBLIC_ROOT', __DIR__);
define('STORAGE_ROOT', APP_ROOT . '/storage');

// Load classes manually (compatible with both local and production)
require_once APP_ROOT . '/src/Core/Config.php';
require_once APP_ROOT . '/src/Core/Database.php';
require_once APP_ROOT . '/src/Core/Router.php';

// Load base controller
require_once APP_ROOT . '/src/Controllers/BaseController.php';

// Load all controllers
require_once APP_ROOT . '/src/Controllers/AuthController.php';
require_once APP_ROOT . '/src/Controllers/CategoryController.php';
require_once APP_ROOT . '/src/Controllers/CustomerController.php';
require_once APP_ROOT . '/src/Controllers/DashboardController.php';
require_once APP_ROOT . '/src/Controllers/EstablishmentController.php';
require_once APP_ROOT . '/src/Controllers/FileController.php';
require_once APP_ROOT . '/src/Controllers/OrderController.php';
require_once APP_ROOT . '/src/Controllers/PaymentMethodController.php';
require_once APP_ROOT . '/src/Controllers/ProductController.php';
require_once APP_ROOT . '/src/Controllers/PublicMenuController.php';

// Load services
require_once APP_ROOT . '/src/Services/GeocodingService.php';
require_once APP_ROOT . '/src/Services/PushNotificationService.php';

// Error reporting based on environment
$config = new App\Core\Config();
if ($config->get('app.debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Initialize database
$database = new App\Core\Database($config->get('database'));

// Initialize router
$router = new App\Core\Router();

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