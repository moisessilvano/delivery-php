<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Seus Dados - <?= htmlspecialchars($establishment['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: <?= $establishment['primary_color'] ?? '#3B82F6' ?>;
            --secondary-color: <?= $establishment['secondary_color'] ?? '#1E40AF' ?>;
        }
        .primary-bg { background-color: var(--primary-color); }
        .primary-text { color: var(--primary-color); }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="p-4 flex items-center">
            <button onclick="history.back()" class="mr-3">
                <i class="fas fa-arrow-left text-xl"></i>
            </button>
            <h1 class="text-lg font-bold">Seus Dados</h1>
        </div>
    </header>

    <!-- Progress -->
    <div class="bg-white border-b">
        <div class="p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 primary-bg text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                    <span class="ml-2 text-sm font-medium">Seus Dados</span>
                </div>
                <div class="flex-1 mx-4 h-1 bg-gray-200 rounded">
                    <div class="h-1 primary-bg rounded" style="width: 33%"></div>
                </div>
                <div class="flex items-center text-gray-400">
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-sm">2</div>
                    <span class="ml-2 text-sm">Endereço</span>
                </div>
                <div class="flex-1 mx-4 h-1 bg-gray-200 rounded"></div>
                <div class="flex items-center text-gray-400">
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-sm">3</div>
                    <span class="ml-2 text-sm">Confirmar</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <main class="p-4">
        <form id="checkout-form" class="space-y-4">
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <h2 class="text-lg font-bold mb-4">Informações de Contato</h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Telefone/WhatsApp *
                        </label>
                        <input type="tel" id="customer_phone" name="customer_phone" required
                               class="w-full p-3 border border-gray-300 rounded-lg phone-mask focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="(11) 99999-9999" maxlength="15">
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Usaremos para confirmar seu pedido
                        </p>
                    </div>

                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Nome Completo *
                        </label>
                        <input type="text" id="customer_name" name="customer_name" required
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Seu nome completo">
                    </div>

                    <div>
                        <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email (opcional)
                        </label>
                        <input type="email" id="customer_email" name="customer_email"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="seu@email.com">
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <h3 class="font-bold mb-3">Resumo do Pedido</h3>
                <div id="cart-summary" class="space-y-2 mb-3">
                    <!-- Cart items will be loaded here -->
                </div>
                <div class="border-t pt-3">
                    <div class="flex justify-between items-center">
                        <span class="font-bold">Total:</span>
                        <span class="text-xl font-bold primary-text" id="total-amount">R$ 0,00</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full primary-bg text-white py-4 rounded-xl font-bold text-lg">
                Continuar para Endereço
                <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </form>
    </main>

    <script src="/js/app.js"></script>
    <script>
        // Load cart data
        const cart = JSON.parse(localStorage.getItem('cart') || '{}');
        const products = JSON.parse(localStorage.getItem('products') || '{}');

        // Populate cart summary
        function populateCartSummary() {
            const cartSummary = document.getElementById('cart-summary');
            let totalAmount = 0;

            cartSummary.innerHTML = '';

            Object.keys(cart).forEach(productId => {
                const quantity = cart[productId];
                const product = products[productId];
                const itemTotal = quantity * product.price;
                totalAmount += itemTotal;

                const item = document.createElement('div');
                item.className = 'flex justify-between items-center text-sm';
                item.innerHTML = `
                    <span>${quantity}x ${product.name}</span>
                    <span>R$ ${itemTotal.toFixed(2).replace('.', ',')}</span>
                `;
                cartSummary.appendChild(item);
            });

            document.getElementById('total-amount').textContent = 
                'R$ ' + totalAmount.toFixed(2).replace('.', ',');
        }

        // Format phone number as user types
        document.getElementById('customer_phone').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            
            if (value.length >= 11) {
                value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
            } else if (value.length >= 7) {
                value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            } else if (value.length >= 3) {
                value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
            }
            
            this.value = value;
        });

        // Check for existing user data by phone
        document.getElementById('customer_phone').addEventListener('blur', async function() {
            const phone = this.value.trim();
            if (phone.length < 10) return;

            // Remove mask from phone before sending
            const phoneDigits = phone.replace(/\D/g, '');
            if (phoneDigits.length < 10) return;

            try {
                const response = await fetch('/api/customer-by-phone', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ phone: phoneDigits })
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.customer) {
                        // Fill form with existing data
                        document.getElementById('customer_name').value = data.customer.name || '';
                        document.getElementById('customer_email').value = data.customer.email || '';
                        
                        // Store customer data for next steps
                        localStorage.setItem('customer_data', JSON.stringify(data.customer));
                        
                        console.log('Dados do cliente preenchidos automaticamente');
                    }
                } else {
                    console.log('Cliente não encontrado - campos permanecerão vazios');
                }
            } catch (error) {
                console.error('Error checking customer:', error);
            }
        });

        // Form submission
        document.getElementById('checkout-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const customerData = {
                phone: formData.get('customer_phone').replace(/\D/g, ''), // Remove mask
                name: formData.get('customer_name'),
                email: formData.get('customer_email')
            };

            // Store data for next step
            localStorage.setItem('checkout_customer', JSON.stringify(customerData));

            // Perform auto login
            await performAutoLogin(customerData);

            // Proceed to step 2
            window.location.href = '/checkout-step2';
        });

        // Auto login function
        async function performAutoLogin(customerData) {
            try {
                const phone = customerData.phone; // Phone already cleaned in form submission
                
                // Check if customer exists
                const response = await fetch('/api/customer-by-phone', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ phone: phone })
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.customer) {
                        // Existing customer - store phone and set session
                        localStorage.setItem('user_phone', phone);
                        
                        // Set session via API call
                        await fetch('/api/set-session', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ phone: phone })
                        });
                        
                        console.log('Login automático realizado!');
                    } else {
                        // New customer - create account
                        await createCustomerAccount(customerData, phone);
                    }
                } else {
                    // API error - create new customer
                    await createCustomerAccount(customerData, phone);
                }
            } catch (error) {
                console.error('Auto login error:', error);
                // Continue anyway - user can still proceed with checkout
            }
        }

        // Create customer account
        async function createCustomerAccount(customerData, phone) {
            try {
                const createResponse = await fetch('/api/create-customer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        phone: phone,
                        name: customerData.name,
                        email: customerData.email,
                        cep: '',
                        address: '',
                        number: '',
                        complement: '',
                        neighborhood: '',
                        city: '',
                        state: ''
                    })
                });

                if (createResponse.ok) {
                    localStorage.setItem('user_phone', phone);
                    
                    // Set session via API call
                    await fetch('/api/set-session', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ phone: phone })
                    });
                    
                    console.log('Conta criada e login realizado!');
                } else {
                    throw new Error('Erro ao criar conta');
                }
            } catch (error) {
                console.error('Create customer error:', error);
                console.log('Erro ao criar conta, mas você pode continuar');
            }
        }

        // Initialize
        populateCartSummary();
    </script>
</body>
</html>
