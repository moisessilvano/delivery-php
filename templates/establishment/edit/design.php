<?php
$title = 'Editar Personalização - ' . $establishment['name'];
$showNavbar = true;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Personalização</h1>
                <p class="text-gray-600">Cores, categorias e configurações visuais do seu estabelecimento</p>
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
                <a href="/profile/edit/hours" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-clock mr-2"></i>Horários
                </a>
                <a href="/profile/edit/design" class="py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                    <i class="fas fa-palette mr-2"></i>Personalização
                </a>
            </nav>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" class="space-y-6">
    

        <!-- Colors Configuration -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-palette mr-2"></i>Personalização do Cardápio
            </h3>
            <p class="text-sm text-gray-600 mb-6">Configure as cores do seu cardápio online</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="primary_color" class="form-label">Cor Principal</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" id="primary_color" name="primary_color" 
                               value="<?= htmlspecialchars($establishment['primary_color'] ?? '#3B82F6') ?>"
                               class="w-12 h-10 rounded border border-gray-300">
                        <input type="text" 
                               value="<?= htmlspecialchars($establishment['primary_color'] ?? '#3B82F6') ?>"
                               class="form-input flex-1" readonly
                               id="primary_color_text">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Cor dos botões e destaques</p>
                </div>

                <div>
                    <label for="secondary_color" class="form-label">Cor Secundária</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" id="secondary_color" name="secondary_color" 
                               value="<?= htmlspecialchars($establishment['secondary_color'] ?? '#1E40AF') ?>"
                               class="w-12 h-10 rounded border border-gray-300">
                        <input type="text" 
                               value="<?= htmlspecialchars($establishment['secondary_color'] ?? '#1E40AF') ?>"
                               class="form-input flex-1" readonly
                               id="secondary_color_text">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Cor de hover e elementos secundários</p>
                </div>
            </div>

            <!-- Color Preview -->
            <div class="mt-6 p-4 border border-gray-200 rounded-lg" id="color-preview">
                <h4 class="font-medium mb-2">Prévia das Cores</h4>
                <div class="flex items-center space-x-4">
                    <button type="button" class="px-4 py-2 rounded text-white font-medium" id="preview-primary">
                        Botão Principal
                    </button>
                    <button type="button" class="px-4 py-2 rounded text-white font-medium" id="preview-secondary">
                        Botão Secundário
                    </button>
                </div>
            </div>

            <!-- Preset Colors -->
            <div class="mt-6">
                <h4 class="font-medium mb-3">Esquemas Pré-definidos</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <button type="button" class="color-preset p-3 border rounded-lg hover:border-blue-500 transition-colors"
                            data-primary="#3B82F6" data-secondary="#1E40AF">
                        <div class="flex space-x-1 mb-2">
                            <div class="w-4 h-4 rounded" style="background: #3B82F6"></div>
                            <div class="w-4 h-4 rounded" style="background: #1E40AF"></div>
                        </div>
                        <span class="text-xs">Azul Clássico</span>
                    </button>

                    <button type="button" class="color-preset p-3 border rounded-lg hover:border-blue-500 transition-colors"
                            data-primary="#EF4444" data-secondary="#DC2626">
                        <div class="flex space-x-1 mb-2">
                            <div class="w-4 h-4 rounded" style="background: #EF4444"></div>
                            <div class="w-4 h-4 rounded" style="background: #DC2626"></div>
                        </div>
                        <span class="text-xs">Vermelho</span>
                    </button>

                    <button type="button" class="color-preset p-3 border rounded-lg hover:border-blue-500 transition-colors"
                            data-primary="#10B981" data-secondary="#059669">
                        <div class="flex space-x-1 mb-2">
                            <div class="w-4 h-4 rounded" style="background: #10B981"></div>
                            <div class="w-4 h-4 rounded" style="background: #059669"></div>
                        </div>
                        <span class="text-xs">Verde</span>
                    </button>

                    <button type="button" class="color-preset p-3 border rounded-lg hover:border-blue-500 transition-colors"
                            data-primary="#F59E0B" data-secondary="#D97706">
                        <div class="flex space-x-1 mb-2">
                            <div class="w-4 h-4 rounded" style="background: #F59E0B"></div>
                            <div class="w-4 h-4 rounded" style="background: #D97706"></div>
                        </div>
                        <span class="text-xs">Laranja</span>
                    </button>

                    <button type="button" class="color-preset p-3 border rounded-lg hover:border-blue-500 transition-colors"
                            data-primary="#8B5CF6" data-secondary="#7C3AED">
                        <div class="flex space-x-1 mb-2">
                            <div class="w-4 h-4 rounded" style="background: #8B5CF6"></div>
                            <div class="w-4 h-4 rounded" style="background: #7C3AED"></div>
                        </div>
                        <span class="text-xs">Roxo</span>
                    </button>

                    <button type="button" class="color-preset p-3 border rounded-lg hover:border-blue-500 transition-colors"
                            data-primary="#EC4899" data-secondary="#DB2777">
                        <div class="flex space-x-1 mb-2">
                            <div class="w-4 h-4 rounded" style="background: #EC4899"></div>
                            <div class="w-4 h-4 rounded" style="background: #DB2777"></div>
                        </div>
                        <span class="text-xs">Rosa</span>
                    </button>

                    <button type="button" class="color-preset p-3 border rounded-lg hover:border-blue-500 transition-colors"
                            data-primary="#06B6D4" data-secondary="#0891B2">
                        <div class="flex space-x-1 mb-2">
                            <div class="w-4 h-4 rounded" style="background: #06B6D4"></div>
                            <div class="w-4 h-4 rounded" style="background: #0891B2"></div>
                        </div>
                        <span class="text-xs">Ciano</span>
                    </button>

                    <button type="button" class="color-preset p-3 border rounded-lg hover:border-blue-500 transition-colors"
                            data-primary="#84CC16" data-secondary="#65A30D">
                        <div class="flex space-x-1 mb-2">
                            <div class="w-4 h-4 rounded" style="background: #84CC16"></div>
                            <div class="w-4 h-4 rounded" style="background: #65A30D"></div>
                        </div>
                        <span class="text-xs">Lima</span>
                    </button>

                    <button type="button" class="color-preset p-3 border rounded-lg hover:border-blue-500 transition-colors"
                            data-primary="#F97316" data-secondary="#EA580C">
                        <div class="flex space-x-1 mb-2">
                            <div class="w-4 h-4 rounded" style="background: #F97316"></div>
                            <div class="w-4 h-4 rounded" style="background: #EA580C"></div>
                        </div>
                        <span class="text-xs">Laranja Vibrante</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-between">
            <a href="/profile/edit/hours" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Anterior: Horários
            </a>
            <div class="flex space-x-4">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Salvar Personalização
                </button>
                <a href="/profile" class="btn-secondary">
                    Finalizar e Ver Perfil <i class="fas fa-eye ml-2"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Color picker functionality
    document.querySelectorAll('input[type="color"]').forEach(colorInput => {
        const textInput = document.getElementById(colorInput.id + '_text');
        
        colorInput.addEventListener('input', function() {
            textInput.value = this.value;
            updateColorPreview();
        });
    });

    // Color preset functionality
    document.querySelectorAll('.color-preset').forEach(preset => {
        preset.addEventListener('click', function() {
            const primary = this.dataset.primary;
            const secondary = this.dataset.secondary;
            
            document.getElementById('primary_color').value = primary;
            document.getElementById('primary_color_text').value = primary;
            document.getElementById('secondary_color').value = secondary;
            document.getElementById('secondary_color_text').value = secondary;
            
            updateColorPreview();
        });
    });

    // Update color preview
    function updateColorPreview() {
        const primary = document.getElementById('primary_color').value;
        const secondary = document.getElementById('secondary_color').value;
        
        document.getElementById('preview-primary').style.backgroundColor = primary;
        document.getElementById('preview-secondary').style.backgroundColor = secondary;
    }

    // Initialize color preview
    updateColorPreview();
    
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