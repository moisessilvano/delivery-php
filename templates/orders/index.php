<?php
$title = 'Pedidos - ' . $establishment['name'];
$showNavbar = true;

?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pedidos</h1>
                <p class="text-gray-600">Gerencie os pedidos do seu estabelecimento</p>
            </div>
            <a href="/orders/create" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Novo Pedido
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div>
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-input">
                    <option value="">Todos os status</option>
                    <option value="pending" <?= $current_status === 'pending' ? 'selected' : '' ?>>Pendente</option>
                    <option value="confirmed" <?= $current_status === 'confirmed' ? 'selected' : '' ?>>Confirmado</option>
                    <option value="preparing" <?= $current_status === 'preparing' ? 'selected' : '' ?>>Preparando</option>
                    <option value="ready" <?= $current_status === 'ready' ? 'selected' : '' ?>>Pronto</option>
                    <option value="delivered" <?= $current_status === 'delivered' ? 'selected' : '' ?>>Entregue</option>
                    <option value="cancelled" <?= $current_status === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                </select>
            </div>
            
            <div>
                <label for="date" class="form-label">Data</label>
                <input type="date" id="date" name="date" value="<?= $current_date ?>" class="form-input">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-search mr-2"></i>Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Orders List -->
    <?php if (empty($orders)): ?>
    <div class="bg-white shadow rounded-lg p-12 text-center">
        <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum pedido encontrado</h3>
        <p class="text-gray-500 mb-6">Os pedidos aparecerão aqui quando chegarem</p>
        <a href="/orders/create" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Criar Primeiro Pedido
        </a>
    </div>
    <?php else: ?>
    <div class="space-y-4">
        <?php foreach ($orders as $order): ?>
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        Pedido #<?= $order['id'] ?>
                    </h3>
                    <p class="text-sm text-gray-500">
                        <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                    </p>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full <?= $this->getStatusColor($order['status']) ?>">
                        <?= $this->getStatusText($order['status']) ?>
                    </span>
                    
                    <div class="text-right">
                        <p class="text-lg font-semibold text-gray-900">
                            R$ <?= number_format($order['total_amount'], 2, ',', '.') ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <h4 class="font-medium text-gray-900">Cliente</h4>
                    <p class="text-gray-600"><?= htmlspecialchars($order['customer_name']) ?></p>
                    <p class="text-gray-600"><?= htmlspecialchars($order['customer_phone']) ?></p>
                </div>
                
                <?php if ($order['customer_address']): ?>
                <div>
                    <h4 class="font-medium text-gray-900">Endereço</h4>
                    <p class="text-gray-600"><?= htmlspecialchars($order['customer_address']) ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="mb-4">
                <h4 class="font-medium text-gray-900 mb-2">Itens do Pedido</h4>
                <div class="space-y-1">
                    <?php foreach ($order['items'] as $item): ?>
                    <div class="flex justify-between text-sm">
                        <span>
                            <?= $item['quantity'] ?>x <?= htmlspecialchars($item['product_name']) ?>
                            <?php 
                            $options = json_decode($item['options'], true);
                            if ($options && !empty($options)) {
                                echo ' (' . implode(', ', $options) . ')';
                            }
                            ?>
                        </span>
                        <span>R$ <?= number_format($item['product_price'] * $item['quantity'], 2, ',', '.') ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <?php if ($order['notes']): ?>
            <div class="mb-4">
                <h4 class="font-medium text-gray-900">Observações</h4>
                <p class="text-gray-600"><?= htmlspecialchars($order['notes']) ?></p>
            </div>
            <?php endif; ?>
            
            <div class="flex justify-end">
                <a href="/orders/<?= $order['id'] ?>" class="btn-primary">
                    <i class="fas fa-eye mr-2"></i>Ver Detalhes
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php


?>

<?php
// Helper functions for status display
function getStatusColor($status) {
    switch ($status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'confirmed': return 'bg-blue-100 text-blue-800';
        case 'preparing': return 'bg-orange-100 text-orange-800';
        case 'ready': return 'bg-green-100 text-green-800';
        case 'delivered': return 'bg-gray-100 text-gray-800';
        case 'cancelled': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function getStatusText($status) {
    switch ($status) {
        case 'pending': return 'Pendente';
        case 'confirmed': return 'Confirmado';
        case 'preparing': return 'Preparando';
        case 'ready': return 'Pronto';
        case 'delivered': return 'Entregue';
        case 'cancelled': return 'Cancelado';
        default: return 'Desconhecido';
    }
}
?>

