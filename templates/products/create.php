<?php
$title = 'Novo Produto - ' . $establishment['name'];
$showNavbar = true;

?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Novo Produto</h1>
                <p class="text-gray-600">Adicione um novo produto ao seu cardápio</p>
            </div>
            <a href="/products" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Voltar
            </a>
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <!-- Basic Info -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Informações Básicas</h3>
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="form-label">Nome do Produto *</label>
                        <input type="text" id="name" name="name" required 
                               class="form-input" placeholder="Ex: X-Burger">
                    </div>
                    
                    <div>
                        <label for="category_id" class="form-label">Categoria *</label>
                        <select id="category_id" name="category_id" required class="form-input">
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label for="description" class="form-label">Descrição</label>
                    <textarea id="description" name="description" rows="3" 
                              class="form-input" placeholder="Descreva o produto"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="price" class="form-label">Preço *</label>
                        <input type="text" id="price" name="price" required 
                               class="form-input money-mask" placeholder="R$ 0,00">
                    </div>
                    
                    <div>
                        <label for="sort_order" class="form-label">Ordem de Exibição</label>
                        <input type="number" id="sort_order" name="sort_order" value="0" 
                               class="form-input" min="0">
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="is_available" name="is_available" checked 
                               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <label for="is_available" class="ml-2 text-sm text-gray-700">
                            Produto disponível
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Imagem do Produto</h3>
            <div>
                <label for="image" class="form-label">Imagem</label>
                <input type="file" id="image" name="image" accept="image/*" class="form-input">
                <p class="text-sm text-gray-500 mt-1">Formatos aceitos: JPG, PNG, GIF, WebP (máx. 5MB)</p>
            </div>
        </div>

        <!-- Product Customization Groups -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Customizações do Produto</h3>
            <p class="text-sm text-gray-600 mb-4">Crie grupos de opções como Tamanhos, Sabores, Complementos, etc.</p>
            
            <div id="customization-groups-container">
                <!-- Groups will be added here -->
            </div>
            
            <button type="button" id="add-customization-group" class="btn-secondary">
                <i class="fas fa-plus mr-2"></i>Adicionar Grupo de Customização
            </button>
        </div>

        <div class="flex justify-end space-x-4">
            <a href="/products" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save mr-2"></i>Criar Produto
            </button>
        </div>
    </form>
</div>

<script>
let groupIndex = 0;

// Add customization group
document.getElementById('add-customization-group').addEventListener('click', function(e) {
    e.preventDefault();
    addCustomizationGroup();
});

// Use event delegation for dynamically added elements
document.addEventListener('click', function(e) {
    // Handle add option button clicks
    if (e.target.closest('.add-option')) {
        e.preventDefault();
        const button = e.target.closest('.add-option');
        const groupIdx = parseInt(button.getAttribute('data-group'));
        addOption(groupIdx);
    }
    
    // Handle remove option button clicks
    if (e.target.closest('.remove-option')) {
        e.preventDefault();
        const optionDiv = e.target.closest('.option-item');
        optionDiv.remove();
    }
    
    // Handle remove group button clicks
    if (e.target.closest('.remove-group')) {
        e.preventDefault();
        const groupDiv = e.target.closest('.customization-group');
        groupDiv.remove();
    }
});

function addCustomizationGroup() {
    const container = document.getElementById('customization-groups-container');
    const groupDiv = document.createElement('div');
    groupDiv.className = 'customization-group border border-gray-300 rounded-lg p-6 mb-6';
    groupDiv.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-lg font-medium text-gray-900">Grupo de Customização ${groupIndex + 1}</h4>
            <button type="button" class="text-red-600 hover:text-red-700 remove-group">
                <i class="fas fa-trash"></i> Remover Grupo
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="form-label">Nome do Grupo *</label>
                <input type="text" name="customization_groups[${groupIndex}][name]" 
                       class="form-input" placeholder="Ex: Tamanho, Sabor, Complementos" required>
            </div>
            <div>
                <label class="form-label">Ordem de Exibição</label>
                <input type="number" name="customization_groups[${groupIndex}][sort_order]" 
                       class="form-input" value="${groupIndex}" min="0">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="customization_groups[${groupIndex}][is_required]" 
                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <span class="ml-2 text-sm text-gray-700">Seleção obrigatória</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="customization_groups[${groupIndex}][allow_multiple]" 
                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                <span class="ml-2 text-sm text-gray-700">Permitir múltiplas seleções</span>
            </label>
        </div>
        
        <div class="border-t pt-4">
            <h5 class="text-md font-medium text-gray-800 mb-3">Opções do Grupo</h5>
            <div class="options-container" data-group="${groupIndex}">
                <!-- Options will be added here -->
            </div>
            <button type="button" class="btn-secondary btn-sm add-option" data-group="${groupIndex}">
                <i class="fas fa-plus mr-2"></i>Adicionar Opção
            </button>
        </div>
    `;
    
    container.appendChild(groupDiv);
    
    // Add first option automatically
    addOption(groupIndex);
    
    groupIndex++;
}

let optionIndexes = {};

function addOption(groupIdx) {
    if (!optionIndexes[groupIdx]) {
        optionIndexes[groupIdx] = 0;
    }
    
    const optionsContainer = document.querySelector(`.options-container[data-group="${groupIdx}"]`);
    if (!optionsContainer) {
        console.error('Options container not found for group:', groupIdx);
        return;
    }
    
    const optionDiv = document.createElement('div');
    optionDiv.className = 'option-item border border-gray-200 rounded-lg p-4 mb-3';
    optionDiv.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="form-label">Nome da Opção *</label>
                <input type="text" name="customization_groups[${groupIdx}][options][${optionIndexes[groupIdx]}][name]" 
                       class="form-input" placeholder="Ex: Pequeno, Médio, Grande" required>
            </div>
            <div>
                <label class="form-label">Preço Adicional (R$)</label>
                <input type="number" name="customization_groups[${groupIdx}][options][${optionIndexes[groupIdx]}][price]" 
                       class="form-input" step="0.01" min="0" value="0">
            </div>
            <div>
                <label class="form-label">Ordem</label>
                <input type="number" name="customization_groups[${groupIdx}][options][${optionIndexes[groupIdx]}][sort_order]" 
                       class="form-input" value="${optionIndexes[groupIdx]}" min="0">
            </div>
            <div class="flex items-end">
                <button type="button" class="text-red-600 hover:text-red-700 remove-option">
                    <i class="fas fa-trash"></i> Remover
                </button>
            </div>
        </div>
        
        <div class="mt-3">
            <label class="form-label">Imagem da Opção (opcional)</label>
            <input type="file" name="customization_groups[${groupIdx}][options][${optionIndexes[groupIdx]}][image]" 
                   class="form-input" accept="image/*">
        </div>
    `;
    
    optionsContainer.appendChild(optionDiv);
    optionIndexes[groupIdx]++;
}

// Add first group automatically
addCustomizationGroup();
</script>

<?php


?>

