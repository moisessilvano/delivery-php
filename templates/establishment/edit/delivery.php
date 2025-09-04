<?php
$title = 'Editar Entrega - ' . $establishment['name'];
$showNavbar = true;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Configurações de Entrega</h1>
                <p class="text-gray-600">Taxa de entrega, tempo e área de cobertura</p>
            </div>
            <a href="/profile" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Voltar ao Perfil
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-600 mr-2"></i>
            <span class="text-green-800"><?= htmlspecialchars(urldecode($_GET['success'])) ?></span>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
            <span class="text-red-800"><?= htmlspecialchars(urldecode($_GET['error'])) ?></span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6">
                <a href="/profile/edit/info" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-info-circle mr-2"></i>Informações Básicas
                </a>
                <a href="/profile/edit/contact" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-images mr-2"></i>Mídia e Contato
                </a>
                <a href="/profile/edit/delivery" class="py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                    <i class="fas fa-truck mr-2"></i>Entrega
                </a>
                <a href="/profile/edit/hours" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-clock mr-2"></i>Horários
                </a>
                <a href="/profile/edit/design" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-palette mr-2"></i>Personalização
                </a>
            </nav>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" class="space-y-6">
        <!-- Delivery Settings -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Configurações Básicas de Entrega</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="delivery_fee" class="form-label">Taxa de Entrega (R$)</label>
                    <input type="number" id="delivery_fee" name="delivery_fee" 
                           value="<?= htmlspecialchars($establishment['delivery_fee'] ?? '') ?>" 
                           class="form-input" step="0.01" min="0" placeholder="5.00">
                    <p class="text-sm text-gray-500 mt-1">Taxa padrão de entrega em reais</p>
                </div>
                
                <div>
                    <label for="delivery_time" class="form-label">Tempo de Entrega (minutos)</label>
                    <input type="number" id="delivery_time" name="delivery_time" 
                           value="<?= htmlspecialchars($establishment['delivery_time'] ?? '') ?>" 
                           class="form-input" min="1" placeholder="30">
                    <p class="text-sm text-gray-500 mt-1">Tempo estimado em minutos</p>
                </div>
                
                <div>
                    <label for="min_order_value" class="form-label">Pedido Mínimo (R$)</label>
                    <input type="number" id="min_order_value" name="min_order_value" 
                           value="<?= htmlspecialchars($establishment['min_order_value'] ?? '') ?>" 
                           class="form-input" step="0.01" min="0" placeholder="20.00">
                    <p class="text-sm text-gray-500 mt-1">Valor mínimo para entrega</p>
                </div>
                
                <div>
                    <label for="max_delivery_distance" class="form-label">Distância Máxima (km)</label>
                    <input type="number" id="max_delivery_distance" name="max_delivery_distance" 
                           value="<?= htmlspecialchars($establishment['max_delivery_distance'] ?? '') ?>" 
                           class="form-input" step="0.1" min="0" placeholder="5.0">
                    <p class="text-sm text-gray-500 mt-1">Raio máximo de entrega em quilômetros</p>
                </div>
            </div>
        </div>

        <!-- Delivery Options -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Opções de Entrega</h3>
            <div class="space-y-4">
                <div class="flex items-center">
                    <input type="checkbox" id="accepts_delivery" name="accepts_delivery" value="1" 
                           <?= ($establishment['accepts_delivery'] ?? 0) ? 'checked' : '' ?> 
                           class="form-checkbox">
                    <label for="accepts_delivery" class="ml-3 text-sm text-gray-700">
                        Aceitar pedidos para entrega
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="accepts_pickup" name="accepts_pickup" value="1" 
                           <?= ($establishment['accepts_pickup'] ?? 0) ? 'checked' : '' ?> 
                           class="form-checkbox">
                    <label for="accepts_pickup" class="ml-3 text-sm text-gray-700">
                        Aceitar pedidos para retirada no local
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="free_delivery_above" name="free_delivery_above" value="1" 
                           <?= ($establishment['free_delivery_above'] ?? 0) ? 'checked' : '' ?> 
                           class="form-checkbox">
                    <label for="free_delivery_above" class="ml-3 text-sm text-gray-700">
                        Entrega grátis acima de um valor
                    </label>
                </div>
                
                <div id="free_delivery_value_div" class="ml-6 mt-2" style="display: <?= ($establishment['free_delivery_above'] ?? 0) ? 'block' : 'none' ?>;">
                    <label for="free_delivery_value" class="form-label">Valor para entrega grátis (R$)</label>
                    <input type="number" id="free_delivery_value" name="free_delivery_value" 
                           value="<?= htmlspecialchars($establishment['free_delivery_value'] ?? '') ?>" 
                           class="form-input" step="0.01" min="0" placeholder="50.00">
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Formas de Pagamento</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center">
                    <input type="checkbox" id="payment_cash" name="payment_methods[]" value="cash" 
                           <?= in_array('cash', explode(',', $establishment['payment_methods'] ?? '')) ? 'checked' : '' ?> 
                           class="form-checkbox">
                    <label for="payment_cash" class="ml-3 text-sm text-gray-700">
                        <i class="fas fa-money-bill mr-2"></i>Dinheiro
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="payment_card" name="payment_methods[]" value="card" 
                           <?= in_array('card', explode(',', $establishment['payment_methods'] ?? '')) ? 'checked' : '' ?> 
                           class="form-checkbox">
                    <label for="payment_card" class="ml-3 text-sm text-gray-700">
                        <i class="fas fa-credit-card mr-2"></i>Cartão (Débito/Crédito)
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="payment_pix" name="payment_methods[]" value="pix" 
                           <?= in_array('pix', explode(',', $establishment['payment_methods'] ?? '')) ? 'checked' : '' ?> 
                           class="form-checkbox">
                    <label for="payment_pix" class="ml-3 text-sm text-gray-700">
                        <i class="fas fa-qrcode mr-2"></i>PIX
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="payment_online" name="payment_methods[]" value="online" 
                           <?= in_array('online', explode(',', $establishment['payment_methods'] ?? '')) ? 'checked' : '' ?> 
                           class="form-checkbox">
                    <label for="payment_online" class="ml-3 text-sm text-gray-700">
                        <i class="fas fa-laptop mr-2"></i>Pagamento Online
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-between">
            <a href="/profile/edit/contact" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Anterior: Mídia e Contato
            </a>
            <div class="flex space-x-4">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Salvar Configurações de Entrega
                </button>
                <a href="/profile/edit/hours" class="btn-secondary">
                    Próximo: Horários <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle free delivery value input
    const freeDeliveryCheckbox = document.getElementById('free_delivery_above');
    const freeDeliveryValueDiv = document.getElementById('free_delivery_value_div');
    
    if (freeDeliveryCheckbox) {
        freeDeliveryCheckbox.addEventListener('change', function() {
            freeDeliveryValueDiv.style.display = this.checked ? 'block' : 'none';
        });
    }
    
    // Auto-hide messages after 5 seconds
    const alerts = document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"]');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });
});
</script>

<?php 
?>