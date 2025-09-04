<?php
$title = 'Perfil - ' . $establishment['name'];
$showNavbar = true;

?>

<div class="space-y-6">
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

    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Perfil do Estabelecimento</h1>
                <p class="text-gray-600">Gerencie as informações do seu estabelecimento</p>
            </div>
            <a href="/profile/edit/info" class="btn-primary">
                <i class="fas fa-edit mr-2"></i>Editar
            </a>
        </div>
    </div>

    <!-- Establishment Info -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-start space-x-6">
            <?php if (!empty($establishment['logo'])): ?>
            <img src="/<?= htmlspecialchars($establishment['logo'] ?? '') ?>" 
                 alt="Logo" class="w-24 h-24 object-cover rounded-lg">
            <?php else: ?>
            <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                <i class="fas fa-store text-2xl text-gray-400"></i>
            </div>
            <?php endif; ?>
            
            <div class="flex-1">
                <h2 class="text-xl font-semibold text-gray-900"><?= htmlspecialchars($establishment['name'] ?? '') ?></h2>
                <?php if (!empty($establishment['description'])): ?>
                <p class="text-gray-600 mt-2"><?= htmlspecialchars($establishment['description'] ?? '') ?></p>
                <?php endif; ?>
                
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php if (!empty($establishment['phone'])): ?>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-phone mr-2"></i>
                        <?= htmlspecialchars($establishment['phone'] ?? '') ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($establishment['whatsapp'])): ?>
                    <div class="flex items-center text-gray-600">
                        <i class="fab fa-whatsapp mr-2 text-green-600"></i>
                        <?= htmlspecialchars($establishment['whatsapp'] ?? '') ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($establishment['street_address']) || !empty($establishment['address'])): ?>
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        <?php 
                        // Build formatted address from detailed fields
                        $addressParts = [];
                        
                        if (!empty($establishment['street_address'])) {
                            $street = $establishment['street_address'];
                            if (!empty($establishment['number'])) {
                                $street .= ', ' . $establishment['number'];
                            }
                            $addressParts[] = $street;
                            
                            if (!empty($establishment['complement'])) {
                                $addressParts[] = $establishment['complement'];
                            }
                            
                            if (!empty($establishment['neighborhood'])) {
                                $addressParts[] = $establishment['neighborhood'];
                            }
                            
                            if (!empty($establishment['city'])) {
                                $addressParts[] = $establishment['city'];
                            }
                            
                            if (!empty($establishment['state'])) {
                                $addressParts[] = $establishment['state'];
                            }
                            
                            if (!empty($establishment['cep'])) {
                                $addressParts[] = 'CEP ' . $establishment['cep'];
                            }
                            
                            $formattedAddress = implode(', ', array_filter($addressParts));
                        } else {
                            // Fallback to old address field
                            $formattedAddress = $establishment['address'];
                        }
                        
                        echo htmlspecialchars($formattedAddress);
                        ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="flex items-center text-gray-600">
                        <i class="fas fa-clock mr-2"></i>
                        Tempo de entrega: <?= $establishment['delivery_time'] ?> min
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Hours -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Horários de Funcionamento</h3>
        <div class="space-y-2">
            <?php 
            $days = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
            foreach ($business_hours as $hour): 
            ?>
            <div class="flex items-center justify-between py-2 border-b border-gray-100">
                <span class="font-medium text-gray-700"><?= $days[$hour['day_of_week']] ?></span>
                <span class="text-gray-600">
                    <?php if ($hour['is_closed']): ?>
                        <span class="text-red-600">Fechado</span>
                    <?php else: ?>
                        <?= $hour['open_time'] ?> - <?= $hour['close_time'] ?>
                    <?php endif; ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Delivery Info -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Informações de Entrega</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Taxa de Entrega</label>
                <p class="text-lg font-semibold text-gray-900">
                    R$ <?= number_format($establishment['delivery_fee'], 2, ',', '.') ?>
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Valor Mínimo do Pedido</label>
                <p class="text-lg font-semibold text-gray-900">
                    R$ <?= number_format($establishment['min_order_value'], 2, ',', '.') ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Social Media -->
    <?php if (!empty($establishment['instagram']) || !empty($establishment['facebook'])): ?>
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Redes Sociais</h3>
        <div class="flex space-x-4">
            <?php if (!empty($establishment['instagram'])): ?>
            <a href="https://instagram.com/<?= htmlspecialchars($establishment['instagram'] ?? '') ?>" 
               target="_blank" class="flex items-center text-pink-600 hover:text-pink-700">
                <i class="fab fa-instagram mr-2"></i>
                @<?= htmlspecialchars($establishment['instagram'] ?? '') ?>
            </a>
            <?php endif; ?>
            
            <?php if (!empty($establishment['facebook'])): ?>
            <a href="https://facebook.com/<?= htmlspecialchars($establishment['facebook'] ?? '') ?>" 
               target="_blank" class="flex items-center text-blue-600 hover:text-blue-700">
                <i class="fab fa-facebook mr-2"></i>
                <?= htmlspecialchars($establishment['facebook'] ?? '') ?>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Public URL -->
    <div class="bg-primary-50 border border-primary-200 rounded-lg p-6">
        <h3 class="text-lg font-medium text-primary-900 mb-2">Seu Cardápio Público</h3>
        <p class="text-primary-700 mb-4">Seus clientes podem acessar seu cardápio através do link:</p>
        <div class="flex items-center space-x-2">
            <input type="text" 
                   value="https://<?= htmlspecialchars($establishment['subdomain'] ?? '') ?>.appzei.com" 
                   readonly 
                   class="flex-1 px-3 py-2 border border-primary-300 rounded-md bg-white text-primary-900">
            <button onclick="copyToClipboard(this.previousElementSibling)" 
                    class="btn-primary">
                <i class="fas fa-copy mr-2"></i>Copiar
            </button>
        </div>
    </div>
</div>

<script>
// Auto-hide success/error messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
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

function copyToClipboard(input) {
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    const button = input.nextElementSibling;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check mr-2"></i>Copiado!';
    button.classList.add('bg-green-600', 'hover:bg-green-700');
    button.classList.remove('bg-primary-600', 'hover:bg-primary-700');
    
    setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('bg-green-600', 'hover:bg-green-700');
        button.classList.add('bg-primary-600', 'hover:bg-primary-700');
    }, 2000);
}
</script>

<?php


?>

