<?php

namespace App\Controllers;

class PublicMenuController extends BaseController
{
    public function index(): void
    {
        if (!$this->subdomain) {
            $this->redirect('/');
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            echo "Estabelecimento não encontrado";
            return;
        }

        // Get categories with products
        $stmt = $this->db->getPdo()->prepare("
            SELECT c.*, p.id as product_id, p.name as product_name, p.description as product_description, 
                   p.price as product_price, p.image as product_image, p.is_available as product_available
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id AND p.is_available = 1
            WHERE c.establishment_id = ? AND c.is_active = 1
            ORDER BY c.sort_order, c.name, p.sort_order, p.name
        ");
        $stmt->execute([$establishment['id']]);
        $categories = $stmt->fetchAll();

        // Get business hours
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM business_hours 
            WHERE establishment_id = ? 
            ORDER BY day_of_week
        ");
        $stmt->execute([$establishment['id']]);
        $businessHours = $stmt->fetchAll();

        $this->view('public/menu_mobile', [
            'establishment' => $establishment,
            'categories' => $categories,
            'business_hours' => $businessHours
        ]);
    }

    public function cart(): void
    {
        if (!$this->subdomain) {
            $this->redirect('/');
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            echo "Estabelecimento não encontrado";
            return;
        }

        $this->view('public/cart', [
            'establishment' => $establishment
        ]);
    }

    public function checkout(): void
    {
        if (!$this->subdomain) {
            $this->redirect('/');
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            echo "Estabelecimento não encontrado";
            return;
        }

        // Get payment methods
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM payment_methods 
            WHERE establishment_id = ? AND is_active = 1 
            ORDER BY sort_order, name
        ");
        $stmt->execute([$establishment['id']]);
        $paymentMethods = $stmt->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleOrder($establishment['id']);
            return;
        }

        $this->view('public/checkout', [
            'establishment' => $establishment,
            'payment_methods' => $paymentMethods
        ]);
    }

    private function handleOrder(int $establishmentId): void
    {
        $customerName = $_POST['customer_name'] ?? '';
        $customerPhone = $_POST['customer_phone'] ?? '';
        $customerAddress = $_POST['customer_address'] ?? '';
        $deliveryType = $_POST['delivery_type'] ?? 'delivery';
        $paymentMethodId = $_POST['payment_method_id'] ?? '';
        $notes = $_POST['notes'] ?? '';
        $items = json_decode($_POST['items'] ?? '[]', true);

        if (empty($customerName) || empty($customerPhone) || empty($paymentMethodId) || empty($items) || 
            ($deliveryType === 'delivery' && empty($customerAddress))) {
            // Get payment methods for error display
            $stmt = $this->db->getPdo()->prepare("
                SELECT * FROM payment_methods 
                WHERE establishment_id = ? AND is_active = 1 
                ORDER BY sort_order, name
            ");
            $stmt->execute([$establishmentId]);
            $paymentMethods = $stmt->fetchAll();
            
            $this->view('public/checkout', [
                'establishment' => $this->getEstablishmentBySubdomain($this->subdomain),
                'payment_methods' => $paymentMethods,
                'error' => 'Nome, telefone, endereço, forma de pagamento e pelo menos um item são obrigatórios'
            ]);
            return;
        }

        try {
            $this->db->getPdo()->beginTransaction();

            // Calculate total
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += (float)$item['price'] * (int)$item['quantity'];
            }
            
            // Add delivery fee only for delivery orders
            $deliveryFee = 0;
            if ($deliveryType === 'delivery') {
                $stmt = $this->db->getPdo()->prepare("SELECT delivery_fee FROM establishments WHERE id = ?");
                $stmt->execute([$establishmentId]);
                $establishment = $stmt->fetch();
                $deliveryFee = $establishment['delivery_fee'] ?? 0;
            }
            
            $totalAmount = $subtotal + $deliveryFee;

            // Create order
            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO orders (establishment_id, customer_name, customer_phone, customer_address, delivery_type, payment_method_id, total_amount, notes, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([$establishmentId, $customerName, $customerPhone, $customerAddress, $deliveryType, $paymentMethodId, $totalAmount, $notes]);
            $orderId = $this->db->getPdo()->lastInsertId();

            // Create order items
            foreach ($items as $item) {
                $stmt = $this->db->getPdo()->prepare("
                    INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, options) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['name'],
                    $item['price'],
                    $item['quantity'],
                    json_encode($item['options'] ?? [])
                ]);
            }

            $this->db->getPdo()->commit();

            // Redirect to success page
            $this->redirect("/?order_success={$orderId}");

        } catch (\Exception $e) {
            $this->db->getPdo()->rollBack();
            $this->view('public/checkout', [
                'establishment' => $this->getEstablishmentBySubdomain($this->subdomain),
                'error' => 'Erro ao processar pedido: ' . $e->getMessage()
            ]);
        }
    }

    public function checkoutStep1(): void
    {
        if (!$this->subdomain) {
            $this->redirect('/');
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            echo "Estabelecimento não encontrado";
            return;
        }

        $this->view('public/checkout_step1', [
            'establishment' => $establishment
        ]);
    }

    public function checkoutStep2(): void
    {
        if (!$this->subdomain) {
            $this->redirect('/');
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            echo "Estabelecimento não encontrado";
            return;
        }

        $this->view('public/checkout_step2', [
            'establishment' => $establishment
        ]);
    }

    public function checkoutStep3(): void
    {
        if (!$this->subdomain) {
            $this->redirect('/');
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            echo "Estabelecimento não encontrado";
            return;
        }

        // Get payment methods
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM payment_methods 
            WHERE establishment_id = ? AND is_active = 1
            ORDER BY sort_order, name
        ");
        $stmt->execute([$establishment['id']]);
        $payment_methods = $stmt->fetchAll();

        $this->view('public/checkout_step3', [
            'establishment' => $establishment,
            'payment_methods' => $payment_methods
        ]);
    }

    public function apiCustomerByPhone(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        if (!$this->subdomain) {
            http_response_code(400);
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $phone = $input['phone'] ?? '';

        if (empty($phone)) {
            http_response_code(400);
            return;
        }

        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM customers 
            WHERE establishment_id = ? AND phone = ? 
            ORDER BY updated_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$establishment['id'], $phone]);
        $customer = $stmt->fetch();

        header('Content-Type: application/json');
        
        if ($customer) {
            echo json_encode([
                'success' => true,
                'customer' => $customer
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Cliente não encontrado'
            ]);
        }
    }

    public function apiGetProduct(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            return;
        }

        if (!$this->subdomain) {
            http_response_code(400);
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            return;
        }

        $productId = $this->params[0] ?? null;
        if (!$productId) {
            http_response_code(400);
            return;
        }

        try {
            // Get product details
            $stmt = $this->db->getPdo()->prepare("
                SELECT p.*, c.name as category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = ? AND p.establishment_id = ? AND p.is_available = 1
            ");
            $stmt->execute([$productId, $establishment['id']]);
            $product = $stmt->fetch();

            if (!$product) {
                http_response_code(404);
                echo json_encode(['error' => 'Produto não encontrado']);
                return;
            }

            // Get option groups
            $stmt = $this->db->getPdo()->prepare("
                SELECT * FROM product_option_groups 
                WHERE product_id = ? 
                ORDER BY sort_order, name
            ");
            $stmt->execute([$productId]);
            $optionGroups = $stmt->fetchAll();

            // Get options for each group
            foreach ($optionGroups as &$group) {
                $stmt = $this->db->getPdo()->prepare("
                    SELECT * FROM product_options 
                    WHERE group_id = ? AND is_available = 1
                    ORDER BY sort_order, name
                ");
                $stmt->execute([$group['id']]);
                $group['options'] = $stmt->fetchAll();
            }

            $product['option_groups'] = $optionGroups;

            header('Content-Type: application/json');
            echo json_encode($product);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function apiCreateCustomer(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        if (!$this->subdomain) {
            http_response_code(400);
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        
        $phone = $input['phone'] ?? '';
        $name = $input['name'] ?? '';
        $email = $input['email'] ?? '';
        $cep = $input['cep'] ?? '';
        $address = $input['address'] ?? '';
        $number = $input['number'] ?? '';
        $complement = $input['complement'] ?? '';
        $neighborhood = $input['neighborhood'] ?? '';
        $city = $input['city'] ?? '';
        $state = $input['state'] ?? '';

        if (empty($phone) || empty($name)) {
            http_response_code(400);
            echo json_encode(['error' => 'Telefone e nome são obrigatórios']);
            return;
        }

        // Check if customer already exists
        $stmt = $this->db->getPdo()->prepare("SELECT id FROM customers WHERE phone = ? AND establishment_id = ?");
        $stmt->execute([$phone, $establishment['id']]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'Cliente já existe']);
            return;
        }

        // Geocode the address to get coordinates
        $latitude = null;
        $longitude = null;
        
        if (!empty($address) && !empty($city)) {
            $fullAddress = trim("$address, $number, $neighborhood, $city, $state, Brasil");
            
            try {
                $geocodingService = new \App\Services\GeocodingService();
                $coordinates = $geocodingService->geocodeAddress($fullAddress);
                
                if ($coordinates) {
                    $latitude = $coordinates['lat'];
                    $longitude = $coordinates['lng'];
                }
            } catch (\Exception $e) {
                // Log error but continue without coordinates
                error_log("Geocoding error: " . $e->getMessage());
            }
        }

        // Create new customer
        $stmt = $this->db->getPdo()->prepare("
            INSERT INTO customers (establishment_id, name, phone, email, cep, address, number, complement, neighborhood, city, state, latitude, longitude, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'), datetime('now'))
        ");
        
        $result = $stmt->execute([
            $establishment['id'],
            $name,
            $phone,
            $email,
            $cep,
            $address,
            $number,
            $complement,
            $neighborhood,
            $city,
            $state,
            $latitude,
            $longitude
        ]);

        if ($result) {
            $customerId = $this->db->getPdo()->lastInsertId();
            $stmt = $this->db->getPdo()->prepare("SELECT * FROM customers WHERE id = ?");
            $stmt->execute([$customerId]);
            $customer = $stmt->fetch();
            
            header('Content-Type: application/json');
            echo json_encode(['customer' => $customer]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao criar cliente']);
        }
    }

    public function apiSetSession(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        if (!$this->subdomain) {
            http_response_code(400);
            echo json_encode(['error' => 'Subdomínio não encontrado']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $phone = trim($input['phone'] ?? '');

        if (empty($phone)) {
            http_response_code(400);
            echo json_encode(['error' => 'Telefone é obrigatório']);
            return;
        }

        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Set user phone in session
        $_SESSION['user_phone'] = $phone;
        $_SESSION['user_type'] = 'customer';
        
        // Close session to ensure it's saved
        session_write_close();

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    public function apiClearSession(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Clear session data
        unset($_SESSION['user_phone']);
        unset($_SESSION['user_type']);
        
        // Destroy session
        session_destroy();

        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    }

    public function apiGetPendingOrders(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        if (!$this->subdomain) {
            http_response_code(400);
            echo json_encode(['error' => 'Subdomínio não encontrado']);
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            echo json_encode(['error' => 'Estabelecimento não encontrado']);
            return;
        }

        // Check if user is logged in via session or POST data
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $userPhone = $_SESSION['user_phone'] ?? '';
        
        // If no session phone, try to get from POST data
        if (empty($userPhone) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $userPhone = $input['phone'] ?? '';
        }
        
        if (empty($userPhone)) {
            http_response_code(401);
            echo json_encode(['error' => 'Usuário não logado']);
            return;
        }

        try {
            // Get pending orders from last 3 hours
            $stmt = $this->db->getPdo()->prepare("
                SELECT o.*, pm.name as payment_method_name,
                       COUNT(oi.id) as items_count
                FROM orders o
                LEFT JOIN payment_methods pm ON o.payment_method_id = pm.id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.establishment_id = ? 
                AND o.customer_phone = ?
                AND o.status IN ('pending', 'preparing', 'ready_pickup', 'ready_delivery', 'delivering')
                AND o.created_at >= datetime('now', '-3 hours')
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT 5
            ");
            $stmt->execute([$establishment['id'], $userPhone]);
            $orders = $stmt->fetchAll();

            // Get order items for each order
            foreach ($orders as &$order) {
                $stmt = $this->db->getPdo()->prepare("
                    SELECT * FROM order_items 
                    WHERE order_id = ? 
                    ORDER BY id
                ");
                $stmt->execute([$order['id']]);
                $order['items'] = $stmt->fetchAll();
            }

            header('Content-Type: application/json');
            echo json_encode(['orders' => $orders]);

        } catch (\Exception $e) {
            error_log("Error fetching pending orders: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Erro interno do servidor']);
        }
    }

    public function apiCalculateDelivery(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        if (!$this->subdomain) {
            http_response_code(400);
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $phone = $input['phone'] ?? '';

        if (empty($phone)) {
            http_response_code(400);
            echo json_encode(['error' => 'Telefone não informado']);
            return;
        }

        // Get customer coordinates
        $stmt = $this->db->getPdo()->prepare("
            SELECT latitude, longitude FROM customers 
            WHERE establishment_id = ? AND phone = ? 
            AND latitude IS NOT NULL AND longitude IS NOT NULL
        ");
        $stmt->execute([$establishment['id'], $phone]);
        $customer = $stmt->fetch();

        if (!$customer) {
            http_response_code(404);
            echo json_encode(['error' => 'Cliente não encontrado ou endereço sem coordenadas']);
            return;
        }

        // Get establishment coordinates
        if (!$establishment['latitude'] || !$establishment['longitude']) {
            http_response_code(400);
            echo json_encode(['error' => 'Estabelecimento sem localização configurada']);
            return;
        }

        // Calculate distance
        $geocodingService = new \App\Services\GeocodingService();
        $distance = $geocodingService->calculateDistance(
            (float)$establishment['latitude'],
            (float)$establishment['longitude'],
            (float)$customer['latitude'],
            (float)$customer['longitude']
        );

        // Get delivery zones
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM delivery_zones 
            WHERE establishment_id = ? AND is_active = 1 
            AND radius_km >= ? 
            ORDER BY radius_km ASC 
            LIMIT 1
        ");
        $stmt->execute([$establishment['id'], $distance]);
        $deliveryZone = $stmt->fetch();

        header('Content-Type: application/json');
        
        if ($deliveryZone) {
            echo json_encode([
                'success' => true,
                'distance' => round($distance, 2),
                'delivery_fee' => (float)$deliveryZone['delivery_fee'],
                'min_order_value' => (float)$deliveryZone['min_order_value'],
                'zone_name' => $deliveryZone['name'],
                'is_free' => (float)$deliveryZone['delivery_fee'] == 0
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Entrega não disponível para esta localização',
                'distance' => round($distance, 2)
            ]);
        }
    }

    public function apiPlaceOrder(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        if (!$this->subdomain) {
            http_response_code(400);
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $customerData = $input['customer'] ?? [];
        $addressData = $input['address'] ?? [];
        $cart = $input['cart'] ?? [];
        $cartItems = $input['cart_items'] ?? []; // Full cart with customizations
        $deliveryType = $input['delivery_type'] ?? 'delivery';
        $paymentMethodId = $input['payment_method_id'] ?? '';
        $observations = $input['observations'] ?? '';

        // Validate required fields
        if (empty($customerData['name']) || empty($customerData['phone']) || 
            empty($paymentMethodId) || empty($cart)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados obrigatórios não informados']);
            return;
        }

        // Validate address only for delivery orders
        if ($deliveryType === 'delivery' && (empty($addressData['address']) || empty($addressData['number']))) {
            http_response_code(400);
            echo json_encode(['error' => 'Endereço é obrigatório para entrega']);
            return;
        }

        try {
            $this->db->getPdo()->beginTransaction();

            // Check/create customer
            $stmt = $this->db->getPdo()->prepare("
                SELECT id FROM customers 
                WHERE establishment_id = ? AND phone = ?
            ");
            $stmt->execute([$establishment['id'], $customerData['phone']]);
            $existingCustomer = $stmt->fetch();

            if ($existingCustomer) {
                $customerId = $existingCustomer['id'];
                // Update customer data
                $stmt = $this->db->getPdo()->prepare("
                    UPDATE customers 
                    SET name = ?, email = ?, cep = ?, address = ?, number = ?, 
                        complement = ?, neighborhood = ?, city = ?, state = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                $stmt->execute([
                    $customerData['name'], $customerData['email'] ?? '',
                    $addressData['cep'] ?? '', $addressData['address'], $addressData['number'],
                    $addressData['complement'] ?? '', $addressData['neighborhood'] ?? '',
                    $addressData['city'] ?? '', $addressData['state'] ?? '', $customerId
                ]);
            } else {
                // Create new customer
                $stmt = $this->db->getPdo()->prepare("
                    INSERT INTO customers (establishment_id, name, phone, email, cep, address, number, complement, neighborhood, city, state) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $establishment['id'], $customerData['name'], $customerData['phone'], 
                    $customerData['email'] ?? '', $addressData['cep'] ?? '', $addressData['address'], 
                    $addressData['number'], $addressData['complement'] ?? '', 
                    $addressData['neighborhood'] ?? '', $addressData['city'] ?? '', $addressData['state'] ?? ''
                ]);
                $customerId = $this->db->getPdo()->lastInsertId();
            }

            // Calculate total using cart items with customizations
            $totalAmount = 0;
            if (!empty($cartItems)) {
                // Use detailed cart items if available (includes customizations)
                foreach ($cartItems as $item) {
                    $totalAmount += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
                }
            } else {
                // Fallback to basic cart calculation
                $stmt = $this->db->getPdo()->prepare("SELECT price FROM products WHERE id = ?");
                foreach ($cart as $productId => $quantity) {
                    $stmt->execute([$productId]);
                    $product = $stmt->fetch();
                    if ($product) {
                        $totalAmount += $product['price'] * $quantity;
                    }
                }
            }

            // Add delivery fee only for delivery orders
            if ($deliveryType === 'delivery') {
                $totalAmount += $establishment['delivery_fee'] ?? 0;
            }

            // Create full address string
            if ($deliveryType === 'delivery') {
                $fullAddress = $addressData['address'] . ', ' . $addressData['number'];
                if (!empty($addressData['complement'])) {
                    $fullAddress .= ', ' . $addressData['complement'];
                }
                $fullAddress .= ', ' . ($addressData['neighborhood'] ?? '') . ', ' . ($addressData['city'] ?? '') . ' - ' . ($addressData['state'] ?? '');
                if (!empty($addressData['cep'])) {
                    $fullAddress .= ', CEP: ' . $addressData['cep'];
                }
            } else {
                $fullAddress = 'Retirada no Local';
            }

            // Generate daily order number
            $orderNumber = $this->generateDailyOrderNumber($establishment['id']);

            // Create order
            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO orders (public_id, establishment_id, customer_id, customer_name, customer_phone, customer_address, delivery_type, payment_method_id, total_amount, notes, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([
                $orderNumber, $establishment['id'], $customerId, $customerData['name'], $customerData['phone'], 
                $fullAddress, $deliveryType, $paymentMethodId, $totalAmount, $observations
            ]);
            $orderId = $this->db->getPdo()->lastInsertId();

            // Create order items with customizations
            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, options) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            if (!empty($cartItems)) {
                // Use detailed cart items with customizations
                foreach ($cartItems as $item) {
                    $options = json_encode($item['options'] ?? []);
                    $stmt->execute([
                        $orderId, 
                        $item['product_id'], 
                        $item['name'], 
                        $item['price'], // This includes customization prices
                        $item['quantity'], 
                        $options
                    ]);
                }
            } else {
                // Fallback to basic cart (no customizations)
                $productStmt = $this->db->getPdo()->prepare("SELECT name, price FROM products WHERE id = ?");
                foreach ($cart as $productId => $quantity) {
                    $productStmt->execute([$productId]);
                    $product = $productStmt->fetch();
                    if ($product) {
                        $stmt->execute([$orderId, $productId, $product['name'], $product['price'], $quantity, '[]']);
                    }
                }
            }

            $this->db->getPdo()->commit();

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'order_id' => $orderNumber]);

        } catch (\Exception $e) {
            $this->db->getPdo()->rollback();
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function orderSuccess(): void
    {
        if (!$this->subdomain) {
            $this->redirect('/');
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            echo "Estabelecimento não encontrado";
            return;
        }

        $publicId = $this->params[0] ?? '';
        if (empty($publicId)) {
            $this->redirect('/');
            return;
        }

        // Get order by public_id
        $stmt = $this->db->getPdo()->prepare("
            SELECT o.*, pm.name as payment_method_name
            FROM orders o
            LEFT JOIN payment_methods pm ON o.payment_method_id = pm.id
            WHERE o.public_id = ? AND o.establishment_id = ?
        ");
        $stmt->execute([$publicId, $establishment['id']]);
        $order = $stmt->fetch();

        if (!$order) {
            $this->redirect('/');
            return;
        }

        // Get order items
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM order_items 
            WHERE order_id = ? 
            ORDER BY id
        ");
        $stmt->execute([$order['id']]);
        $orderItems = $stmt->fetchAll();

        $this->view('public/order_success', [
            'establishment' => $establishment,
            'order' => $order,
            'order_items' => $orderItems
        ]);
    }

    private function generateDailyOrderNumber(int $establishmentId): string
    {
        $today = date('Y-m-d');
        
        // Get the last order number for today
        $stmt = $this->db->getPdo()->prepare("
            SELECT public_id FROM orders 
            WHERE establishment_id = ? AND DATE(created_at) = ? 
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute([$establishmentId, $today]);
        $lastOrder = $stmt->fetch();
        
        if ($lastOrder && is_numeric($lastOrder['public_id'])) {
            $nextNumber = (int)$lastOrder['public_id'] + 1;
        } else {
            $nextNumber = 1;
        }
        
        return (string)$nextNumber;
    }

    public function ordersList(): void
    {
        if (!$this->subdomain) {
            $this->redirect('/');
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            echo "Estabelecimento não encontrado";
            return;
        }

        // Check if user is logged in via session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $userPhone = $_SESSION['user_phone'] ?? '';
        
        if (empty($userPhone)) {
            // Show page with auto-login check via JavaScript
            $this->view('public/orders_list_auto_login', [
                'establishment' => $establishment
            ]);
            return;
        }

        // Pagination
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Get total count
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(*) as total 
            FROM orders 
            WHERE establishment_id = ? AND customer_phone = ?
        ");
        $stmt->execute([$establishment['id'], $userPhone]);
        $total = $stmt->fetch()['total'];
        $totalPages = ceil($total / $limit);

        // Get orders
        $stmt = $this->db->getPdo()->prepare("
            SELECT o.*, pm.name as payment_method_name,
                   COUNT(oi.id) as items_count
            FROM orders o
            LEFT JOIN payment_methods pm ON o.payment_method_id = pm.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.establishment_id = ? AND o.customer_phone = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$establishment['id'], $userPhone, $limit, $offset]);
        $orders = $stmt->fetchAll();

        // Get order items for each order
        foreach ($orders as &$order) {
            $stmt = $this->db->getPdo()->prepare("
                SELECT * FROM order_items 
                WHERE order_id = ? 
                ORDER BY id
            ");
            $stmt->execute([$order['id']]);
            $order['items'] = $stmt->fetchAll();
        }

        $this->view('public/orders_list', [
            'establishment' => $establishment,
            'orders' => $orders,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_orders' => $total
        ]);
    }

    public function profile(): void
    {
        if (!$this->subdomain) {
            $this->redirect('/');
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            echo "Estabelecimento não encontrado";
            return;
        }

        // Check if user is logged in via session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $userPhone = $_SESSION['user_phone'] ?? '';
        
        if (empty($userPhone)) {
            // Show page with auto-login check via JavaScript
            $this->view('public/profile_login', [
                'establishment' => $establishment
            ]);
            return;
        }
        
        $customer = null;
        $orderStats = ['total_orders' => 0, 'total_spent' => 0];

        if (!empty($userPhone)) {
            // Get customer data
            $stmt = $this->db->getPdo()->prepare("
                SELECT * FROM customers 
                WHERE establishment_id = ? AND phone = ?
                ORDER BY updated_at DESC 
                LIMIT 1
            ");
            $stmt->execute([$establishment['id'], $userPhone]);
            $customer = $stmt->fetch();

            // Get order statistics
            $stmt = $this->db->getPdo()->prepare("
                SELECT COUNT(*) as total_orders, 
                       COALESCE(SUM(total_amount), 0) as total_spent
                FROM orders 
                WHERE establishment_id = ? AND customer_phone = ? AND status != 'cancelled'
            ");
            $stmt->execute([$establishment['id'], $userPhone]);
            $orderStats = $stmt->fetch();
        }

        $this->view('public/profile', [
            'establishment' => $establishment,
            'customer' => $customer,
            'order_stats' => $orderStats
        ]);
    }

    public function apiUpdateProfile(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        if (!$this->subdomain) {
            http_response_code(400);
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $name = $input['name'] ?? '';
        $phone = $input['phone'] ?? '';
        $email = $input['email'] ?? '';

        if (empty($name) || empty($phone)) {
            http_response_code(400);
            echo json_encode(['error' => 'Nome e telefone são obrigatórios']);
            return;
        }

        try {
            // Check if customer exists
            $stmt = $this->db->getPdo()->prepare("
                SELECT id FROM customers 
                WHERE establishment_id = ? AND phone = ?
            ");
            $stmt->execute([$establishment['id'], $phone]);
            $existingCustomer = $stmt->fetch();

            if ($existingCustomer) {
                // Update existing customer
                $stmt = $this->db->getPdo()->prepare("
                    UPDATE customers 
                    SET name = ?, email = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                $stmt->execute([$name, $email, $existingCustomer['id']]);
            } else {
                // Create new customer
                $stmt = $this->db->getPdo()->prepare("
                    INSERT INTO customers (establishment_id, name, phone, email) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$establishment['id'], $name, $phone, $email]);
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function apiUpdateAddress(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        if (!$this->subdomain) {
            http_response_code(400);
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $phone = $_SESSION['user_phone'] ?? $input['phone'] ?? '';

        if (empty($phone)) {
            http_response_code(400);
            echo json_encode(['error' => 'Telefone não identificado']);
            return;
        }

        try {
            // Geocode the address to get coordinates
            $latitude = null;
            $longitude = null;
            
            $address = $input['address'] ?? '';
            $number = $input['number'] ?? '';
            $neighborhood = $input['neighborhood'] ?? '';
            $city = $input['city'] ?? '';
            $state = $input['state'] ?? '';
            
            if (!empty($address) && !empty($city)) {
                $fullAddress = trim("$address, $number, $neighborhood, $city, $state, Brasil");
                
                try {
                    $geocodingService = new \App\Services\GeocodingService();
                    $coordinates = $geocodingService->geocodeAddress($fullAddress);
                    
                    if ($coordinates) {
                        $latitude = $coordinates['lat'];
                        $longitude = $coordinates['lng'];
                    }
                } catch (\Exception $e) {
                    // Log error but continue without coordinates
                    error_log("Geocoding error: " . $e->getMessage());
                }
            }

            $stmt = $this->db->getPdo()->prepare("
                UPDATE customers 
                SET cep = ?, address = ?, number = ?, complement = ?, 
                    neighborhood = ?, city = ?, state = ?, latitude = ?, longitude = ?, updated_at = CURRENT_TIMESTAMP
                WHERE establishment_id = ? AND phone = ?
            ");
            $stmt->execute([
                $input['cep'] ?? '',
                $address,
                $number,
                $input['complement'] ?? '',
                $neighborhood,
                $city,
                $state,
                $latitude,
                $longitude,
                $establishment['id'],
                $phone
            ]);

            header('Content-Type: application/json');
            echo json_encode(['success' => true]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function apiSubscribeNotifications(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        if (!$this->subdomain) {
            http_response_code(400);
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $subscription = $input['subscription'] ?? [];
        $phone = $input['phone'] ?? '';

        if (empty($subscription) || empty($subscription['endpoint'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados de subscription inválidos']);
            return;
        }

        try {
            // Check if subscription already exists
            $stmt = $this->db->getPdo()->prepare("
                SELECT id FROM push_subscriptions 
                WHERE establishment_id = ? AND endpoint = ?
            ");
            $stmt->execute([$establishment['id'], $subscription['endpoint']]);
            $existing = $stmt->fetch();

            if ($existing) {
                // Update existing subscription
                $stmt = $this->db->getPdo()->prepare("
                    UPDATE push_subscriptions 
                    SET customer_phone = ?, p256dh = ?, auth = ?, is_active = 1, updated_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                $stmt->execute([
                    $phone,
                    $subscription['keys']['p256dh'] ?? '',
                    $subscription['keys']['auth'] ?? '',
                    $existing['id']
                ]);
            } else {
                // Create new subscription
                $stmt = $this->db->getPdo()->prepare("
                    INSERT INTO push_subscriptions (establishment_id, customer_phone, endpoint, p256dh, auth) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $establishment['id'],
                    $phone,
                    $subscription['endpoint'],
                    $subscription['keys']['p256dh'] ?? '',
                    $subscription['keys']['auth'] ?? ''
                ]);
            }

            header('Content-Type: application/json');
            echo json_encode(['success' => true]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function apiUnsubscribeNotifications(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        if (!$this->subdomain) {
            http_response_code(400);
            return;
        }

        $establishment = $this->getEstablishmentBySubdomain($this->subdomain);
        if (!$establishment) {
            http_response_code(404);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $endpoint = $input['endpoint'] ?? '';

        if (empty($endpoint)) {
            http_response_code(400);
            echo json_encode(['error' => 'Endpoint não informado']);
            return;
        }

        try {
            $stmt = $this->db->getPdo()->prepare("
                UPDATE push_subscriptions 
                SET is_active = 0, updated_at = CURRENT_TIMESTAMP
                WHERE establishment_id = ? AND endpoint = ?
            ");
            $stmt->execute([$establishment['id'], $endpoint]);

            header('Content-Type: application/json');
            echo json_encode(['success' => true]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

