<?php

namespace App\Services;

use App\Core\Database;

class PushNotificationService
{
    private Database $db;
    private string $vapidPublicKey;
    private string $vapidPrivateKey;
    private string $vapidSubject;

    public function __construct(Database $db)
    {
        $this->db = $db;
        
        // TODO: Generate VAPID keys and add to environment
        // You can generate them using: https://web-push-codelab.glitch.me/
        $this->vapidPublicKey = $_ENV['VAPID_PUBLIC_KEY'] ?? 'YOUR_VAPID_PUBLIC_KEY';
        $this->vapidPrivateKey = $_ENV['VAPID_PRIVATE_KEY'] ?? 'YOUR_VAPID_PRIVATE_KEY';
        $this->vapidSubject = $_ENV['VAPID_SUBJECT'] ?? 'mailto:admin@comida-sm.com';
    }

    /**
     * Send notification to all subscribers of an establishment
     */
    public function sendToEstablishment(int $establishmentId, string $title, string $message, array $data = []): bool
    {
        try {
            $stmt = $this->db->getPdo()->prepare("
                SELECT * FROM push_subscriptions 
                WHERE establishment_id = ? AND is_active = 1
            ");
            $stmt->execute([$establishmentId]);
            $subscriptions = $stmt->fetchAll();

            $success = true;
            foreach ($subscriptions as $subscription) {
                if (!$this->sendPushNotification($subscription, $title, $message, $data)) {
                    $success = false;
                }
            }

            return $success;
        } catch (\Exception $e) {
            error_log("Error sending notifications to establishment $establishmentId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to specific customer
     */
    public function sendToCustomer(int $establishmentId, string $customerPhone, string $title, string $message, array $data = []): bool
    {
        try {
            $stmt = $this->db->getPdo()->prepare("
                SELECT * FROM push_subscriptions 
                WHERE establishment_id = ? AND customer_phone = ? AND is_active = 1
            ");
            $stmt->execute([$establishmentId, $customerPhone]);
            $subscriptions = $stmt->fetchAll();

            $success = true;
            foreach ($subscriptions as $subscription) {
                if (!$this->sendPushNotification($subscription, $title, $message, $data)) {
                    $success = false;
                }
            }

            return $success;
        } catch (\Exception $e) {
            error_log("Error sending notification to customer $customerPhone: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send order status update notification
     */
    public function sendOrderStatusUpdate(string $orderPublicId, string $newStatus): bool
    {
        try {
            // Get order details
            $stmt = $this->db->getPdo()->prepare("
                SELECT o.*, e.name as establishment_name
                FROM orders o
                JOIN establishments e ON o.establishment_id = e.id
                WHERE o.public_id = ?
            ");
            $stmt->execute([$orderPublicId]);
            $order = $stmt->fetch();

            if (!$order) {
                return false;
            }

            $statusMessages = [
                'pending' => 'Seu pedido foi recebido e está sendo processado.',
                'preparing' => 'Seu pedido está sendo preparado!',
                'ready' => 'Seu pedido está pronto para entrega!',
                'delivering' => 'Seu pedido saiu para entrega!',
                'delivered' => 'Seu pedido foi entregue com sucesso!',
                'cancelled' => 'Seu pedido foi cancelado.'
            ];

            $message = $statusMessages[$newStatus] ?? 'Status do pedido atualizado.';
            $title = "Pedido #{$order['public_id']}" . " - " . substr($order['public_id'], -8);

            $data = [
                'url' => "/order-success/{$order['public_id']}",
                'tag' => "order-{$order['public_id']}",
                'order_id' => $order['public_id'],
                'status' => $newStatus
            ];

            return $this->sendToCustomer(
                $order['establishment_id'],
                $order['customer_phone'],
                $title,
                $message,
                $data
            );

        } catch (\Exception $e) {
            error_log("Error sending order status notification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send push notification to a specific subscription
     */
    private function sendPushNotification(array $subscription, string $title, string $message, array $data = []): bool
    {
        try {
            $payload = json_encode([
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'icon' => '/icons/icon-192x192.png',
                'badge' => '/icons/badge-72x72.png',
                'tag' => $data['tag'] ?? 'general',
                'requireInteraction' => true,
                'actions' => [
                    [
                        'action' => 'view',
                        'title' => 'Ver Detalhes'
                    ],
                    [
                        'action' => 'dismiss',
                        'title' => 'Dispensar'
                    ]
                ]
            ]);

            // In a real implementation, you would use a library like web-push/web-push-php
            // For now, we'll simulate the notification send
            $this->simulatePushSend($subscription, $payload);
            
            return true;

        } catch (\Exception $e) {
            error_log("Error sending push notification: " . $e->getMessage());
            
            // Mark subscription as inactive if endpoint is invalid
            $this->markSubscriptionInactive($subscription['id']);
            
            return false;
        }
    }

    /**
     * Simulate push notification send (for development)
     */
    private function simulatePushSend(array $subscription, string $payload): void
    {
        // In development, just log the notification
        error_log("PUSH NOTIFICATION: {$subscription['endpoint']} - {$payload}");
        
        // TODO: Implement real push notification using web-push library
        // Example:
        /*
        $webPush = new \Minishlink\WebPush\WebPush([
            'VAPID' => [
                'subject' => $this->vapidSubject,
                'publicKey' => $this->vapidPublicKey,
                'privateKey' => $this->vapidPrivateKey,
            ],
        ]);

        $webPush->sendOneNotification(
            \Minishlink\WebPush\Subscription::create([
                'endpoint' => $subscription['endpoint'],
                'keys' => [
                    'p256dh' => $subscription['p256dh'],
                    'auth' => $subscription['auth'],
                ],
            ]),
            $payload
        );
        */
    }

    /**
     * Mark subscription as inactive
     */
    private function markSubscriptionInactive(int $subscriptionId): void
    {
        try {
            $stmt = $this->db->getPdo()->prepare("
                UPDATE push_subscriptions 
                SET is_active = 0, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?
            ");
            $stmt->execute([$subscriptionId]);
        } catch (\Exception $e) {
            error_log("Error marking subscription inactive: " . $e->getMessage());
        }
    }

    /**
     * Clean up old inactive subscriptions
     */
    public function cleanupOldSubscriptions(): void
    {
        try {
            $stmt = $this->db->getPdo()->prepare("
                DELETE FROM push_subscriptions 
                WHERE is_active = 0 AND updated_at < date('now', '-30 days')
            ");
            $stmt->execute();
        } catch (\Exception $e) {
            error_log("Error cleaning up old subscriptions: " . $e->getMessage());
        }
    }
}
