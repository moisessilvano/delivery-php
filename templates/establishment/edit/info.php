<?php
$title = 'Editar Informações Básicas - ' . $establishment['name'];
$showNavbar = true;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Informações Básicas</h1>
                <p class="text-gray-600">Nome, descrição e endereço do estabelecimento</p>
            </div>
            <a href="/profile" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Voltar ao Perfil
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-600 mr-2"></i>
            <span class="text-green-800"><?= htmlspecialchars(urldecode($_GET['success'])) ?></span>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
            <span class="text-red-800"><?= htmlspecialchars(urldecode($_GET['error'])) ?></span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6">
                <a href="/profile/edit/info" class="py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                    <i class="fas fa-info-circle mr-2"></i>Informações Básicas
                </a>
                <a href="/profile/edit/contact" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-images mr-2"></i>Mídia e Contato
                </a>
                <a href="/profile/edit/delivery" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-truck mr-2"></i>Entrega
                </a>
                <a href="/profile/edit/hours" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-clock mr-2"></i>Horários
                </a>
                <a href="/profile/edit/design" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-palette mr-2"></i>Personalização
                </a>
            </nav>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" class="space-y-6">
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Dados do Estabelecimento</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="form-label">Nome do Estabelecimento *</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($establishment['name'] ?? '') ?>" 
                           class="form-input" required>
                </div>
                
                <div>
                    <label for="phone" class="form-label">Telefone</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($establishment['phone'] ?? '') ?>" 
                           class="form-input" placeholder="(11) 99999-9999">
                    <div class="mt-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_whatsapp" value="1" 
                                   <?= ($establishment['is_whatsapp'] ?? 0) ? 'checked' : '' ?>
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Este número é WhatsApp</span>
                        </label>
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="form-label">Descrição</label>
                    <textarea id="description" name="description" rows="3" 
                              class="form-input" placeholder="Descreva seu estabelecimento..."><?= htmlspecialchars($establishment['description'] ?? '') ?></textarea>
                </div>
                

            </div>
        </div>

        <!-- Address Section -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Endereço do Estabelecimento</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label for="cep" class="form-label">CEP *</label>
                    <input type="text" id="cep" name="cep" 
                           value="<?= htmlspecialchars($establishment['cep'] ?? '') ?>" 
                           class="form-input cep-mask" 
                           placeholder="00000-000" required>
                </div>
                <div>
                    <label for="number" class="form-label">Número *</label>
                    <input type="text" id="number" name="number" 
                           value="<?= htmlspecialchars($establishment['number'] ?? '') ?>" 
                           class="form-input" 
                           placeholder="123" required>
                </div>
            </div>
            
            <div class="mt-4">
                <label for="street_address" class="form-label">Endereço *</label>
                <input type="text" id="street_address" name="street_address" 
                       value="<?= htmlspecialchars($establishment['street_address'] ?? '') ?>" 
                       class="form-input" 
                       placeholder="Rua, avenida, etc." required>
            </div>
            
            <div class="mt-4">
                <label for="complement" class="form-label">Complemento</label>
                <input type="text" id="complement" name="complement" 
                       value="<?= htmlspecialchars($establishment['complement'] ?? '') ?>" 
                       class="form-input" 
                       placeholder="Apartamento, casa, etc. (opcional)">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="neighborhood" class="form-label">Bairro *</label>
                    <input type="text" id="neighborhood" name="neighborhood" 
                           value="<?= htmlspecialchars($establishment['neighborhood'] ?? '') ?>" 
                           class="form-input" 
                           placeholder="Centro" required>
                </div>
                <div>
                    <label for="city" class="form-label">Cidade *</label>
                    <input type="text" id="city" name="city" 
                           value="<?= htmlspecialchars($establishment['city'] ?? '') ?>" 
                           class="form-input" 
                           placeholder="São Paulo" required>
                </div>
            </div>
            
            <div class="mt-4">
                <label for="state" class="form-label">Estado *</label>
                <select id="state" name="state" class="form-input" required>
                    <option value="">Selecione o estado</option>
                    <?php
                    $states = [
                        'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas', 'BA' => 'Bahia',
                        'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo', 'GO' => 'Goiás',
                        'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul', 'MG' => 'Minas Gerais',
                        'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná', 'PE' => 'Pernambuco', 'PI' => 'Piauí',
                        'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte', 'RS' => 'Rio Grande do Sul',
                        'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina', 'SP' => 'São Paulo',
                        'SE' => 'Sergipe', 'TO' => 'Tocantins'
                    ];
                    foreach ($states as $code => $name):
                        $selected = ($establishment['state'] ?? '') === $code ? 'selected' : '';
                    ?>
                    <option value="<?= $code ?>" <?= $selected ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div id="address-status" class="mt-3 text-sm hidden">
                <div id="address-success" class="text-green-600 hidden">
                    <i class="fas fa-check-circle mr-1"></i>
                    <span>Endereço validado e localização atualizada</span>
                </div>
                <div id="address-error" class="text-red-600 hidden">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    <span></span>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-between">
            <a href="/profile" class="btn-secondary">Cancelar</a>
            <div class="flex space-x-4">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Salvar Informações
                </button>
                <a href="/profile/edit/contact" class="btn-secondary">
                    Próximo: Mídia e Contato <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </form>
</div>

<script>
// CEP mask and ViaCEP integration
document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.getElementById('cep');
    if (cepInput) {
        // CEP mask
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            e.target.value = value;
        });
        
        // ViaCEP integration
        cepInput.addEventListener('blur', async function() {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length === 8) {
                await fetchAddressByCep(cep);
            }
        });
    }
});

// Fetch address by CEP
async function fetchAddressByCep(cep) {
    try {
        const response = await fetch(`/api/viacep?cep=${cep}`);
        const data = await response.json();
        
        if (data.success) {
            // Preencher campos e converter para maiúsculo
            document.getElementById('street_address').value = (data.street_address || '').toUpperCase();
            document.getElementById('neighborhood').value = (data.neighborhood || '').toUpperCase();
            document.getElementById('city').value = (data.city || '').toUpperCase();
            document.getElementById('state').value = (data.state || '').toUpperCase();
            document.getElementById('number').focus();
            showAddressSuccess('Endereço preenchido automaticamente!');
            setTimeout(() => geocodeCurrentAddress(), 500);
        } else {
            showAddressError(data.error || 'CEP não encontrado');
        }
    } catch (error) {
        showAddressError('Erro ao buscar CEP');
    }
}

// Geocode current address
async function geocodeCurrentAddress() {
    const streetAddress = document.getElementById('street_address').value;
    const number = document.getElementById('number').value;
    const neighborhood = document.getElementById('neighborhood').value;
    const city = document.getElementById('city').value;
    const state = document.getElementById('state').value;
    
    if (!streetAddress || !city) return;
    
    const fullAddress = `${streetAddress}, ${number}, ${neighborhood}, ${city}, ${state}, Brasil`;
    
    try {
        const response = await fetch('/api/geocode-address', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'address=' + encodeURIComponent(fullAddress)
        });
        
        const data = await response.json();
        if (data.success) {
            showAddressSuccess('Endereço validado e localização atualizada');
        } else {
            showAddressError('Erro na geocodificação: ' + (data.error || 'Endereço não encontrado'));
        }
    } catch (error) {
        showAddressError('Erro ao validar endereço');
    }
}

// Address status messages
function showAddressSuccess(message) {
    const statusDiv = document.getElementById('address-status');
    const successDiv = document.getElementById('address-success');
    const errorDiv = document.getElementById('address-error');
    statusDiv.classList.remove('hidden');
    successDiv.classList.remove('hidden');
    errorDiv.classList.add('hidden');
    successDiv.querySelector('span').textContent = message;
}

function showAddressError(message) {
    const statusDiv = document.getElementById('address-status');
    const successDiv = document.getElementById('address-success');
    const errorDiv = document.getElementById('address-error');
    statusDiv.classList.remove('hidden');
    successDiv.classList.add('hidden');
    errorDiv.classList.remove('hidden');
    errorDiv.querySelector('span').textContent = message;
}

// Auto-geocode when address fields change and convert to uppercase
['street_address', 'neighborhood', 'city', 'state'].forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
        // Convert to uppercase on input
        field.addEventListener('input', function() {
            const cursorPos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(cursorPos, cursorPos);
        });
        
        field.addEventListener('blur', function() {
            clearTimeout(field.geocodeTimeout);
            field.geocodeTimeout = setTimeout(() => geocodeCurrentAddress(), 1000);
        });
    }
});

// For number field, just add geocoding
const numberField = document.getElementById('number');
if (numberField) {
    numberField.addEventListener('blur', function() {
        clearTimeout(numberField.geocodeTimeout);
        numberField.geocodeTimeout = setTimeout(() => geocodeCurrentAddress(), 1000);
    });
}

// Auto-hide messages after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('[class*="bg-green-50"], [class*="bg-red-50"]');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });
});
</script>

<?php 
?>