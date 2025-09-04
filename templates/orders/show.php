<?php
$title = 'Pedido #' . $order['id'] . ' - ' . $establishment['name'];
$showNavbar = true;

?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pedido #<?= $order['id'] ?></h1>
                <p class="text-gray-600">
                    Criado em <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                </p>
            </div>
            <a href="/orders" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Voltar
            </a>
        </div>
    </div>

    <!-- Order Status -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Status do Pedido</h3>
        
        <form method="POST" class="flex items-center space-x-4">
            <select name="status" class="form-input" style="width: auto;">
                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pendente</option>
                <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmado</option>
                <option value="preparing" <?= $order['status'] === 'preparing' ? 'selected' : '' ?>>Preparando</option>
                <option value="ready" <?= $order['status'] === 'ready' ? 'selected' : '' ?>>Pronto</option>
                <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Entregue</option>
                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
            </select>
            
            <button type="submit" class="btn-primary">
                <i class="fas fa-save mr-2"></i>Atualizar Status
            </button>
        </form>
    </div>

    <!-- Customer Info -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?= ($order['delivery_type'] ?? 'delivery') === 'pickup' ? 'Informações de Retirada' : 'Informações de Entrega' ?>
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-700">Nome</h4>
                <p class="text-gray-900"><?= htmlspecialchars($order['customer_name']) ?></p>
            </div>
            
            <div>
                <h4 class="font-medium text-gray-700">Telefone</h4>
                <p class="text-gray-900"><?= htmlspecialchars($order['customer_phone']) ?></p>
            </div>
            
            <?php if (($order['delivery_type'] ?? 'delivery') === 'pickup'): ?>
            <div class="md:col-span-2">
                <h4 class="font-medium text-gray-700">Tipo de Pedido</h4>
                <p class="text-gray-900">
                    <i class="fas fa-store mr-2"></i>Retirada no Local
                </p>
            </div>
            <?php elseif ($order['customer_address']): ?>
            <div class="md:col-span-2">
                <h4 class="font-medium text-gray-700">Endereço de Entrega</h4>
                <p class="text-gray-900"><?= htmlspecialchars($order['customer_address']) ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Itens do Pedido</h3>
        <div class="space-y-4">
            <?php foreach ($order['items'] as $item): ?>
            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                <div class="flex-1">
                    <h4 class="font-medium text-gray-900"><?= htmlspecialchars($item['product_name']) ?></h4>
                    <p class="text-sm text-gray-600">Quantidade: <?= $item['quantity'] ?></p>
                    <?php 
                    $options = json_decode($item['options'], true);
                    if ($options && !empty($options)) {
                        echo '<p class="text-sm text-gray-600">Opções: ' . implode(', ', $options) . '</p>';
                    }
                    ?>
                </div>
                <div class="text-right">
                    <p class="font-medium text-gray-900">
                        R$ <?= number_format($item['product_price'] * $item['quantity'], 2, ',', '.') ?>
                    </p>
                    <p class="text-sm text-gray-600">
                        R$ <?= number_format($item['product_price'], 2, ',', '.') ?> cada
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-6 pt-4 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <span class="text-lg font-medium text-gray-900">Total:</span>
                <span class="text-xl font-bold text-gray-900">
                    R$ <?= number_format($order['total_amount'], 2, ',', '.') ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Notes -->
    <?php if ($order['notes']): ?>
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Observações</h3>
        <p class="text-gray-700"><?= htmlspecialchars($order['notes']) ?></p>
    </div>
    <?php endif; ?>

    <!-- Order Timeline -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Histórico do Pedido</h3>
        <div class="space-y-4">
            <div class="flex items-center space-x-3">
                <div class="w-3 h-3 bg-primary-600 rounded-full"></div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Pedido criado</p>
                    <p class="text-xs text-gray-500"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                </div>
            </div>
            
            <?php if ($order['updated_at'] !== $order['created_at']): ?>
            <div class="flex items-center space-x-3">
                <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Última atualização</p>
                    <p class="text-xs text-gray-500"><?= date('d/m/Y H:i', strtotime($order['updated_at'])) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php


?>

