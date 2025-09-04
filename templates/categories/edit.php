<?php
$title = 'Editar Categoria - ' . $category['name'];
$showNavbar = true;

?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Categoria</h1>
                <p class="text-gray-600">Atualize as informações da categoria</p>
            </div>
            <a href="/categories" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Voltar
            </a>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="space-y-6">
                <div>
                    <label for="name" class="form-label">Nome da Categoria *</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($category['name']) ?>" 
                           required class="form-input">
                </div>
                
            
            </div>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="/categories" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save mr-2"></i>Salvar Alterações
            </button>
        </div>
    </form>
</div>

<?php


?>

