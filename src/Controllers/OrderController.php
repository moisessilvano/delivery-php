<?php

namespace App\Controllers;

class OrderController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        // Redirect to kanban view by default
        $this->redirect('/orders/kanban');
    }

    public function kanban(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        $this->view('orders/kanban', [
            'establishment' => $establishment
        ]);
    }

    public function show(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        $orderId = $this->params[0] ?? null;
        if (!$orderId) {
            $this->redirect('/orders');
            return;
        }

        // Get order
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM orders 
            WHERE id = ? AND establishment_id = ?
        ");
        $stmt->execute([$orderId, $establishment['id']]);
        $order = $stmt->fetch();

        if (!$order) {
            $this->redirect('/orders');
            return;
        }

        // Get order items
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM order_items 
            WHERE order_id = ? 
            ORDER BY id
        ");
        $stmt->execute([$orderId]);
        $order['items'] = $stmt->fetchAll();

        // Handle status update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
            $this->updateOrderStatus($orderId, $establishment['id'], $_POST['status']);
            $this->redirect("/orders/{$orderId}?success=Status atualizado com sucesso");
            return;
        }

        $this->view('orders/show', [
            'order' => $order,
            'establishment' => $establishment
        ]);
    }

    private function updateOrderStatus(int $orderId, int $establishmentId, string $status): void
    {
        $allowedStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'];
        
        if (!in_array($status, $allowedStatuses)) {
            return;
        }

        $stmt = $this->db->getPdo()->prepare("
            UPDATE orders 
            SET status = ?, updated_at = CURRENT_TIMESTAMP 
            WHERE id = ? AND establishment_id = ?
        ");
        $stmt->execute([$status, $orderId, $establishmentId]);
    }

    public function create(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreateOrder($establishment['id']);
            return;
        }

        // Get categories and products for the form
        $stmt = $this->db->getPdo()->prepare("
            SELECT c.*, p.id as product_id, p.name as product_name, p.price as product_price
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id AND p.is_available = 1
            WHERE c.establishment_id = ? AND c.is_active = 1
            ORDER BY c.sort_order, c.name, p.sort_order, p.name
        ");
        $stmt->execute([$establishment['id']]);
        $categories = $stmt->fetchAll();

        $this->view('orders/create', [
            'categories' => $categories,
            'establishment' => $establishment
        ]);
    }

    private function handleCreateOrder(int $establishmentId): void
    {
        $customerName = $_POST['customer_name'] ?? '';
        $customerPhone = $_POST['customer_phone'] ?? '';
        $customerAddress = $_POST['customer_address'] ?? '';
        $notes = $_POST['notes'] ?? '';
        $items = $_POST['items'] ?? [];

        if (empty($customerName) || empty($customerPhone) || empty($items)) {
            $this->view('orders/create', [
                'categories' => $this->getCategoriesWithProducts($establishmentId),
                'establishment' => $this->getCurrentEstablishment(),
                'error' => 'Nome, telefone e pelo menos um item são obrigatórios'
            ]);
            return;
        }

        try {
            $this->db->getPdo()->beginTransaction();

            // Calculate total
            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += (float)$item['price'] * (int)$item['quantity'];
            }

            // Generate daily order number
            $publicId = $this->generateDailyOrderNumber($establishmentId);

            // Create order
            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO orders (establishment_id, public_id, customer_name, customer_phone, customer_address, total_amount, notes, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([$establishmentId, $publicId, $customerName, $customerPhone, $customerAddress, $totalAmount, $notes]);
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
                    $item['product_name'],
                    $item['price'],
                    $item['quantity'],
                    json_encode($item['options'] ?? [])
                ]);
            }

            $this->db->getPdo()->commit();
            $this->redirect("/orders/{$orderId}?success=Pedido criado com sucesso");

        } catch (\Exception $e) {
            $this->db->getPdo()->rollBack();
            $this->view('orders/create', [
                'categories' => $this->getCategoriesWithProducts($establishmentId),
                'establishment' => $this->getCurrentEstablishment(),
                'error' => 'Erro ao criar pedido: ' . $e->getMessage()
            ]);
        }
    }

    private function getCategoriesWithProducts(int $establishmentId): array
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT c.*, p.id as product_id, p.name as product_name, p.price as product_price
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id AND p.is_available = 1
            WHERE c.establishment_id = ? AND c.is_active = 1
            ORDER BY c.sort_order, c.name, p.sort_order, p.name
        ");
        $stmt->execute([$establishmentId]);
        return $stmt->fetchAll();
    }

    public function history(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        $this->view('orders/history', [
            'establishment' => $establishment
        ]);
    }

    public function apiOrdersHistory(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            http_response_code(403);
            return;
        }

        $phone = $_GET['phone'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';

        $sql = "
            SELECT o.public_id, o.customer_name, o.customer_phone, o.customer_address, o.delivery_type, o.total_amount, 
                   o.status, o.created_at, o.notes, pm.name as payment_method,
                   COUNT(oi.id) as items_count
            FROM orders o
            LEFT JOIN payment_methods pm ON o.payment_method_id = pm.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.establishment_id = ?
        ";
        
        $params = [$establishment['id']];

        if (!empty($phone)) {
            $sql .= " AND o.customer_phone LIKE ?";
            $params[] = "%$phone%";
        }

        if (!empty($dateFrom)) {
            $sql .= " AND DATE(o.created_at) >= ?";
            $params[] = $dateFrom;
        }

        if (!empty($dateTo)) {
            $sql .= " AND DATE(o.created_at) <= ?";
            $params[] = $dateTo;
        }

        $sql .= " GROUP BY o.id ORDER BY o.created_at DESC LIMIT 100";

        $stmt = $this->db->getPdo()->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll();

        header('Content-Type: application/json');
        echo json_encode(['orders' => $orders]);
    }

    public function apiOrders(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            http_response_code(403);
            return;
        }

        $stmt = $this->db->getPdo()->prepare("
            SELECT o.public_id, o.customer_name, o.customer_phone, o.customer_address, o.delivery_type, o.total_amount, 
                   o.status, o.created_at, o.notes, pm.name as payment_method,
                   COUNT(oi.id) as items_count
            FROM orders o
            LEFT JOIN payment_methods pm ON o.payment_method_id = pm.id
            LEFT JOIN order_items oi ON o.id = oi.order_id
            WHERE o.establishment_id = ? AND o.status IN ('pending', 'preparing', 'ready', 'delivering')
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$establishment['id']]);
        $orders = $stmt->fetchAll();

        header('Content-Type: application/json');
        echo json_encode($orders);
    }

    public function apiUpdateStatus(): void
    {
        $this->requireAuth();

        if (!in_array($_SERVER['REQUEST_METHOD'], ['PATCH', 'PUT'])) {
            http_response_code(405);
            return;
        }

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            http_response_code(403);
            return;
        }

        $publicId = $this->params[0] ?? null;
        if (!$publicId) {
            http_response_code(400);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $status = $input['status'] ?? '';

        $allowedStatuses = ['pending', 'preparing', 'ready', 'delivering', 'completed', 'cancelled'];
        if (!in_array($status, $allowedStatuses)) {
            http_response_code(400);
            echo json_encode(['error' => 'Status inválido']);
            return;
        }

        try {
            $stmt = $this->db->getPdo()->prepare("
                UPDATE orders 
                SET status = ?, updated_at = CURRENT_TIMESTAMP
                WHERE public_id = ? AND establishment_id = ?
            ");
            $stmt->execute([$status, $publicId, $establishment['id']]);

            // Send push notification about status change
            if ($stmt->rowCount() > 0) {
                $pushService = new \App\Services\PushNotificationService($this->db);
                $pushService->sendOrderStatusUpdate($publicId, $status);
            }

            http_response_code(200);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function apiOrderDetails(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            http_response_code(403);
            return;
        }

        $orderId = $this->params[0] ?? null;
        if (!$orderId) {
            http_response_code(400);
            echo json_encode(['error' => 'Order ID is required']);
            return;
        }

        try {
            // Get order details
            $stmt = $this->db->getPdo()->prepare("
                SELECT o.*, pm.name as payment_method
                FROM orders o
                LEFT JOIN payment_methods pm ON o.payment_method_id = pm.id
                WHERE o.public_id = ? AND o.establishment_id = ?
            ");
            $stmt->execute([$orderId, $establishment['id']]);
            $order = $stmt->fetch();

            if (!$order) {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']);
                return;
            }

            // Get order items with options
            $stmt = $this->db->getPdo()->prepare("
                SELECT oi.*, p.name as product_name
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
                ORDER BY oi.id
            ");
            $stmt->execute([$order['id']]);
            $items = $stmt->fetchAll();

            // Parse options for each item
            foreach ($items as &$item) {
                $item['options'] = $item['options'] ? json_decode($item['options'], true) : [];
            }

            $order['items'] = $items;

            header('Content-Type: application/json');
            echo json_encode($order);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function apiGetNewOrders(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            http_response_code(403);
            return;
        }

        try {
            // Get orders that haven't been notified yet
            $stmt = $this->db->getPdo()->prepare("
                SELECT public_id, customer_name, total_amount, created_at 
                FROM orders 
                WHERE establishment_id = ? AND notified = 0 AND status = 'pending'
                ORDER BY created_at ASC
            ");
            $stmt->execute([$establishment['id']]);
            $newOrders = $stmt->fetchAll();

            header('Content-Type: application/json');
            echo json_encode(['new_orders' => $newOrders]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function apiMarkAsNotified(): void
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            http_response_code(403);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $orderIds = $input['order_ids'] ?? [];

        if (empty($orderIds) || !is_array($orderIds)) {
            http_response_code(400);
            echo json_encode(['error' => 'IDs dos pedidos são obrigatórios']);
            return;
        }

        try {
            // Mark orders as notified
            $placeholders = str_repeat('?,', count($orderIds) - 1) . '?';
            $stmt = $this->db->getPdo()->prepare("
                UPDATE orders 
                SET notified = 1, updated_at = CURRENT_TIMESTAMP
                WHERE public_id IN ($placeholders) AND establishment_id = ?
            ");
            
            $params = array_merge($orderIds, [$establishment['id']]);
            $stmt->execute($params);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'updated' => $stmt->rowCount()]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function generateDailyOrderNumber(int $establishmentId): string
    {
        $today = date('Y-m-d');
        $maxRetries = 5;
        $retryCount = 0;
        
        while ($retryCount < $maxRetries) {
            try {
                $this->db->getPdo()->beginTransaction();
                
                // Get the last order number for today with FOR UPDATE to prevent race conditions
                $stmt = $this->db->getPdo()->prepare("
                    SELECT public_id FROM orders 
                    WHERE establishment_id = ? AND DATE(created_at) = ? 
                    ORDER BY id DESC LIMIT 1
                    FOR UPDATE
                ");
                $stmt->execute([$establishmentId, $today]);
                $lastOrder = $stmt->fetch();
                
                if ($lastOrder && is_numeric($lastOrder['public_id'])) {
                    $nextNumber = (int)$lastOrder['public_id'] + 1;
                } else {
                    $nextNumber = 1;
                }
                
                // Test if this number is already taken (double-check)
                $stmt = $this->db->getPdo()->prepare("
                    SELECT id FROM orders 
                    WHERE establishment_id = ? AND public_id = ? AND DATE(created_at) = ?
                ");
                $stmt->execute([$establishmentId, (string)$nextNumber, $today]);
                $existingOrder = $stmt->fetch();
                
                if ($existingOrder) {
                    // Number already exists, increment and try again
                    $nextNumber++;
                    $stmt = $this->db->getPdo()->prepare("
                        SELECT id FROM orders 
                        WHERE establishment_id = ? AND public_id = ? AND DATE(created_at) = ?
                    ");
                    $stmt->execute([$establishmentId, (string)$nextNumber, $today]);
                    $existingOrder = $stmt->fetch();
                    
                    if ($existingOrder) {
                        // Still exists, abort transaction and retry
                        $this->db->getPdo()->rollBack();
                        $retryCount++;
                        usleep(100000); // Wait 100ms before retry
                        continue;
                    }
                }
                
                $this->db->getPdo()->commit();
                return (string)$nextNumber;
                
            } catch (\Exception $e) {
                $this->db->getPdo()->rollBack();
                $retryCount++;
                usleep(100000); // Wait 100ms before retry
                error_log("Error generating order number (attempt {$retryCount}): " . $e->getMessage());
            }
        }
        
        // If all retries failed, fall back to timestamp-based ID
        return (string)(time() . rand(100, 999));
    }
}

