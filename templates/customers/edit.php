<?php
$title = 'Editar Cliente - ' . $establishment['name'];
$showNavbar = true;
?>

<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="/customers" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Editar Cliente</h1>
            <p class="text-gray-600">Atualize as informações do cliente</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="form-label">Nome *</label>
                    <input type="text" id="name" name="name" required
                           class="form-input" 
                           placeholder="Nome completo do cliente"
                           value="<?= htmlspecialchars($_POST['name'] ?? $customer['name']) ?>">
                </div>

                <div>
                    <label for="phone" class="form-label">Telefone *</label>
                    <input type="tel" id="phone" name="phone" required
                           class="form-input" 
                           placeholder="(11) 99999-9999"
                           value="<?= htmlspecialchars($_POST['phone'] ?? $customer['phone']) ?>">
                </div>
            </div>

            <div>
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email"
                       class="form-input" 
                       placeholder="email@exemplo.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? $customer['email']) ?>">
            </div>

            <div>
                <label for="address" class="form-label">Endereço</label>
                <textarea id="address" name="address" rows="3"
                          class="form-input" 
                          placeholder="Endereço completo para entrega"><?= htmlspecialchars($_POST['address'] ?? $customer['address']) ?></textarea>
            </div>

            <div>
                <label for="notes" class="form-label">Observações</label>
                <textarea id="notes" name="notes" rows="3"
                          class="form-input" 
                          placeholder="Observações sobre o cliente (opcional)"><?= htmlspecialchars($_POST['notes'] ?? $customer['notes']) ?></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/customers" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Atualizar Cliente
                </button>
            </div>
        </form>
    </div>

    <!-- Customer Info -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="font-medium text-gray-900 mb-2">Informações do Cliente</h3>
        <div class="text-sm text-gray-600 space-y-1">
            <p><strong>Cadastrado em:</strong> <?= date('d/m/Y H:i', strtotime($customer['created_at'])) ?></p>
            <?php if ($customer['updated_at'] !== $customer['created_at']): ?>
            <p><strong>Última atualização:</strong> <?= date('d/m/Y H:i', strtotime($customer['updated_at'])) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
