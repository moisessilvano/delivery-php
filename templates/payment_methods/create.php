<?php
$title = 'Adicionar Forma de Pagamento - ' . $establishment['name'];
$showNavbar = true;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Adicionar Forma de Pagamento</h1>
            <p class="text-gray-600">Configure uma nova forma de pagamento para seu estabelecimento</p>
        </div>
        <a href="/payment-methods" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Voltar
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="form-label">Nome da Forma de Pagamento *</label>
                    <input type="text" id="name" name="name" required
                           class="form-input" 
                           placeholder="Ex: Cartão de Crédito Visa"
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>

                <div>
                    <label for="type" class="form-label">Tipo *</label>
                    <select id="type" name="type" required class="form-input">
                        <option value="">Selecione o tipo</option>
                        <option value="dinheiro" <?= ($_POST['type'] ?? '') === 'dinheiro' ? 'selected' : '' ?>>Dinheiro</option>
                        <option value="cartao_credito" <?= ($_POST['type'] ?? '') === 'cartao_credito' ? 'selected' : '' ?>>Cartão de Crédito</option>
                        <option value="cartao_debito" <?= ($_POST['type'] ?? '') === 'cartao_debito' ? 'selected' : '' ?>>Cartão de Débito</option>
                        <option value="pix" <?= ($_POST['type'] ?? '') === 'pix' ? 'selected' : '' ?>>PIX</option>
                        <option value="vale_refeicao" <?= ($_POST['type'] ?? '') === 'vale_refeicao' ? 'selected' : '' ?>>Vale Refeição</option>
                        <option value="vale_alimentacao" <?= ($_POST['type'] ?? '') === 'vale_alimentacao' ? 'selected' : '' ?>>Vale Alimentação</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="sort_order" class="form-label">Ordem de Exibição</label>
                    <input type="number" id="sort_order" name="sort_order" 
                           class="form-input" min="0" step="1"
                           placeholder="0"
                           value="<?= htmlspecialchars($_POST['sort_order'] ?? '0') ?>">
                    <p class="text-xs text-gray-500 mt-1">Ordem em que aparece no checkout (menor número = primeiro)</p>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" checked 
                           class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                           <?= isset($_POST['is_active']) ? 'checked' : '' ?>>
                    <label for="is_active" class="ml-2 text-sm text-gray-700">
                        Forma de pagamento ativa
                    </label>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end space-x-4">
                <a href="/payment-methods" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Adicionar Forma de Pagamento
                </button>
            </div>
        </form>
    </div>

    <!-- Payment Method Types Info -->
    <div class="bg-blue-50 rounded-lg p-6">
        <h3 class="text-lg font-medium text-blue-900 mb-4">
            <i class="fas fa-info-circle mr-2"></i>Tipos de Pagamento Disponíveis
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
            <div class="bg-white rounded p-3">
                <h4 class="font-medium text-gray-900 mb-2">Dinheiro</h4>
                <p class="text-gray-600">Pagamento em espécie na entrega</p>
            </div>
            <div class="bg-white rounded p-3">
                <h4 class="font-medium text-gray-900 mb-2">Cartão de Crédito</h4>
                <p class="text-gray-600">Visa, MasterCard, Elo, etc.</p>
            </div>
            <div class="bg-white rounded p-3">
                <h4 class="font-medium text-gray-900 mb-2">Cartão de Débito</h4>
                <p class="text-gray-600">Débito na máquina</p>
            </div>
            <div class="bg-white rounded p-3">
                <h4 class="font-medium text-gray-900 mb-2">PIX</h4>
                <p class="text-gray-600">Transferência instantânea</p>
            </div>
            <div class="bg-white rounded p-3">
                <h4 class="font-medium text-gray-900 mb-2">Vale Refeição</h4>
                <p class="text-gray-600">Sodexo, Alelo, VR, etc.</p>
            </div>
            <div class="bg-white rounded p-3">
                <h4 class="font-medium text-gray-900 mb-2">Vale Alimentação</h4>
                <p class="text-gray-600">Ticket, Sodexo, etc.</p>
            </div>
        </div>
    </div>
</div>
