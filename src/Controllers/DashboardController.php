<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        // Get statistics
        $stats = $this->getDashboardStats($establishment['id']);
        
        // Get setup progress
        $setupProgress = $this->getSetupProgress($establishment);

        $this->view('dashboard/index', [
            'establishment' => $establishment,
            'stats' => $stats,
            'setup_progress' => $setupProgress
        ]);
    }

    private function getDashboardStats(int $establishmentId): array
    {
        // Total products
        $stmt = $this->db->getPdo()->prepare("SELECT COUNT(*) as total FROM products WHERE establishment_id = ?");
        $stmt->execute([$establishmentId]);
        $totalProducts = $stmt->fetch()['total'];

        // Total categories
        $stmt = $this->db->getPdo()->prepare("SELECT COUNT(*) as total FROM categories WHERE establishment_id = ?");
        $stmt->execute([$establishmentId]);
        $totalCategories = $stmt->fetch()['total'];

        // Today's orders
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(*) as total, COALESCE(SUM(total_amount), 0) as revenue 
            FROM orders 
            WHERE establishment_id = ? AND DATE(created_at) = DATE('now')
        ");
        $stmt->execute([$establishmentId]);
        $todayStats = $stmt->fetch();

        // Pending orders
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(*) as total 
            FROM orders 
            WHERE establishment_id = ? AND status IN ('pending', 'confirmed', 'preparing')
        ");
        $stmt->execute([$establishmentId]);
        $pendingOrders = $stmt->fetch()['total'];

        return [
            'total_products' => $totalProducts,
            'total_categories' => $totalCategories,
            'today_orders' => $todayStats['total'],
            'today_revenue' => $todayStats['revenue'],
            'pending_orders' => $pendingOrders
        ];
    }

    private function getSetupProgress(array $establishment): array
    {
        $steps = [];
        $completedSteps = 0;
        $totalSteps = 6;

        // Step 1: Basic establishment info
        $hasBasicInfo = !empty($establishment['name']) && !empty($establishment['phone']);
        $steps[] = [
            'id' => 'basic_info',
            'title' => 'Informações Básicas',
            'description' => 'Nome do estabelecimento e telefone',
            'completed' => $hasBasicInfo,
            'action_url' => '/profile/edit/info',
            'action_text' => $hasBasicInfo ? 'Editar' : 'Configurar',
            'icon' => 'fas fa-store',
            'priority' => 1
        ];
        if ($hasBasicInfo) $completedSteps++;

        // Step 2: Address
        $hasAddress = !empty($establishment['street_address']) || !empty($establishment['address']);
        $steps[] = [
            'id' => 'address',
            'title' => 'Endereço Completo',
            'description' => 'Endereço para entrega e localização',
            'completed' => $hasAddress,
            'action_url' => '/profile/edit/info',
            'action_text' => $hasAddress ? 'Editar' : 'Adicionar',
            'icon' => 'fas fa-map-marker-alt',
            'priority' => 2
        ];
        if ($hasAddress) $completedSteps++;

        // Step 3: Payment methods
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(*) as total FROM payment_methods 
            WHERE establishment_id = ? AND is_active = 1
        ");
        $stmt->execute([$establishment['id']]);
        $hasPaymentMethods = $stmt->fetch()['total'] > 0;
        
        $steps[] = [
            'id' => 'payment_methods',
            'title' => 'Formas de Pagamento',
            'description' => 'Configure as formas de pagamento aceitas',
            'completed' => $hasPaymentMethods,
            'action_url' => '/payment-methods',
            'action_text' => $hasPaymentMethods ? 'Gerenciar' : 'Configurar',
            'icon' => 'fas fa-credit-card',
            'priority' => 3
        ];
        if ($hasPaymentMethods) $completedSteps++;

        // Step 4: Business hours
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(*) as total FROM business_hours 
            WHERE establishment_id = ?
        ");
        $stmt->execute([$establishment['id']]);
        $hasBusinessHours = $stmt->fetch()['total'] > 0;
        
        $steps[] = [
            'id' => 'business_hours',
            'title' => 'Horários de Funcionamento',
            'description' => 'Defina quando seu estabelecimento funciona',
            'completed' => $hasBusinessHours,
            'action_url' => '/profile/edit/hours',
            'action_text' => $hasBusinessHours ? 'Editar' : 'Definir',
            'icon' => 'fas fa-clock',
            'priority' => 4
        ];
        if ($hasBusinessHours) $completedSteps++;

        // Step 5: Categories
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(*) as total FROM categories 
            WHERE establishment_id = ? AND is_active = 1
        ");
        $stmt->execute([$establishment['id']]);
        $hasCategories = $stmt->fetch()['total'] > 0;
        
        $steps[] = [
            'id' => 'categories',
            'title' => 'Categorias',
            'description' => 'Organize seus produtos em categorias',
            'completed' => $hasCategories,
            'action_url' => '/categories',
            'action_text' => $hasCategories ? 'Gerenciar' : 'Criar Primeira',
            'icon' => 'fas fa-tags',
            'priority' => 5
        ];
        if ($hasCategories) $completedSteps++;

        // Step 6: Products
        $stmt = $this->db->getPdo()->prepare("
            SELECT COUNT(*) as total FROM products 
            WHERE establishment_id = ? AND is_available = 1
        ");
        $stmt->execute([$establishment['id']]);
        $hasProducts = $stmt->fetch()['total'] > 0;
        
        $steps[] = [
            'id' => 'products',
            'title' => 'Produtos',
            'description' => 'Adicione produtos ao seu cardápio',
            'completed' => $hasProducts,
            'action_url' => '/products',
            'action_text' => $hasProducts ? 'Gerenciar' : 'Adicionar Primeiro',
            'icon' => 'fas fa-utensils',
            'priority' => 6
        ];
        if ($hasProducts) $completedSteps++;

        // Sort by priority (incomplete first, then by priority)
        usort($steps, function($a, $b) {
            if ($a['completed'] != $b['completed']) {
                return $a['completed'] ? 1 : -1; // Incomplete first
            }
            return $a['priority'] - $b['priority'];
        });

        return [
            'steps' => $steps,
            'completed_count' => $completedSteps,
            'total_count' => $totalSteps,
            'percentage' => round(($completedSteps / $totalSteps) * 100),
            'is_complete' => $completedSteps === $totalSteps
        ];
    }
}

