<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Meus Pedidos - <?= htmlspecialchars($establishment['name']) ?></title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="<?= $establishment['primary_color'] ?? '#3B82F6' ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="<?= htmlspecialchars($establishment['name']) ?>">
    
    <!-- PWA Links -->
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: <?= $establishment['primary_color'] ?? '#3B82F6' ?>;
            --secondary-color: <?= $establishment['secondary_color'] ?? '#1E40AF' ?>;
            --background-color: <?= $establishment['background_color'] ?? '#F8FAFC' ?>;
            --text-color: <?= $establishment['text_color'] ?? '#1F2937' ?>;
        }
        
        body {
            background-color: var(--background-color);
            color: var(--text-color);
        }
        
        .primary-bg { background-color: var(--primary-color); }
        .primary-text { color: var(--primary-color); }
        
        @media (min-width: 768px) {
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 0 2rem;
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-receipt text-2xl text-blue-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Meus Pedidos</h1>
            <p class="text-gray-600">Digite seu telefone para acessar seus pedidos</p>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form id="phone-login-form" class="space-y-4">
                <div>
                    <label for="login-phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input type="tel" id="login-phone" name="phone" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 phone-mask" 
                           placeholder="(11) 99999-9999">
                </div>
                
                <div id="customer-confirm" class="hidden">
                    <div class="p-3 bg-gray-50 rounded-lg mb-4">
                        <p class="text-sm text-gray-600 mb-1">Cliente encontrado:</p>
                        <p class="font-medium text-gray-900" id="customer-name"></p>
                        <p class="text-sm text-gray-600" id="customer-phone-display"></p>
                    </div>
                </div>

                <!-- New Customer Form -->
                <div id="new-customer-form" class="hidden space-y-4">
                    <div>
                        <label for="customer-name-input" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo *</label>
                        <input type="text" id="customer-name-input" name="customer_name" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Digite seu nome completo">
                    </div>
                    
                    <div>
                        <label for="customer-email-input" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="customer-email-input" name="customer_email" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Digite seu email (opcional)">
                    </div>
                    
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2">
                            <label for="customer-cep-input" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                            <input type="text" id="customer-cep-input" name="customer_cep" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cep-mask" 
                                   placeholder="00000-000">
                        </div>
                        <div>
                            <label for="customer-number-input" class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                            <input type="text" id="customer-number-input" name="customer_number" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="123">
                        </div>
                    </div>
                    
                    <div>
                        <label for="customer-address-input" class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
                        <input type="text" id="customer-address-input" name="customer_address" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Rua, Avenida, etc.">
                    </div>
                    
                    <div>
                        <label for="customer-complement-input" class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                        <input type="text" id="customer-complement-input" name="customer_complement" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Apartamento, casa, etc. (opcional)">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="customer-neighborhood-input" class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                            <input type="text" id="customer-neighborhood-input" name="customer_neighborhood" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Bairro">
                        </div>
                        <div>
                            <label for="customer-city-input" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                            <input type="text" id="customer-city-input" name="customer_city" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Cidade">
                        </div>
                    </div>
                    
                    <div>
                        <label for="customer-state-input" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select id="customer-state-input" name="customer_state" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Selecione</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" id="login-submit" class="w-full primary-bg text-white py-3 rounded-lg font-medium hover:opacity-90 transition-opacity">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    <span id="login-text">Verificar Telefone</span>
                </button>
            </form>
        </div>

        <!-- Back to Menu -->
        <div class="text-center mt-6">
            <a href="/" class="text-gray-600 hover:text-gray-800 text-sm">
                <i class="fas fa-arrow-left mr-1"></i>
                Voltar ao Cardápio
            </a>
        </div>
    </div>

    <script src="/js/app.js"></script>
    <script>
        let foundCustomer = null;

        // Phone login form handling
        document.getElementById('phone-login-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const phone = document.getElementById('login-phone').value.replace(/\D/g, '');
            
            if (phone.length < 10) {
                showNotification('Digite um telefone válido', 'error');
                return;
            }

            if (!foundCustomer) {
                // First step: verify phone
                await verifyPhone(phone);
            } else {
                // Second step: confirm and login
                // Validate required fields for new customers
                if (foundCustomer.isNew) {
                    const nameInput = document.getElementById('customer-name-input');
                    if (!nameInput.value.trim()) {
                        showNotification('Nome é obrigatório', 'error');
                        nameInput.focus();
                        return;
                    }
                }
                await confirmLogin(phone);
            }
        });

        async function verifyPhone(phone) {
            try {
                const response = await fetch('/api/customer-by-phone', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ phone: phone })
                });

                const data = await response.json();
                console.log('API Response:', { status: response.status, ok: response.ok, data: data });

                if (response.ok && data.customer) {
                    // Existing customer
                    foundCustomer = data.customer;
                    document.getElementById('customer-name').textContent = data.customer.name;
                    document.getElementById('customer-phone-display').textContent = data.customer.phone;
                    document.getElementById('customer-confirm').classList.remove('hidden');
                    document.getElementById('new-customer-form').classList.add('hidden');
                    document.getElementById('login-text').textContent = 'Confirmar e Acessar';
                } else {
                    // New customer - show form
                    foundCustomer = { phone: phone, isNew: true };
                    document.getElementById('customer-confirm').classList.add('hidden');
                    document.getElementById('new-customer-form').classList.remove('hidden');
                    document.getElementById('login-text').textContent = 'Criar Conta e Acessar';
                }
            } catch (error) {
                showNotification('Erro ao verificar telefone. Tente novamente.', 'error');
            }
        }

        async function confirmLogin(phone) {
            try {
                // Show loading state
                const submitBtn = document.getElementById('login-submit');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processando...';
                submitBtn.disabled = true;

                if (foundCustomer.isNew) {
                    // Create new customer
                    const customerData = {
                        phone: phone,
                        name: document.getElementById('customer-name-input').value,
                        email: document.getElementById('customer-email-input').value,
                        cep: document.getElementById('customer-cep-input').value,
                        address: document.getElementById('customer-address-input').value,
                        number: document.getElementById('customer-number-input').value,
                        complement: document.getElementById('customer-complement-input').value,
                        neighborhood: document.getElementById('customer-neighborhood-input').value,
                        city: document.getElementById('customer-city-input').value,
                        state: document.getElementById('customer-state-input').value
                    };

                    // Validate required fields
                    if (!customerData.name.trim()) {
                        throw new Error('Nome é obrigatório');
                    }

                    const createResponse = await fetch('/api/create-customer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(customerData)
                    });

                    if (!createResponse.ok) {
                        throw new Error('Erro ao criar conta');
                    }
                }

                // Store phone in localStorage
                localStorage.setItem('user_phone', phone);

                // Show success message
                showNotification('Login realizado com sucesso!', 'success');
                
                // Redirect to orders page
                setTimeout(() => {
                    window.location.href = `/orders-list?phone=${encodeURIComponent(phone)}`;
                }, 1000);
                
            } catch (error) {
                // Restore button state
                const submitBtn = document.getElementById('login-submit');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                showNotification(error.message || 'Erro ao fazer login. Tente novamente.', 'error');
            }
        }

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // ViaCEP integration
        function setupViaCEP() {
            const cepInput = document.getElementById('customer-cep-input');
            if (cepInput) {
                cepInput.addEventListener('blur', async function() {
                    const cep = this.value.replace(/\D/g, '');
                    if (cep.length === 8) {
                        try {
                            const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                            const data = await response.json();
                            
                            if (!data.erro) {
                                // Fill address fields
                                document.getElementById('customer-address-input').value = data.logradouro || '';
                                document.getElementById('customer-neighborhood-input').value = data.bairro || '';
                                document.getElementById('customer-city-input').value = data.localidade || '';
                                document.getElementById('customer-state-input').value = data.uf || '';
                                
                                // Focus on number field
                                document.getElementById('customer-number-input').focus();
                                
                                showNotification('Endereço preenchido automaticamente!', 'success');
                            } else {
                                showNotification('CEP não encontrado', 'error');
                            }
                        } catch (error) {
                            showNotification('Erro ao buscar CEP', 'error');
                        }
                    }
                });
            }
        }

        // Initialize ViaCEP when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setupViaCEP();
        });
    </script>
</body>
</html>
