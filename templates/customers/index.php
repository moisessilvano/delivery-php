<?php
$title = 'Clientes - ' . $establishment['name'];
$showNavbar = true;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Clientes</h1>
            <p class="text-gray-600">Gerencie os clientes do seu estabelecimento</p>
        </div>
        <a href="/customers/create" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Novo Cliente
        </a>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
        <?= htmlspecialchars($_GET['success']) ?>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <?= htmlspecialchars($_GET['error']) ?>
    </div>
    <?php endif; ?>

    <!-- Customers List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <?php if (empty($customers)): ?>
        <div class="text-center py-12">
            <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum cliente cadastrado</h3>
            <p class="text-gray-500 mb-4">Comece adicionando seus primeiros clientes</p>
            <a href="/customers/create" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Adicionar Cliente
            </a>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nome
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Telefone
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cadastrado em
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($customers as $customer): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                <?= htmlspecialchars($customer['name']) ?>
                            </div>
                            <?php if ($customer['address']): ?>
                            <div class="text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                <?= htmlspecialchars($customer['address']) ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <i class="fas fa-phone mr-1 text-gray-400"></i>
                            <?= htmlspecialchars($customer['phone']) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php if ($customer['email']): ?>
                                <i class="fas fa-envelope mr-1 text-gray-400"></i>
                                <?= htmlspecialchars($customer['email']) ?>
                            <?php else: ?>
                                <span class="text-gray-400">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= date('d/m/Y', strtotime($customer['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="/customers/edit/<?= $customer['id'] ?>" 
                               class="text-primary-600 hover:text-primary-900 mr-3">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>
