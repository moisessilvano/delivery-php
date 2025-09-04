<?php
$title = 'Editar Mídia e Contato - ' . $establishment['name'];
$showNavbar = true;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Mídia e Contato</h1>
                <p class="text-gray-600">Logo, fotos e redes sociais do estabelecimento</p>
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
                <a href="/profile/edit/contact" class="py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                    <i class="fas fa-images mr-2"></i>Mídia e Contato
                </a>
                <a href="/profile/edit/delivery" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
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
    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <!-- Images Section -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Imagens do Estabelecimento</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="logo" class="form-label">Logo</label>
                    <?php if (!empty($establishment['logo'])): ?>
                    <div class="mb-3">
                        <img src="/<?= htmlspecialchars($establishment['logo']) ?>" 
                             alt="Logo atual" class="w-24 h-24 object-cover rounded-lg border">
                        <p class="text-sm text-gray-500 mt-1">Logo atual</p>
                    </div>
                    <?php endif; ?>
                    <input type="file" id="logo" name="logo" accept="image/*" class="form-input">
                    <p class="text-sm text-gray-500 mt-1">Recomendado: imagem quadrada, mínimo 200x200px</p>
                </div>
                
                <div>
                    <label for="photo" class="form-label">Foto do Estabelecimento</label>
                    <?php if (!empty($establishment['photo'])): ?>
                    <div class="mb-3">
                        <img src="/<?= htmlspecialchars($establishment['photo']) ?>" 
                             alt="Foto atual" class="w-24 h-24 object-cover rounded-lg border">
                        <p class="text-sm text-gray-500 mt-1">Foto atual</p>
                    </div>
                    <?php endif; ?>
                    <input type="file" id="photo" name="photo" accept="image/*" class="form-input">
                    <p class="text-sm text-gray-500 mt-1">Foto que representa seu estabelecimento</p>
                </div>
            </div>
        </div>
        
        <!-- Contact & Social Section -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Contato e Redes Sociais</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="instagram" class="form-label">Instagram</label>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-3 py-2 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">@</span>
                        <input type="text" id="instagram" name="instagram" 
                               value="<?= htmlspecialchars($establishment['instagram'] ?? '') ?>" 
                               class="form-input rounded-l-none" 
                               placeholder="seuinstagram">
                    </div>
                </div>
                
                <div>
                    <label for="facebook" class="form-label">Facebook</label>
                    <div class="flex items-center">
                        <span class="inline-flex items-center px-3 py-2 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">facebook.com/</span>
                        <input type="text" id="facebook" name="facebook" 
                               value="<?= htmlspecialchars($establishment['facebook'] ?? '') ?>" 
                               class="form-input rounded-l-none" 
                               placeholder="seu.facebook">
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-between">
            <a href="/profile/edit/info" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Anterior: Informações
            </a>
            <div class="flex space-x-4">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Salvar Mídia e Contato
                </button>
                <a href="/profile/edit/delivery" class="btn-secondary">
                    Próximo: Entrega <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<script>
// Auto-hide messages after 5 seconds
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
</script>

<?php 
?>