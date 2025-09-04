<?php
$title = 'Editar Forma de Pagamento - ' . $establishment['name'];
$showNavbar = true;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Forma de Pagamento</h1>
            <p class="text-gray-600">Atualize as informações da forma de pagamento</p>
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
                           value="<?= htmlspecialchars($payment_method['name']) ?>">
                </div>

                <div>
                    <label for="type" class="form-label">Tipo *</label>
                    <select id="type" name="type" required class="form-input">
                        <option value="">Selecione o tipo</option>
                        <option value="dinheiro" <?= $payment_method['type'] === 'dinheiro' ? 'selected' : '' ?>>Dinheiro</option>
                        <option value="cartao_credito" <?= $payment_method['type'] === 'cartao_credito' ? 'selected' : '' ?>>Cartão de Crédito</option>
                        <option value="cartao_debito" <?= $payment_method['type'] === 'cartao_debito' ? 'selected' : '' ?>>Cartão de Débito</option>
                        <option value="pix" <?= $payment_method['type'] === 'pix' ? 'selected' : '' ?>>PIX</option>
                        <option value="vale_refeicao" <?= $payment_method['type'] === 'vale_refeicao' ? 'selected' : '' ?>>Vale Refeição</option>
                        <option value="vale_alimentacao" <?= $payment_method['type'] === 'vale_alimentacao' ? 'selected' : '' ?>>Vale Alimentação</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="sort_order" class="form-label">Ordem de Exibição</label>
                    <input type="number" id="sort_order" name="sort_order" 
                           class="form-input" min="0" step="1"
                           value="<?= htmlspecialchars($payment_method['sort_order']) ?>">
                    <p class="text-xs text-gray-500 mt-1">Ordem em que aparece no checkout (menor número = primeiro)</p>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" 
                           class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                           <?= $payment_method['is_active'] ? 'checked' : '' ?>>
                    <label for="is_active" class="ml-2 text-sm text-gray-700">
                        Forma de pagamento ativa
                    </label>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end space-x-4">
                <a href="/payment-methods" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Salvar Alterações
                </button>
            </div>
        </form>
    </div>

    <!-- Delete Section -->
    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
        <h3 class="text-lg font-medium text-red-900 mb-2">
            <i class="fas fa-exclamation-triangle mr-2"></i>Zona de Perigo
        </h3>
        <p class="text-red-700 text-sm mb-4">
            Excluir esta forma de pagamento irá removê-la permanentemente. Esta ação não pode ser desfeita.
        </p>
        <form method="POST" action="/payment-methods/delete/<?= $payment_method['id'] ?>" 
              onsubmit="return confirm('Tem certeza que deseja excluir esta forma de pagamento? Esta ação não pode ser desfeita.')">
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-2"></i>Excluir Forma de Pagamento
            </button>
        </form>
    </div>
</div>
