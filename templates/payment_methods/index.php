<?php
$title = 'Formas de Pagamento - ' . $establishment['name'];
$showNavbar = true;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Formas de Pagamento</h1>
            <p class="text-gray-600">Configure as formas de pagamento aceitas</p>
        </div>
        <div class="flex space-x-3">
            <?php if (empty($payment_methods)): ?>
            <a href="/payment-methods/setup-default" class="btn-secondary">
                <i class="fas fa-magic mr-2"></i>Criar Padrões
            </a>
            <?php endif; ?>
            <a href="/payment-methods/create" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>Nova Forma
            </a>
        </div>
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

    <!-- Payment Methods List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <?php if (empty($payment_methods)): ?>
        <div class="text-center py-12">
            <i class="fas fa-credit-card text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma forma de pagamento configurada</h3>
            <p class="text-gray-500 mb-4">Configure as formas de pagamento que você aceita</p>
            <div class="flex justify-center space-x-3">
                <a href="/payment-methods/setup-default" class="btn-secondary">
                    <i class="fas fa-magic mr-2"></i>Criar Formas Padrão
                </a>
                <a href="/payment-methods/create" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>Adicionar Forma
                </a>
            </div>
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
                            Tipo
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
                    <?php foreach ($payment_methods as $method): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <?php
                                $icons = [
                                    'dinheiro' => 'fas fa-money-bill-wave text-green-600',
                                    'cartao_credito' => 'fas fa-credit-card text-blue-600',
                                    'cartao_debito' => 'fas fa-credit-card text-purple-600',
                                    'pix' => 'fas fa-qrcode text-teal-600',
                                    'vale_refeicao' => 'fas fa-utensils text-orange-600',
                                    'vale_alimentacao' => 'fas fa-shopping-basket text-red-600'
                                ];
                                $icon = $icons[$method['type']] ?? 'fas fa-credit-card text-gray-600';
                                ?>
                                <i class="<?= $icon ?> mr-3"></i>
                                <div class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($method['name']) ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php
                            $types = [
                                'dinheiro' => 'Dinheiro',
                                'cartao_credito' => 'Cartão de Crédito',
                                'cartao_debito' => 'Cartão de Débito',
                                'pix' => 'PIX',
                                'vale_refeicao' => 'Vale Refeição',
                                'vale_alimentacao' => 'Vale Alimentação'
                            ];
                            echo $types[$method['type']] ?? $method['type'];
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?= $method['sort_order'] ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($method['is_active']): ?>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Ativo
                                </span>
                            <?php else: ?>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inativo
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="/payment-methods/edit/<?= $method['id'] ?>" 
                               class="text-primary-600 hover:text-primary-900">
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
