<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Confirmar Pedido - <?= htmlspecialchars($establishment['name']) ?></title>
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
            <h1 class="text-lg font-bold">Confirmar Pedido</h1>
        </div>
    </header>

    <!-- Progress -->
    <div class="bg-white border-b">
        <div class="p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center text-green-600">
                    <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm">
                        <i class="fas fa-check"></i>
                    </div>
                    <span class="ml-2 text-sm">Dados</span>
                </div>
                <div class="flex-1 mx-4 h-1 bg-green-500 rounded"></div>
                <div class="flex items-center text-green-600">
                    <div class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm">
                        <i class="fas fa-check"></i>
                    </div>
                    <span class="ml-2 text-sm">Endereço</span>
                </div>
                <div class="flex-1 mx-4 h-1 bg-green-500 rounded"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 primary-bg text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                    <span class="ml-2 text-sm font-medium">Confirmar</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Content -->
    <main class="p-4 space-y-4">
        <!-- Order Items -->
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <h2 class="text-lg font-bold mb-4">Seu Pedido</h2>
            <div id="order-items" class="space-y-3 mb-4">
                <!-- Items will be populated here -->
            </div>
            <div class="border-t pt-3 space-y-2">
                <div class="flex justify-between text-sm">
                    <span>Subtotal:</span>
                    <span id="subtotal">R$ 0,00</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span>Taxa de entrega:</span>
                    <span class="delivery-fee">R$ <?= number_format($establishment['delivery_fee'] ?? 0, 2, ',', '.') ?></span>
                </div>
                <div class="flex justify-between text-lg font-bold primary-text border-t pt-2">
                    <span>Total:</span>
                    <span class="total-amount">R$ 0,00</span>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold">Seus Dados</h3>
                <button onclick="window.location.href='/checkout-step1'" class="text-blue-600 text-sm">
                    <i class="fas fa-edit mr-1"></i>Editar
                </button>
            </div>
            <div id="customer-info" class="text-sm text-gray-600 space-y-1">
                <!-- Customer info will be populated here -->
            </div>
        </div>

        <!-- Delivery Type Selection -->
        <?php if ($establishment['accepts_pickup']): ?>
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <h3 class="font-bold mb-3">Tipo de Pedido</h3>
            <div class="space-y-3">
                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="delivery_type" value="delivery" checked 
                           class="delivery-type-radio mr-3 text-blue-600 focus:ring-blue-500">
                    <div>
                        <span class="font-medium">Entrega</span>
                        <p class="text-sm text-gray-600">Entregamos no seu endereço</p>
                    </div>
                </label>
                
                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="delivery_type" value="pickup" 
                           class="delivery-type-radio mr-3 text-blue-600 focus:ring-blue-500">
                    <div>
                        <span class="font-medium">Retirada no Local</span>
                        <p class="text-sm text-gray-600">Você retira no estabelecimento</p>
                    </div>
                </label>
            </div>
        </div>
        <?php endif; ?>

        <!-- Delivery Address -->
        <div class="bg-white rounded-xl p-4 shadow-sm" id="address-section">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold">Endereço de Entrega</h3>
                <button onclick="window.location.href='/checkout-step2'" class="text-blue-600 text-sm">
                    <i class="fas fa-edit mr-1"></i>Editar
                </button>
            </div>
            <div id="delivery-address" class="text-sm text-gray-600">
                <!-- Address will be populated here -->
            </div>
        </div>

        <!-- Payment Method -->
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <h3 class="font-bold mb-3">Forma de Pagamento</h3>
            <div class="space-y-2">
                <?php foreach ($payment_methods as $method): ?>
                <label class="flex items-center p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="payment_method" value="<?= $method['id'] ?>" 
                           class="mr-3 text-blue-600 focus:ring-blue-500" required>
                    <div class="flex items-center">
                        <i class="fas fa-credit-card mr-2 text-gray-400"></i>
                        <span><?= htmlspecialchars($method['name']) ?></span>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
            <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                <div class="flex items-center text-blue-800 text-sm">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span>Pagamento na entrega</span>
                </div>
            </div>
        </div>

        <!-- Observations -->
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <h3 class="font-bold mb-3">Observações (opcional)</h3>
            <textarea id="observations" placeholder="Alguma observação especial sobre seu pedido?"
                      class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      rows="3"></textarea>
        </div>

        <!-- Delivery Time -->
        <div class="bg-green-50 rounded-xl p-4">
            <div class="flex items-center text-green-800 mb-2">
                <i class="fas fa-clock mr-2"></i>
                <span class="font-medium">Tempo de Entrega</span>
            </div>
            <div class="text-sm text-green-700">
                Seu pedido chegará em aproximadamente <strong><?= $establishment['delivery_time'] ?? 30 ?> minutos</strong>
            </div>
        </div>

        <!-- Confirm Button -->
        <button id="confirm-order" disabled class="w-full bg-gray-400 text-white py-4 rounded-xl font-bold text-lg">
            <i class="fas fa-check mr-2"></i>
            Confirmar Pedido
        </button>
    </main>

    <!-- Loading Modal -->
    <div id="loading-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 m-4 text-center">
            <div class="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full mx-auto mb-4"></div>
            <p class="text-gray-600">Processando seu pedido...</p>
        </div>
    </div>

    <script src="/js/app.js"></script>
    <script>
        // Load data from localStorage
        const cart = JSON.parse(localStorage.getItem('cart') || '{}');
        const products = JSON.parse(localStorage.getItem('products') || '{}');
        const customerData = JSON.parse(localStorage.getItem('checkout_customer') || '{}');
        const addressData = JSON.parse(localStorage.getItem('checkout_address') || '{}');
        
        const deliveryFee = <?= $establishment['delivery_fee'] ?? 0 ?>;

        // Populate order items
        function populateOrderItems() {
            const orderItems = document.getElementById('order-items');
            let subtotal = 0;

            orderItems.innerHTML = '';

            // Use detailed cart items if available (includes customizations)
            const cartItems = JSON.parse(localStorage.getItem('cart_items') || '[]');
            
            if (cartItems.length > 0) {
                // Use cart items with customizations
                cartItems.forEach(item => {
                    const itemTotal = item.price * item.quantity;
                    subtotal += itemTotal;

                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'space-y-2';
                    itemDiv.innerHTML = `
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="font-medium">${item.name}</div>
                                ${item.options && item.options.length > 0 ? `
                                    <div class="text-sm text-gray-600 mt-1">
                                        ${item.options.map(opt => 
                                            `• ${opt.name}${opt.price > 0 ? ` (+R$ ${opt.price.toFixed(2).replace('.', ',')})` : ''}`
                                        ).join('<br>')}
                                    </div>
                                ` : ''}
                                <div class="text-sm text-gray-500">${item.quantity}x R$ ${item.price.toFixed(2).replace('.', ',')}</div>
                            </div>
                            <div class="font-bold">R$ ${itemTotal.toFixed(2).replace('.', ',')}</div>
                        </div>
                    `;
                    orderItems.appendChild(itemDiv);
                });
            } else {
                // Fallback to basic cart
                Object.keys(cart).forEach(productId => {
                    const quantity = cart[productId];
                    const product = products[productId];
                    const itemTotal = quantity * product.price;
                    subtotal += itemTotal;

                    const item = document.createElement('div');
                    item.className = 'flex justify-between items-center';
                    item.innerHTML = `
                        <div>
                            <div class="font-medium">${product.name}</div>
                            <div class="text-sm text-gray-500">${quantity}x R$ ${product.price.toFixed(2).replace('.', ',')}</div>
                        </div>
                        <div class="font-bold">R$ ${itemTotal.toFixed(2).replace('.', ',')}</div>
                    `;
                    orderItems.appendChild(item);
                });
            }

            // Update totals
            document.getElementById('subtotal').textContent = 'R$ ' + subtotal.toFixed(2).replace('.', ',');
            updateOrderTotal();
        }

        // Populate customer info
        function populateCustomerInfo() {
            const customerInfo = document.getElementById('customer-info');
            customerInfo.innerHTML = `
                <div><i class="fas fa-user mr-2"></i>${customerData.name}</div>
                <div><i class="fas fa-phone mr-2"></i>${customerData.phone}</div>
                ${customerData.email ? `<div><i class="fas fa-envelope mr-2"></i>${customerData.email}</div>` : ''}
            `;
        }

        // Populate delivery address
        function populateDeliveryAddress() {
            const deliveryAddress = document.getElementById('delivery-address');
            const fullAddress = `${addressData.address}, ${addressData.number}${addressData.complement ? ', ' + addressData.complement : ''}<br>
                                ${addressData.neighborhood}, ${addressData.city} - ${addressData.state}<br>
                                CEP: ${addressData.cep}
                                ${addressData.reference ? '<br>Ref: ' + addressData.reference : ''}`;
            deliveryAddress.innerHTML = fullAddress;
        }

        // Handle delivery type change
        function handleDeliveryTypeChange() {
            const addressSection = document.getElementById('address-section');
            const deliveryType = document.querySelector('input[name="delivery_type"]:checked')?.value || 'delivery';
            
            if (deliveryType === 'pickup') {
                addressSection.style.display = 'none';
            } else {
                addressSection.style.display = 'block';
            }
            
            updateOrderTotal();
        }

        // Update order total based on delivery type
        function updateOrderTotal() {
            const deliveryType = document.querySelector('input[name="delivery_type"]:checked')?.value || 'delivery';
            const deliveryFeeEl = document.querySelector('.delivery-fee');
            const totalEl = document.querySelector('.total-amount');
            
            let subtotal = 0;
            
            // Use detailed cart items if available (includes customizations)
            const cartItems = JSON.parse(localStorage.getItem('cart_items') || '[]');
            
            if (cartItems.length > 0) {
                // Calculate using cart items with customizations
                cartItems.forEach(item => {
                    subtotal += (item.price || 0) * (item.quantity || 1);
                });
            } else {
                // Fallback to basic cart calculation
                Object.keys(cart).forEach(productId => {
                    const quantity = cart[productId];
                    const product = products[productId];
                    if (product && product.price) {
                        subtotal += product.price * quantity;
                    }
                });
            }
            
            const fee = deliveryType === 'pickup' ? 0 : deliveryFee;
            const total = subtotal + fee;
            
            if (deliveryFeeEl) {
                deliveryFeeEl.textContent = deliveryType === 'pickup' ? 'Grátis (Retirada)' : `R$ ${fee.toFixed(2).replace('.', ',')}`;
            }
            
            if (totalEl) {
                totalEl.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
            }
        }

        // Enable confirm button when payment method is selected
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const confirmBtn = document.getElementById('confirm-order');
                confirmBtn.disabled = false;
                confirmBtn.className = 'w-full primary-bg text-white py-4 rounded-xl font-bold text-lg hover:opacity-90 transition-opacity';
            });
        });

        // Handle delivery type change
        document.querySelectorAll('.delivery-type-radio').forEach(radio => {
            radio.addEventListener('change', handleDeliveryTypeChange);
        });

        // Confirm order
        document.getElementById('confirm-order').addEventListener('click', async function() {
            if (this.disabled) return;

            const paymentMethodId = document.querySelector('input[name="payment_method"]:checked')?.value;
            if (!paymentMethodId) {
                alert('Por favor, selecione uma forma de pagamento');
                return;
            }

            // Show loading
            document.getElementById('loading-modal').classList.remove('hidden');

            const deliveryType = document.querySelector('input[name="delivery_type"]:checked')?.value || 'delivery';
            
            // Get detailed cart items with customizations
            const cartItems = JSON.parse(localStorage.getItem('cart_items') || '[]');
            
            const orderData = {
                customer: customerData,
                address: addressData,
                cart: cart,
                cart_items: cartItems, // Include detailed cart with customizations
                delivery_type: deliveryType,
                payment_method_id: paymentMethodId,
                observations: document.getElementById('observations').value
            };

            try {
                const response = await fetch('/api/place-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(orderData)
                });

                if (response.ok) {
                    const result = await response.json();
                    
                    // Clear localStorage
                    localStorage.removeItem('cart');
                    localStorage.removeItem('products');
                    localStorage.removeItem('cart_items');
                    localStorage.removeItem('checkout_customer');
                    localStorage.removeItem('checkout_address');

                    // Redirect to success page
                    window.location.href = `/order-success/${result.order_id}`;
                } else {
                    throw new Error('Erro ao processar pedido');
                }
            } catch (error) {
                alert('Erro ao finalizar pedido. Tente novamente.');
                console.error(error);
            } finally {
                document.getElementById('loading-modal').classList.add('hidden');
            }
        });

        // Initialize
        populateOrderItems();
        populateCustomerInfo();
        populateDeliveryAddress();
    </script>
</body>
</html>
