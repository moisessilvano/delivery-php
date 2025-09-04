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
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md text-center">
        <!-- Loading Animation -->
        <div class="mb-8">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-spinner fa-spin text-2xl text-blue-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Carregando Perfil</h1>
            <p class="text-gray-600">Verificando seus dados...</p>
        </div>

        <!-- Loading Steps -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <div id="step-1" class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-xs">
                        <i class="fas fa-phone"></i>
                    </div>
                    <span class="text-sm text-gray-600">Verificando telefone...</span>
                </div>
                
                <div class="flex items-center space-x-3">
                    <div id="step-2" class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-xs">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="text-sm text-gray-600">Carregando dados do cliente...</span>
                </div>
                
                <div class="flex items-center space-x-3">
                    <div id="step-3" class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-xs">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <span class="text-sm text-gray-600">Abrindo perfil...</span>
                </div>
            </div>
        </div>

        <!-- Error State (hidden by default) -->
        <div id="error-state" class="hidden mt-6">
            <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
                    <h3 class="text-lg font-semibold text-red-800">Não foi possível carregar automaticamente</h3>
                </div>
                <p class="text-red-600 mb-4">Você precisa fazer login para acessar seu perfil.</p>
                <button onclick="showLoginForm()" class="primary-bg text-white px-6 py-3 rounded-lg font-medium hover:opacity-90 transition-opacity">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Fazer Login
                </button>
            </div>
        </div>

        <!-- Login Form (hidden by default) -->
        <div id="login-form" class="hidden mt-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Digite seu telefone</h3>
                <form id="phone-login-form" class="space-y-4">
                    <div>
                        <input type="tel" id="login-phone" name="phone" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 phone-mask" 
                               placeholder="(11) 99999-9999">
                    </div>
                    
                    <button type="submit" class="w-full primary-bg text-white py-3 rounded-lg font-medium hover:opacity-90 transition-opacity">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Acessar Perfil
                    </button>
                </form>
            </div>
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
        // Auto-login check
        document.addEventListener('DOMContentLoaded', function() {
            checkAutoLogin();
        });

        async function checkAutoLogin() {
            try {
                // Step 1: Check localStorage
                updateStep(1, 'success');
                await sleep(500);
                
                const userPhone = localStorage.getItem('user_phone');
                if (!userPhone) {
                    throw new Error('No phone in localStorage');
                }

                // Step 2: Verify customer exists
                updateStep(2, 'loading');
                await sleep(500);
                
                const response = await fetch('/api/customer-by-phone', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ phone: userPhone })
                });

                if (!response.ok) {
                    throw new Error('Customer not found');
                }

                const data = await response.json();
                if (!data.success || !data.customer) {
                    throw new Error('Customer not found');
                }

                updateStep(2, 'success');
                await sleep(500);

                // Step 3: Set session and redirect to profile
                updateStep(3, 'loading');
                await sleep(500);
                
                // Set session via API call
                await fetch('/api/set-session', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ phone: userPhone })
                });
                
                window.location.href = `/profile`;
                
            } catch (error) {
                console.error('Auto-login failed:', error);
                showError();
            }
        }

        function updateStep(stepNumber, status) {
            const step = document.getElementById(`step-${stepNumber}`);
            const icon = step.querySelector('i');
            
            step.className = `w-6 h-6 rounded-full flex items-center justify-center text-xs ${
                status === 'success' ? 'bg-green-500 text-white' :
                status === 'loading' ? 'bg-blue-500 text-white' :
                'bg-gray-200'
            }`;
            
            if (status === 'success') {
                icon.className = 'fas fa-check';
            } else if (status === 'loading') {
                icon.className = 'fas fa-spinner fa-spin';
            }
        }

        function showError() {
            document.getElementById('error-state').classList.remove('hidden');
        }

        function showLoginForm() {
            document.getElementById('error-state').classList.add('hidden');
            document.getElementById('login-form').classList.remove('hidden');
        }

        // Login form handling
        document.getElementById('phone-login-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const phone = document.getElementById('login-phone').value.replace(/\D/g, '');
            
            if (phone.length < 10) {
                showNotification('Digite um telefone válido', 'error');
                return;
            }

            // Store phone and set session
            localStorage.setItem('user_phone', phone);
            
            // Set session via API call
            await fetch('/api/set-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ phone: phone })
            });
            
            window.location.href = `/profile`;
        });

        function showNotification(message, type) {
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
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        function sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }
    </script>
</body>
</html>