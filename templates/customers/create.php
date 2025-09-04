<?php
$title = 'Novo Cliente - ' . $establishment['name'];
$showNavbar = true;
?>

<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="/customers" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Novo Cliente</h1>
            <p class="text-gray-600">Adicione um novo cliente ao seu estabelecimento</p>
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
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>

                <div>
                    <label for="phone" class="form-label">Telefone *</label>
                    <input type="tel" id="phone" name="phone" required
                           class="form-input phone-mask" 
                           placeholder="(11) 99999-9999"
                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
            </div>

            <div>
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email"
                       class="form-input" 
                       placeholder="email@exemplo.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <!-- Endereço -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Endereço</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="cep" class="form-label">CEP</label>
                        <input type="text" id="cep" name="cep"
                               class="form-input cep-mask" 
                               placeholder="00000-000"
                               value="<?= htmlspecialchars($_POST['cep'] ?? '') ?>">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="address" class="form-label">Endereço</label>
                        <input type="text" id="address" name="address"
                               class="form-input" 
                               placeholder="Rua, avenida, etc."
                               value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label for="number" class="form-label">Número</label>
                        <input type="text" id="number" name="number"
                               class="form-input" 
                               placeholder="123"
                               value="<?= htmlspecialchars($_POST['number'] ?? '') ?>">
                    </div>
                    
                    <div>
                        <label for="complement" class="form-label">Complemento</label>
                        <input type="text" id="complement" name="complement"
                               class="form-input" 
                               placeholder="Apto, bloco, etc."
                               value="<?= htmlspecialchars($_POST['complement'] ?? '') ?>">
                    </div>
                    
                    <div>
                        <label for="neighborhood" class="form-label">Bairro</label>
                        <input type="text" id="neighborhood" name="neighborhood"
                               class="form-input" 
                               placeholder="Centro"
                               value="<?= htmlspecialchars($_POST['neighborhood'] ?? '') ?>">
                    </div>
                    
                    <div>
                        <label for="city" class="form-label">Cidade</label>
                        <input type="text" id="city" name="city"
                               class="form-input" 
                               placeholder="São Paulo"
                               value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label for="state" class="form-label">Estado</label>
                        <select id="state" name="state" class="form-input">
                            <option value="">Selecione</option>
                            <?php 
                            $states = [
                                'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas',
                                'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
                                'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul',
                                'MG' => 'Minas Gerais', 'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
                                'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
                                'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
                                'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins'
                            ];
                            foreach ($states as $code => $name):
                                $selected = ($_POST['state'] ?? '') === $code ? 'selected' : '';
                            ?>
                            <option value="<?= $code ?>" <?= $selected ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label for="notes" class="form-label">Observações</label>
                <textarea id="notes" name="notes" rows="3"
                          class="form-input" 
                          placeholder="Observações sobre o cliente (opcional)"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="/customers" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Salvar Cliente
                </button>
            </div>
        </form>
    </div>
</div>
