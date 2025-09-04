<?php
$title = 'Produtos - ' . $establishment['name'];
$showNavbar = true;

?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Produtos</h1>
                <p class="text-gray-600">Gerencie os produtos do seu cardápio</p>
            </div>
            <a href="/products/create" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Novo Produto
            </a>
        </div>
    </div>

    <!-- Products Table -->
    <?php if (empty($products)): ?>
    <div class="bg-white shadow rounded-lg p-12 text-center">
        <i class="fas fa-box text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum produto criado</h3>
        <p class="text-gray-500 mb-6">Comece criando produtos para seu cardápio</p>
        <a href="/products/create" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Criar Primeiro Produto
        </a>
    </div>
    <?php else: ?>
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Produto
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Categoria
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Preço
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($products as $product): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <?php if ($product['image']): ?>
                                <img src="/<?= htmlspecialchars($product['image']) ?>" 
                                     alt="<?= htmlspecialchars($product['name']) ?>" 
                                     class="w-12 h-12 object-cover rounded-lg mr-4">
                                <?php else: ?>
                                <div class="w-12 h-12 bg-gray-200 rounded-lg mr-4 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                                <?php endif; ?>
                                
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </div>
                                    <?php if ($product['description']): ?>
                                    <div class="text-sm text-gray-500 truncate max-w-xs">
                                        <?= htmlspecialchars($product['description']) ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?= htmlspecialchars($product['category_name']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            R$ <?= number_format($product['price'], 2, ',', '.') ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($product['is_available']): ?>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Disponível
                            </span>
                            <?php else: ?>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Indisponível
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="/products/edit/<?= $product['id'] ?>" 
                               class="text-primary-600 hover:text-primary-900 mr-3">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php


?>

