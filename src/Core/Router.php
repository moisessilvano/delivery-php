<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $subdomainRoutes = [];

    public function __construct()
    {
        $this->setupRoutes();
    }

    private function setupRoutes(): void
    {
        // Admin routes
        $this->routes = [
            '/' => 'AuthController@index',
            '/login' => 'AuthController@login',
            '/register' => 'AuthController@register',
            '/logout' => 'AuthController@logout',
            '/dashboard' => 'DashboardController@index',
            '/profile' => 'EstablishmentController@profile',
            '/profile/edit' => 'EstablishmentController@edit',
            '/profile/edit/info' => 'EstablishmentController@editInfo',
            '/profile/edit/contact' => 'EstablishmentController@editContact',
            '/profile/edit/delivery' => 'EstablishmentController@editDelivery',
            '/profile/edit/hours' => 'EstablishmentController@editHours',
            '/profile/edit/design' => 'EstablishmentController@editDesign',
            '/storage/{path}' => 'FileController@serve',
            '/api/geocode-address' => 'EstablishmentController@geocodeAddress',
            '/api/viacep' => 'EstablishmentController@viaCep',
            '/api/delivery-zones' => 'EstablishmentController@deliveryZones',
            '/api/delivery-zones/update' => 'EstablishmentController@updateDeliveryZone',
            '/api/delivery-zones/delete' => 'EstablishmentController@deleteDeliveryZone',
            '/categories' => 'CategoryController@index',
            '/categories/create' => 'CategoryController@create',
            '/categories/edit/{id}' => 'CategoryController@edit',
            '/categories/update-order' => 'CategoryController@updateOrder',
            '/products' => 'ProductController@index',
            '/products/create' => 'ProductController@create',
            '/products/edit/{id}' => 'ProductController@edit',
            '/customers' => 'CustomerController@index',
            '/customers/create' => 'CustomerController@create',
            '/customers/edit/{id}' => 'CustomerController@edit',
            '/payment-methods' => 'PaymentMethodController@index',
            '/payment-methods/create' => 'PaymentMethodController@create',
            '/payment-methods/edit/{id}' => 'PaymentMethodController@edit',
            '/payment-methods/delete/{id}' => 'PaymentMethodController@delete',
            '/payment-methods/setup-default' => 'PaymentMethodController@setupDefault',
            '/orders' => 'OrderController@index',
            '/orders/kanban' => 'OrderController@kanban',
            '/orders/history' => 'OrderController@history',
            '/orders/create' => 'OrderController@create',
            '/orders/{id}' => 'OrderController@show',
            '/api/orders' => 'OrderController@apiOrders',
            '/api/orders/history' => 'OrderController@apiOrdersHistory',
            '/api/orders/new' => 'OrderController@apiGetNewOrders',
            '/api/orders/mark-notified' => 'OrderController@apiMarkAsNotified',
            '/api/orders/{id}/details' => 'OrderController@apiOrderDetails',
            '/api/orders/{id}/status' => 'OrderController@apiUpdateStatus',
        ];

        // Subdomain routes (public menu)
        $this->subdomainRoutes = [
            '/' => 'PublicMenuController@index',
            '/cart' => 'PublicMenuController@cart',
            '/checkout' => 'PublicMenuController@checkout',
            '/checkout-step1' => 'PublicMenuController@checkoutStep1',
            '/checkout-step2' => 'PublicMenuController@checkoutStep2',
            '/checkout-step3' => 'PublicMenuController@checkoutStep3',
            '/order' => 'PublicMenuController@handleOrder',
            '/order-success/{id}' => 'PublicMenuController@orderSuccess',
            '/orders-list' => 'PublicMenuController@ordersList',
            '/profile' => 'PublicMenuController@profile',
            '/storage/{path}' => 'FileController@serve',
            '/api/customer-by-phone' => 'PublicMenuController@apiCustomerByPhone',
            '/api/create-customer' => 'PublicMenuController@apiCreateCustomer',
            '/api/product/{id}' => 'PublicMenuController@apiGetProduct',
            '/api/calculate-delivery' => 'PublicMenuController@apiCalculateDelivery',
            '/api/place-order' => 'PublicMenuController@apiPlaceOrder',
            '/api/update-profile' => 'PublicMenuController@apiUpdateProfile',
            '/api/update-address' => 'PublicMenuController@apiUpdateAddress',
            '/api/subscribe-notifications' => 'PublicMenuController@apiSubscribeNotifications',
            '/api/unsubscribe-notifications' => 'PublicMenuController@apiUnsubscribeNotifications',
            '/api/set-session' => 'PublicMenuController@apiSetSession',
            '/api/clear-session' => 'PublicMenuController@apiClearSession',
            '/api/pending-orders' => 'PublicMenuController@apiGetPendingOrders',
        ];
    }

    public function handleRequest(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // Remove trailing slash
        $uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }

        // Check if route exists
        if (isset($this->routes[$uri])) {
            $this->dispatch($this->routes[$uri]);
        } else {
            // Try to match dynamic routes
            $this->matchDynamicRoute($uri);
        }
    }

    public function handleSubdomain(string $subdomain, string $path): void
    {
        // Remove trailing slash
        $path = rtrim($path, '/');
        if (empty($path)) {
            $path = '/';
        }

        // Check if subdomain route exists
        if (isset($this->subdomainRoutes[$path])) {
            $this->dispatch($this->subdomainRoutes[$path], $subdomain);
        } else {
            // Try to match dynamic subdomain routes
            $this->matchDynamicSubdomainRoute($path, $subdomain);
        }
    }

    private function matchDynamicRoute(string $uri): void
    {
        // Special handling for storage files (can contain slashes)
        if (strpos($uri, '/storage/') === 0) {
            $path = substr($uri, 9); // Remove '/storage/' prefix
            $this->dispatch('FileController@serve', null, [$path]);
            return;
        }
        
        foreach ($this->routes as $route => $handler) {
            if (strpos($route, '{') !== false) {
                $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
                if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
                    array_shift($matches); // Remove full match
                    $this->dispatch($handler, null, $matches);
                    return;
                }
            }
        }

        // 404
        http_response_code(404);
        echo "Página não encontrada";
    }

    private function matchDynamicSubdomainRoute(string $path, string $subdomain): void
    {
        // Special handling for storage files (can contain slashes)
        if (strpos($path, '/storage/') === 0) {
            $filePath = substr($path, 9); // Remove '/storage/' prefix
            $this->dispatch('FileController@serve', $subdomain, [$filePath]);
            return;
        }
        
        foreach ($this->subdomainRoutes as $route => $handler) {
            if (strpos($route, '{') !== false) {
                $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
                if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
                    array_shift($matches); // Remove full match
                    $this->dispatch($handler, $subdomain, $matches);
                    return;
                }
            }
        }

        // 404
        http_response_code(404);
        echo "Página não encontrada";
    }

    private function dispatch(string $handler, ?string $subdomain = null, array $params = []): void
    {
        [$controller, $method] = explode('@', $handler);
        
        $controllerClass = "App\\Controllers\\{$controller}";
        
        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller {$controllerClass} not found");
        }

        $instance = new $controllerClass();
        
        if (!method_exists($instance, $method)) {
            throw new \Exception("Method {$method} not found in {$controllerClass}");
        }

        // Pass subdomain and params to controller
        $instance->setSubdomain($subdomain);
        $instance->setParams($params);
        
        call_user_func([$instance, $method]);
    }
}
