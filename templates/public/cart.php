<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - <?= htmlspecialchars($establishment['name']) ?></title>
    <link href="/css/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-primary-600 hover:text-primary-700">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar ao Cardápio
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
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Seu Carrinho</h2>
            
            <div id="cart-items" class="space-y-4 mb-6">
                <!-- Cart items will be loaded here -->
            </div>
            
            <div class="border-t pt-6">
                <div class="flex justify-between items-center mb-6">
                    <span class="text-xl font-semibold text-gray-900">Total:</span>
                    <span id="cart-total" class="text-2xl font-bold text-primary-600">R$ 0,00</span>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <a href="/" class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Continuar Comprando
                    </a>
                    <button id="checkout-btn" onclick="goToCheckout()" 
                            class="btn-primary disabled:bg-gray-300 disabled:cursor-not-allowed"
                            disabled>
                        <i class="fas fa-credit-card mr-2"></i>Finalizar Pedido
                    </button>
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
                updateCartDisplay();
            } else {
                showEmptyCart();
            }
        }
        
        function showEmptyCart() {
            document.getElementById('cart-items').innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Seu carrinho está vazio</h3>
                    <p class="text-gray-500 mb-6">Adicione alguns itens do cardápio para continuar</p>
                    <a href="/" class="btn-primary">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar ao Cardápio
                    </a>
                </div>
            `;
            document.getElementById('checkout-btn').disabled = true;
        }
        
        function updateCartDisplay() {
            const cartItems = document.getElementById('cart-items');
            const cartTotal = document.getElementById('cart-total');
            const checkoutBtn = document.getElementById('checkout-btn');
            
            if (cart.length === 0) {
                showEmptyCart();
                return;
            }
            
            cartItems.innerHTML = cart.map(item => `
                <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">${item.name}</h3>
                        <p class="text-sm text-gray-600">R$ ${item.price.toFixed(2).replace('.', ',')} cada</p>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})" 
                                class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <span class="w-12 text-center font-medium">${item.quantity}</span>
                        <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})" 
                                class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                    
                    <div class="text-right">
                        <p class="font-semibold text-gray-900">
                            R$ ${(parseFloat(item.price || 0) * item.quantity).toFixed(2).replace('.', ',')}
                        </p>
                    </div>
                    
                    <button onclick="removeFromCart(${item.id})" 
                            class="text-red-500 hover:text-red-700 p-2">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `).join('');
            
            // Update total
            const total = cart.reduce((sum, item) => sum + (parseFloat(item.price || 0) * item.quantity), 0);
            cartTotal.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
            
            checkoutBtn.disabled = false;
        }
        
        function updateQuantity(productId, quantity) {
            const item = cart.find(item => item.id === productId);
            if (item) {
                if (quantity <= 0) {
                    removeFromCart(productId);
                } else {
                    item.quantity = quantity;
                    sessionStorage.setItem('cart', JSON.stringify(cart));
                    updateCartDisplay();
                }
            }
        }
        
        function removeFromCart(productId) {
            cart = cart.filter(item => item.id !== productId);
            sessionStorage.setItem('cart', JSON.stringify(cart));
            updateCartDisplay();
        }
        
        function goToCheckout() {
            if (cart.length === 0) return;
            
            // Store cart in sessionStorage
            sessionStorage.setItem('cart', JSON.stringify(cart));
            window.location.href = '/checkout';
        }
        
        // Load cart on page load
        loadCart();
    </script>
</body>
</html>




