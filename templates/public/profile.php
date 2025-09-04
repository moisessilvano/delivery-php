<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Meu Perfil - <?= htmlspecialchars($establishment['name']) ?></title>
    
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
            
            .mobile-nav {
                display: none;
            }
        }
        
        .form-input {
            @apply w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500;
        }
        
        .form-label {
            @apply block text-sm font-medium text-gray-700 mb-1;
        }
    </style>
</head>
<body class="pb-20">
    <!-- Header -->
    <header class="sticky top-0 z-40 bg-white shadow-sm">
        <div class="container">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <button onclick="history.back()" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </button>
                        <div>
                            <h1 class="text-lg font-bold">Meu Perfil</h1>
                            <p class="text-sm text-gray-500">Gerencie suas informações</p>
                        </div>
                    </div>
                    <div>
                        <button id="logout-btn" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors text-sm">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Sair
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="container p-4 space-y-6">
        <!-- User Info Card -->
        <div class="bg-white rounded-xl p-6 shadow-sm">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user text-2xl text-blue-600"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900" id="profile-name">
                    <?= htmlspecialchars($customer['name'] ?? 'Usuário') ?>
                </h2>
                <p class="text-gray-600" id="profile-phone">
                    <?= htmlspecialchars($customer['phone'] ?? '') ?>
                </p>
            </div>

            <!-- Quick Stats -->
            <!-- <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold primary-text"><?= $order_stats['total_orders'] ?? 0 ?></div>
                    <div class="text-sm text-gray-600">Pedidos</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold primary-text">R$ <?= number_format($order_stats['total_spent'] ?? 0, 2, ',', '.') ?></div>
                    <div class="text-sm text-gray-600">Gasto Total</div>
                </div>
            </div> -->
        </div>

        <!-- Menu Options -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b">
                <h3 class="font-semibold text-gray-900">Configurações</h3>
            </div>
            
            <div class="divide-y divide-gray-100">
                <button class="profile-option w-full p-4 flex items-center justify-between hover:bg-gray-50 transition-colors" data-action="edit-info">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div class="text-left">
                            <div class="font-medium text-gray-900">Informações Pessoais</div>
                            <div class="text-sm text-gray-600">Nome, telefone e email</div>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>

                <button class="profile-option w-full p-4 flex items-center justify-between hover:bg-gray-50 transition-colors" data-action="edit-address">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-green-600"></i>
                        </div>
                        <div class="text-left">
                            <div class="font-medium text-gray-900">Endereço de Entrega</div>
                            <div class="text-sm text-gray-600">Gerencie seus endereços</div>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>

                <button class="profile-option w-full p-4 flex items-center justify-between hover:bg-gray-50 transition-colors" data-action="notifications">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-bell text-yellow-600"></i>
                        </div>
                        <div class="text-left">
                            <div class="font-medium text-gray-900">Notificações</div>
                            <div class="text-sm text-gray-600">Configurar alertas de pedidos</div>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>

                <a href="/orders-list" class="profile-option w-full p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-receipt text-purple-600"></i>
                        </div>
                        <div class="text-left">
                            <div class="font-medium text-gray-900">Histórico de Pedidos</div>
                            <div class="text-sm text-gray-600">Ver todos os seus pedidos</div>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
            </div>
        </div>

        <!-- Establishment Info -->
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <h3 class="font-semibold text-gray-900 mb-3">Sobre o Restaurante</h3>
            <div class="flex items-start space-x-3">
                <?php if ($establishment['logo']): ?>
                <img src="/<?= htmlspecialchars($establishment['logo']) ?>" 
                     alt="<?= htmlspecialchars($establishment['name']) ?>" 
                     class="w-12 h-12 rounded-lg object-cover">
                <?php endif; ?>
                <div class="flex-1">
                    <h4 class="font-medium text-gray-900"><?= htmlspecialchars($establishment['name']) ?></h4>
                    <?php if ($establishment['description']): ?>
                    <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($establishment['description']) ?></p>
                    <?php endif; ?>
                    
                    <div class="flex flex-wrap gap-4 mt-3 text-sm text-gray-600">
                        <?php if ($establishment['phone']): ?>
                        <div><i class="fas fa-phone mr-1"></i><?= htmlspecialchars($establishment['phone']) ?></div>
                        <?php endif; ?>
                        
                        <?php if ($establishment['whatsapp']): ?>
                        <a href="https://wa.me/<?= preg_replace('/[^\d]/', '', $establishment['whatsapp']) ?>" 
                           target="_blank" class="text-green-600 hover:text-green-700">
                            <i class="fab fa-whatsapp mr-1"></i>WhatsApp
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Mobile Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t mobile-nav">
        <div class="flex items-center justify-around py-2">
            <button class="nav-item flex flex-col items-center py-2 px-4" data-page="menu" onclick="window.location.href='/'">
                <i class="fas fa-utensils text-xl mb-1"></i>
                <span class="text-xs">Cardápio</span>
            </button>
            <button class="nav-item flex flex-col items-center py-2 px-4" data-page="orders" onclick="window.location.href='/orders-list'">
                <i class="fas fa-receipt text-xl mb-1"></i>
                <span class="text-xs">Pedidos</span>
            </button>
            <button class="nav-item active flex flex-col items-center py-2 px-4" data-page="profile">
                <i class="fas fa-user text-xl mb-1 primary-text"></i>
                <span class="text-xs primary-text">Perfil</span>
            </button>
        </div>
    </nav>

    <!-- Edit Info Modal -->
    <div id="edit-info-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end justify-center md:items-center">
        <div class="bg-white rounded-t-3xl md:rounded-xl w-full max-w-md">
            <div class="p-4 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold">Editar Informações</h3>
                    <button class="close-modal text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <form id="info-form" class="p-4 space-y-4">
                <div>
                    <label for="edit-name" class="form-label">Nome Completo *</label>
                    <input type="text" id="edit-name" name="name" required class="form-input" 
                           value="<?= htmlspecialchars($customer['name'] ?? '') ?>">
                </div>
                
                <div>
                    <label for="edit-phone" class="form-label">Telefone *</label>
                    <input type="tel" id="edit-phone" name="phone" required class="form-input phone-mask" 
                           value="<?= htmlspecialchars($customer['phone'] ?? '') ?>">
                </div>
                
                <div>
                    <label for="edit-email" class="form-label">Email</label>
                    <input type="email" id="edit-email" name="email" class="form-input" 
                           value="<?= htmlspecialchars($customer['email'] ?? '') ?>">
                </div>
                
                <button type="submit" class="w-full primary-bg text-white py-3 rounded-lg font-medium">
                    Salvar Alterações
                </button>
            </form>
        </div>
    </div>



    <!-- Edit Address Modal -->
    <div id="edit-address-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end justify-center md:items-center">
        <div class="bg-white rounded-t-3xl md:rounded-xl w-full max-w-md max-h-96 overflow-y-auto">
            <div class="p-4 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold">Editar Endereço</h3>
                    <button class="close-modal text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <form id="address-form" class="p-4 space-y-4">
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label for="edit-cep" class="form-label">CEP</label>
                        <input type="text" id="edit-cep" name="cep" class="form-input cep-mask" 
                               value="<?= htmlspecialchars($customer['cep'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="edit-number" class="form-label">Número</label>
                        <input type="text" id="edit-number" name="number" class="form-input" 
                               value="<?= htmlspecialchars($customer['number'] ?? '') ?>">
                    </div>
                </div>
                
                <div>
                    <label for="edit-address" class="form-label">Endereço</label>
                    <input type="text" id="edit-address" name="address" class="form-input" 
                           value="<?= htmlspecialchars($customer['address'] ?? '') ?>">
                </div>
                
                <div>
                    <label for="edit-complement" class="form-label">Complemento</label>
                    <input type="text" id="edit-complement" name="complement" class="form-input" 
                           value="<?= htmlspecialchars($customer['complement'] ?? '') ?>">
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="edit-neighborhood" class="form-label">Bairro</label>
                        <input type="text" id="edit-neighborhood" name="neighborhood" class="form-input" 
                               value="<?= htmlspecialchars($customer['neighborhood'] ?? '') ?>">
                    </div>
                    <div>
                        <label for="edit-city" class="form-label">Cidade</label>
                        <input type="text" id="edit-city" name="city" class="form-input" 
                               value="<?= htmlspecialchars($customer['city'] ?? '') ?>">
                    </div>
                </div>
                
                <div>
                    <label for="edit-state" class="form-label">Estado</label>
                    <select id="edit-state" name="state" class="form-input">
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
                            $selected = ($customer['state'] ?? '') === $code ? 'selected' : '';
                        ?>
                        <option value="<?= $code ?>" <?= $selected ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="w-full primary-bg text-white py-3 rounded-lg font-medium">
                    Salvar Endereço
                </button>
            </form>
        </div>
    </div>

    <script src="/js/app.js"></script>
    <script>
        // Profile option handlers
        document.querySelectorAll('.profile-option').forEach(option => {
            option.addEventListener('click', function() {
                const action = this.dataset.action;
                
                switch(action) {
                    case 'edit-info':
                        document.getElementById('edit-info-modal').classList.remove('hidden');
                        break;
                    case 'edit-address':
                        document.getElementById('edit-address-modal').classList.remove('hidden');
                        break;
                    case 'notifications':
                        handleNotificationSettings();
                        break;
                }
            });
        });
        
        // Close modals
        document.querySelectorAll('.close-modal').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.fixed').classList.add('hidden');
            });
        });
        
        // Form submissions
        document.getElementById('info-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('/api/update-profile', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                if (response.ok) {
                    showNotification('Informações atualizadas com sucesso!', 'success');
                    
                    // Update UI
                    document.getElementById('profile-name').textContent = data.name;
                    document.getElementById('profile-phone').textContent = data.phone;
                    
                    document.getElementById('edit-info-modal').classList.add('hidden');
                } else {
                    throw new Error('Erro ao atualizar informações');
                }
            } catch (error) {
                showNotification('Erro ao atualizar informações. Tente novamente.', 'error');
            }
        });
        
        document.getElementById('address-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('/api/update-address', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                if (response.ok) {
                    showNotification('Endereço atualizado com sucesso!', 'success');
                    document.getElementById('edit-address-modal').classList.add('hidden');
                } else {
                    throw new Error('Erro ao atualizar endereço');
                }
            } catch (error) {
                showNotification('Erro ao atualizar endereço. Tente novamente.', 'error');
            }
        });
        
        function handleNotificationSettings() {
            if ('Notification' in window) {
                const permission = Notification.permission;
                
                if (permission === 'granted') {
                    showNotification('Notificações já estão ativadas!', 'info');
                } else if (permission === 'denied') {
                    showNotification('Notificações foram negadas. Ative nas configurações do navegador.', 'warning');
                } else {
                    Notification.requestPermission().then(permission => {
                        if (permission === 'granted') {
                            showNotification('Notificações ativadas com sucesso!', 'success');
                        }
                    });
                }
            } else {
                showNotification('Notificações não são suportadas neste dispositivo.', 'error');
            }
        }
        


        // Logout function
        async function logout() {
            try {
                // Clear session via API
                await fetch('/api/clear-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });
                
                // Clear localStorage
                localStorage.removeItem('user_phone');
                localStorage.removeItem('customer_data');
                localStorage.removeItem('checkout_customer');
                
                // Show success message
                showNotification('Logout realizado com sucesso!', 'success');
                
                // Redirect to menu after delay
                setTimeout(() => {
                    window.location.href = '/';
                }, 1500);
                
            } catch (error) {
                console.error('Erro no logout:', error);
                // Even if API fails, clear localStorage and redirect
                localStorage.removeItem('user_phone');
                localStorage.removeItem('customer_data');
                localStorage.removeItem('checkout_customer');
                window.location.href = '/';
            }
        }

        // Add event listener for logout button
        document.getElementById('logout-btn').addEventListener('click', function() {
            if (confirm('Tem certeza que deseja sair?')) {
                logout();
            }
        });

        // Load user data from phone
        const userPhone = localStorage.getItem('user_phone');
        if (userPhone && !document.getElementById('profile-phone').textContent) {
            document.getElementById('profile-phone').textContent = userPhone;
        }
    </script>
</body>
</html>
