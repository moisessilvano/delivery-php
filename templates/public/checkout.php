<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Pedido - <?= htmlspecialchars($establishment['name']) ?></title>
    <link href="/css/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="/cart" class="text-primary-600 hover:text-primary-700">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar ao Carrinho
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <?php if ($establishment['logo']): ?>
                    <img src="/<?= htmlspecialchars($establishment['logo']) ?>" 
                         alt="Logo" class="h-8 w-8 object-cover rounded">
                    <?php endif; ?>
                    <h1 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($establishment['name']) ?></h1>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Order Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Finalizar Pedido</h2>
                
                <form id="checkout-form" method="POST" class="space-y-6">
                    <div>
                        <label for="customer_name" class="form-label">Nome Completo *</label>
                        <input type="text" id="customer_name" name="customer_name" required 
                               class="form-input" placeholder="Seu nome completo">
                    </div>
                    
                    <div>
                        <label for="customer_phone" class="form-label">Telefone/WhatsApp *</label>
                        <input type="tel" id="customer_phone" name="customer_phone" required 
                               class="form-input" placeholder="(11) 99999-9999">
                    </div>
                    
                    <!-- Delivery Type Selection -->
                    <div>
                        <label class="form-label">Tipo de Pedido *</label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="radio" name="delivery_type" value="delivery" checked 
                                       class="delivery-type-radio mr-3 text-primary-600 focus:ring-primary-500">
                                <div>
                                    <span class="font-medium">Entrega</span>
                                    <p class="text-sm text-gray-600">Entregamos no seu endereço</p>
                                </div>
                            </label>
                            
                            <?php if ($establishment['accepts_pickup']): ?>
                            <label class="flex items-center">
                                <input type="radio" name="delivery_type" value="pickup" 
                                       class="delivery-type-radio mr-3 text-primary-600 focus:ring-primary-500">
                                <div>
                                    <span class="font-medium">Retirada no Local</span>
                                    <p class="text-sm text-gray-600">Você retira no estabelecimento</p>
                                </div>
                            </label>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div id="address-section">
                        <label for="customer_address" class="form-label">Endereço de Entrega *</label>
                        <textarea id="customer_address" name="customer_address" required rows="3" 
                                  class="form-input" placeholder="Rua, número, bairro, cidade"></textarea>
                    </div>
                    
                    <div>
                        <label for="payment_method_id" class="form-label">Forma de Pagamento *</label>
                        <select id="payment_method_id" name="payment_method_id" required class="form-input">
                            <option value="">Selecione a forma de pagamento</option>
                            <?php foreach ($payment_methods as $method): ?>
                            <option value="<?= $method['id'] ?>">
                                <?= htmlspecialchars($method['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Pagamento na entrega
                        </p>
                    </div>
                    
                    <div>
                        <label for="notes" class="form-label">Observações</label>
                        <textarea id="notes" name="notes" rows="3" 
                                  class="form-input" placeholder="Alguma observação especial?"></textarea>
                    </div>
                    
                    <input type="hidden" id="items" name="items" value="">
                    
                    <button type="submit" class="w-full btn-primary">
                        <i class="fas fa-check mr-2"></i>Confirmar Pedido
                    </button>
                </form>
            </div>
            
            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Resumo do Pedido</h2>
                
                <div id="order-summary" class="space-y-4 mb-6">
                    <!-- Order items will be loaded here -->
                </div>
                
                <div class="border-t pt-4">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span id="subtotal">R$ 0,00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Taxa de entrega:</span>
                            <span class="delivery-fee">R$ <?= number_format($establishment['delivery_fee'], 2, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between text-lg font-semibold">
                            <span>Total:</span>
                            <span id="total">R$ 0,00</span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <h3 class="font-semibold text-blue-900 mb-2">Informações de Entrega</h3>
                    <div class="text-sm text-blue-800 space-y-1">
                        <p><strong>Tempo de entrega:</strong> <?= $establishment['delivery_time'] ?> minutos</p>
                        <p><strong>Valor mínimo:</strong> R$ <?= number_format($establishment['min_order_value'], 2, ',', '.') ?></p>
                        <?php if ($establishment['whatsapp']): ?>
                        <p><strong>WhatsApp:</strong> <?= htmlspecialchars($establishment['whatsapp']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        let cart = [];
        
        // Load cart from sessionStorage
        function loadCart() {
            const storedCart = sessionStorage.getItem('cart');
            if (storedCart) {
                cart = JSON.parse(storedCart);
                updateOrderSummary();
            } else {
                // Redirect to menu if no cart
                window.location.href = '/';
            }
        }
        
        function updateOrderSummary() {
            const orderSummary = document.getElementById('order-summary');
            const subtotalEl = document.getElementById('subtotal');
            const totalEl = document.getElementById('total');
            const itemsInput = document.getElementById('items');
            
            if (cart.length === 0) {
                orderSummary.innerHTML = '<p class="text-gray-500">Nenhum item no carrinho</p>';
                return;
            }
            
            // Update items display
            orderSummary.innerHTML = cart.map(item => `
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <div>
                        <h4 class="font-medium">${item.name}</h4>
                        <p class="text-sm text-gray-600">${item.quantity}x R$ ${item.price.toFixed(2).replace('.', ',')}</p>
                    </div>
                    <span class="font-medium">R$ ${(parseFloat(item.price || 0) * item.quantity).toFixed(2).replace('.', ',')}</span>
                </div>
            `).join('');
            
            // Calculate totals
            const subtotal = cart.reduce((sum, item) => sum + (parseFloat(item.price || 0) * item.quantity), 0);
            const deliveryType = document.querySelector('input[name="delivery_type"]:checked')?.value || 'delivery';
            const deliveryFee = deliveryType === 'pickup' ? 0 : <?= $establishment['delivery_fee'] ?>;
            const total = subtotal + deliveryFee;
            
            subtotalEl.textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
            totalEl.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
            
            // Update delivery fee display
            const deliveryFeeEl = document.querySelector('.delivery-fee');
            if (deliveryFeeEl) {
                deliveryFeeEl.textContent = deliveryType === 'pickup' ? 'Grátis (Retirada)' : `R$ ${deliveryFee.toFixed(2).replace('.', ',')}`;
            }
            
            // Update hidden input
            itemsInput.value = JSON.stringify(cart);
            
            // Check minimum order value
            const minOrderValue = <?= $establishment['min_order_value'] ?>;
            if (subtotal < minOrderValue) {
                alert(`O valor mínimo do pedido é R$ ${minOrderValue.toFixed(2).replace('.', ',')}. Seu pedido atual é de R$ ${subtotal.toFixed(2).replace('.', ',')}.`);
                return false;
            }
        }
        
        // Form submission
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const customerName = formData.get('customer_name');
            const customerPhone = formData.get('customer_phone');
            const customerAddress = formData.get('customer_address');
            
            if (!customerName || !customerPhone || !customerAddress) {
                alert('Por favor, preencha todos os campos obrigatórios.');
                return;
            }
            
            if (cart.length === 0) {
                alert('Seu carrinho está vazio.');
                return;
            }
            
            // Check minimum order value
            const subtotal = cart.reduce((sum, item) => sum + (parseFloat(item.price || 0) * item.quantity), 0);
            const minOrderValue = <?= $establishment['min_order_value'] ?>;
            if (subtotal < minOrderValue) {
                alert(`O valor mínimo do pedido é R$ ${minOrderValue.toFixed(2).replace('.', ',')}. Seu pedido atual é de R$ ${subtotal.toFixed(2).replace('.', ',')}.`);
                return;
            }
            
            // Submit form
            this.submit();
        });
        
        // Load cart on page load
        loadCart();
        
        // Handle delivery type change
        document.querySelectorAll('.delivery-type-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const addressSection = document.getElementById('address-section');
                const addressField = document.getElementById('customer_address');
                
                if (this.value === 'pickup') {
                    addressSection.style.display = 'none';
                    addressField.required = false;
                    addressField.value = 'Retirada no local';
                } else {
                    addressSection.style.display = 'block';
                    addressField.required = true;
                    if (addressField.value === 'Retirada no local') {
                        addressField.value = '';
                    }
                }
                updateOrderSummary();
            });
        });
    </script>
</body>
</html>

