<?php
$title = 'Categorias - ' . $establishment['name'];
$showNavbar = true;

?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Categorias</h1>
                <p class="text-gray-600">Organize seus produtos em categorias</p>
            </div>
            <a href="/categories/create" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Nova Categoria
            </a>
        </div>
    </div>

    <!-- Categories Grid -->
    <?php if (empty($categories)): ?>
    <div class="bg-white shadow rounded-lg p-12 text-center">
        <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma categoria criada</h3>
        <p class="text-gray-500 mb-6">Comece criando uma categoria para organizar seus produtos</p>
        <a href="/categories/create" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Criar Primeira Categoria
        </a>
    </div>
    <?php else: ?>
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-4 bg-blue-50 border-b border-blue-200">
            <div class="flex items-center text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                <span class="text-sm">Arraste as categorias para reordenar a prioridade no cardápio</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table id="categories-table" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-grip-vertical mr-2"></i>Categoria
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Produtos
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ordem
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($categories as $category): ?>
                    <tr class="hover:bg-gray-50 cursor-move" draggable="true" data-category-id="<?= $category['id'] ?>">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="mr-3 text-gray-400">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-tag text-primary-600 text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($category['name']) ?>
                                    </div>
                                    <?php if ($category['description']): ?>
                                    <div class="text-sm text-gray-500">
                                        <?= htmlspecialchars($category['description']) ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <?= $category['product_count'] ?> produtos
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-800">
                                #<?= $category['sort_order'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($category['is_active']): ?>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Ativa
                                </span>
                            <?php else: ?>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inativa
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="/categories/edit/<?= $category['id'] ?>" 
                               class="text-primary-600 hover:text-primary-900 mr-3">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                initCategoryDragDrop();
            });
        </script>
    </div>
    <?php endif; ?>
</div>

<?php


?>

