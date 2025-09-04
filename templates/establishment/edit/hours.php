<?php
$title = 'Editar Horários - ' . $establishment['name'];
$showNavbar = true;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Horários de Funcionamento</h1>
                <p class="text-gray-600">Configure os horários de funcionamento do seu estabelecimento</p>
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
                <a href="/profile/edit/delivery" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-truck mr-2"></i>Entrega
                </a>
                <a href="/profile/edit/hours" class="py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
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
        <!-- Operating Hours -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Horários de Funcionamento</h3>
            
            <?php
            $days = [
                'monday' => 'Segunda-feira',
                'tuesday' => 'Terça-feira', 
                'wednesday' => 'Quarta-feira',
                'thursday' => 'Quinta-feira',
                'friday' => 'Sexta-feira',
                'saturday' => 'Sábado',
                'sunday' => 'Domingo'
            ];
            
            // Parse existing hours from JSON or use defaults
            $operating_hours = [];
            if (!empty($establishment['operating_hours'])) {
                $operating_hours = json_decode($establishment['operating_hours'], true) ?: [];
            }
            ?>
            
            <div class="space-y-4">
                <?php foreach ($days as $day => $dayName): ?>
                <?php 
                $dayData = $operating_hours[$day] ?? [];
                // Handle both string "1" and boolean true for is_open
                $isOpen = isset($dayData['is_open']) ? (($dayData['is_open'] === "1" || $dayData['is_open'] === true || $dayData['is_open'] === 1)) : true;
                $openTime = $dayData['open_time'] ?? '08:00';
                $closeTime = $dayData['close_time'] ?? '22:00';
                ?>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-4">
                        <input type="checkbox" 
                               id="<?= $day ?>_open" 
                               name="operating_hours[<?= $day ?>][is_open]" 
                               value="1" 
                               <?= $isOpen ? 'checked' : '' ?>
                               class="form-checkbox day-checkbox">
                        <label for="<?= $day ?>_open" class="font-medium text-gray-900 w-32">
                            <?= $dayName ?>
                        </label>
                    </div>
                    
                    <div class="flex items-center space-x-4 day-times" style="display: <?= $isOpen ? 'flex' : 'none' ?>;">
                        <div>
                            <label class="text-sm text-gray-600">Abertura</label>
                            <input type="time" 
                                   name="operating_hours[<?= $day ?>][open_time]" 
                                   value="<?= $openTime ?>"
                                   class="form-input">
                        </div>
                        <span class="text-gray-500">até</span>
                        <div>
                            <label class="text-sm text-gray-600">Fechamento</label>
                            <input type="time" 
                                   name="operating_hours[<?= $day ?>][close_time]" 
                                   value="<?= $closeTime ?>"
                                   class="form-input">
                        </div>
                    </div>
                    
                    <div class="day-closed text-gray-500" style="display: <?= !$isOpen ? 'block' : 'none' ?>;">
                        Fechado
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium">Dicas:</p>
                        <ul class="mt-1 list-disc list-inside space-y-1">
                            <li>Desmarque a caixa para dias em que o estabelecimento não funciona</li>
                            <li>Para funcionamento 24h, coloque abertura 00:00 e fechamento 23:59</li>
                            <li>Os horários são exibidos para os clientes no seu perfil</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Special Hours -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Configurações Especiais</h3>
            <div class="space-y-4">
                <div>
                    <label for="special_hours_note" class="form-label">Observações sobre horários</label>
                    <textarea id="special_hours_note" name="special_hours_note" rows="3" 
                              class="form-input" 
                              placeholder="Ex: Feriados consultar, Delivery até mais tarde, etc."><?= htmlspecialchars($establishment['special_hours_note'] ?? '') ?></textarea>
                    <p class="text-sm text-gray-500 mt-1">Informações adicionais sobre horários especiais</p>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="24_hours" name="is_24_hours" value="1" 
                           <?= ($establishment['is_24_hours'] ?? 0) ? 'checked' : '' ?> 
                           class="form-checkbox">
                    <label for="24_hours" class="ml-3 text-sm text-gray-700">
                        Funcionamento 24 horas
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="holiday_hours" name="different_holiday_hours" value="1" 
                           <?= ($establishment['different_holiday_hours'] ?? 0) ? 'checked' : '' ?> 
                           class="form-checkbox">
                    <label for="holiday_hours" class="ml-3 text-sm text-gray-700">
                        Horários diferentes em feriados
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-between">
            <a href="/profile/edit/delivery" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Anterior: Entrega
            </a>
            <div class="flex space-x-4">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Salvar Horários
                </button>
                <a href="/profile/edit/design" class="btn-secondary">
                    Próximo: Personalização <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle day times based on checkbox
    const dayCheckboxes = document.querySelectorAll('.day-checkbox');
    
    dayCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const dayContainer = this.closest('.flex');
            const timesContainer = dayContainer.querySelector('.day-times');
            const closedContainer = dayContainer.querySelector('.day-closed');
            
            if (this.checked) {
                timesContainer.style.display = 'flex';
                closedContainer.style.display = 'none';
            } else {
                timesContainer.style.display = 'none';
                closedContainer.style.display = 'block';
            }
        });
    });
    
    // 24 hours toggle
    const twentyFourHours = document.getElementById('24_hours');
    if (twentyFourHours) {
        twentyFourHours.addEventListener('change', function() {
            const timeInputs = document.querySelectorAll('input[type="time"]');
            if (this.checked) {
                timeInputs.forEach(function(input) {
                    if (input.name.includes('open_time')) {
                        input.value = '00:00';
                    }
                    if (input.name.includes('close_time')) {
                        input.value = '23:59';
                    }
                });
                
                // Check all day checkboxes
                dayCheckboxes.forEach(function(checkbox) {
                    checkbox.checked = true;
                    checkbox.dispatchEvent(new Event('change'));
                });
            }
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