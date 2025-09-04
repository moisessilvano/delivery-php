<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($establishment['name']) ?> - Cardápio</title>
    <link href="/css/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="description" content="<?= htmlspecialchars($establishment['description']) ?>">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <?php if ($establishment['logo']): ?>
                    <img src="/<?= htmlspecialchars($establishment['logo']) ?>" 
                         alt="Logo" class="h-12 w-12 object-cover rounded">
                    <?php endif; ?>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($establishment['name']) ?></h1>
                        <?php if ($establishment['description']): ?>
                        <p class="text-gray-600"><?= htmlspecialchars($establishment['description']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <?php if ($establishment['phone']): ?>
                        <?php if ($establishment['is_whatsapp']): ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $establishment['phone']) ?>" 
                           target="_blank" class="flex items-center text-green-600 hover:text-green-700">
                            <i class="fab fa-whatsapp mr-2"></i>
                            WhatsApp
                        </a>
                        <?php else: ?>
                        <a href="tel:<?= htmlspecialchars($establishment['phone']) ?>" 
                           class="flex items-center text-gray-600 hover:text-gray-700">
                            <i class="fas fa-phone mr-2"></i>
                            <?= htmlspecialchars($establishment['phone']) ?>
                        </a>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <button id="cart-button" class="relative bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Carrinho
                        <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center hidden">0</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Category Filter - Sticky -->
    <div class="sticky top-0 z-30 bg-white shadow-md mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <select id="category-filter" class="w-full max-w-md mx-auto p-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Todas as categorias</option>
                <?php 
                $categoryOptions = [];
                foreach ($categories as $item) {
                    if (!in_array($item['id'], $categoryOptions)) {
                        $categoryOptions[] = $item['id'];
                        echo '<option value="category-' . $item['id'] . '">' . htmlspecialchars($item['name']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Establishment Info -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">
                        <i class="fas fa-clock mr-2 text-primary-600"></i>
                        Ver tempo e taxa de entrega
                    </h3>
                    <div class="text-sm text-gray-600">
                        <p>Pedido mínimo: R$ <?= number_format($establishment['min_order_value'], 2, ',', '.') ?></p>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-semibold text-gray-900 mb-2">Suas vantagens</h3>
                    <div class="space-y-1 text-sm">
                        <div class="flex items-center text-orange-600">
                            <i class="fas fa-star mr-2"></i>
                            <span>Entre ou cadastre-se e participe do</span>
                        </div>
                        <div class="font-semibold text-orange-600">Programa de Fidelidade!</div>
                    </div>
                </div>
                
                <div>
                    <div class="bg-gray-800 text-white p-3 rounded-lg text-center">
                        <i class="fas fa-truck mr-2"></i>
                        <span class="text-sm">Entrega grátis em pedidos acima de R$ 199,99!</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Horários de Funcionamento -->
        <?php if (!empty($establishment['special_hours_note'])): ?>
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Observações sobre horários
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p><?= htmlspecialchars($establishment['special_hours_note']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Menu Categories -->
        <div class="space-y-8">
            <?php 
            $currentCategory = null;
            foreach ($categories as $item): 
                if ($currentCategory !== $item['id']):
                    $currentCategory = $item['id'];
            ?>
            <div id="category-<?= $item['id'] ?>" class="bg-white rounded-lg shadow-md overflow-hidden category-section">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($item['name']) ?></h2>
                    <?php if ($item['description']): ?>
                    <p class="text-gray-600 mt-1"><?= htmlspecialchars($item['description']) ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <?php 
                        // Get products for this category
                        $categoryProducts = array_filter($categories, function($cat) use ($item) {
                            return $cat['id'] === $item['id'] && $cat['product_id'];
                        });
                        
                        foreach ($categoryProducts as $product): 
                        ?>
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
                            <div class="flex items-center space-x-4 flex-1">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900"><?= htmlspecialchars($product['product_name']) ?></h3>
                                    <?php if ($product['product_description']): ?>
                                    <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($product['product_description']) ?></p>
                                    <?php endif; ?>
                                    <div class="mt-2">
                                        <span class="text-lg font-bold text-gray-900">
                                            R$ <?= number_format($product['product_price'], 2, ',', '.') ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <?php if ($product['product_image']): ?>
                                <img src="/<?= htmlspecialchars($product['product_image']) ?>" 
                                     alt="<?= htmlspecialchars($product['product_name']) ?>" 
                                     class="w-16 h-16 object-cover rounded-lg">
                                <?php endif; ?>
                            </div>
                            
                            <div class="ml-4">
                                <button onclick="addToCart(<?= htmlspecialchars(json_encode([
                                    'id' => $product['product_id'],
                                    'name' => $product['product_name'],
                                    'price' => $product['product_price'],
                                    'image' => $product['product_image']
                                ])) ?>)" 
                                        class="bg-primary-600 text-white w-10 h-10 rounded-full hover:bg-primary-700 transition-colors flex items-center justify-center">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>
    </main>

    <!-- Cart Sidebar -->
    <div id="cart-sidebar" class="fixed inset-y-0 right-0 w-96 bg-white shadow-xl transform translate-x-full transition-transform duration-300 ease-in-out z-50">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold">Carrinho</h2>
                <button id="close-cart" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="cart-items" class="flex-1 overflow-y-auto p-4">
                <div id="empty-cart" class="text-center py-8">
                    <i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Seu carrinho está vazio</p>
                </div>
            </div>
            
            <div class="border-t p-4">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-lg font-semibold">Total:</span>
                    <span id="cart-total" class="text-xl font-bold text-primary-600">R$ 0,00</span>
                </div>
                
                <button id="checkout-btn" onclick="goToCheckout()" 
                        class="w-full bg-primary-600 text-white py-3 rounded-lg hover:bg-primary-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed"
                        disabled>
                    Finalizar Pedido
                </button>
            </div>
        </div>
    </div>

    <!-- Cart Overlay -->
    <div id="cart-overlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40"></div>

    <!-- Success Message -->
    <?php if (isset($_GET['order_success'])): ?>
    <div id="success-message" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>Pedido #<?= $_GET['order_success'] ?> realizado com sucesso!</span>
        </div>
    </div>
    <?php endif; ?>

    <script>
        let cart = [];
        
        // Cart functions
        function addToCart(product) {
            const existingItem = cart.find(item => item.id === product.id);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    ...product,
                    quantity: 1,
                    options: []
                });
            }
            
            updateCartDisplay();
        }
        
        function removeFromCart(productId) {
            cart = cart.filter(item => item.id !== productId);
            updateCartDisplay();
        }
        
        function updateQuantity(productId, quantity) {
            const item = cart.find(item => item.id === productId);
            if (item) {
                if (quantity <= 0) {
                    removeFromCart(productId);
                } else {
                    item.quantity = quantity;
                }
                updateCartDisplay();
            }
        }
        
        function updateCartDisplay() {
            const cartCount = document.getElementById('cart-count');
            const cartItems = document.getElementById('cart-items');
            const cartTotal = document.getElementById('cart-total');
            const emptyCart = document.getElementById('empty-cart');
            const checkoutBtn = document.getElementById('checkout-btn');
            
            // Update count
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            cartCount.textContent = totalItems;
            cartCount.classList.toggle('hidden', totalItems === 0);
            
            // Update items display
            if (cart.length === 0) {
                emptyCart.classList.remove('hidden');
                cartItems.innerHTML = '<div id="empty-cart" class="text-center py-8"><i class="fas fa-shopping-cart text-4xl text-gray-300 mb-4"></i><p class="text-gray-500">Seu carrinho está vazio</p></div>';
                checkoutBtn.disabled = true;
            } else {
                emptyCart.classList.add('hidden');
                cartItems.innerHTML = cart.map(item => `
                    <div class="flex items-center space-x-3 mb-4 p-3 border border-gray-200 rounded">
                        <div class="flex-1">
                            <h4 class="font-medium">${item.name}</h4>
                            <p class="text-sm text-gray-600">R$ ${item.price.toFixed(2).replace('.', ',')}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <span class="w-8 text-center">${item.quantity}</span>
                            <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>
                        <button onclick="removeFromCart(${item.id})" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `).join('');
                checkoutBtn.disabled = false;
            }
            
            // Update total
            const total = cart.reduce((sum, item) => sum + (parseFloat(item.price || 0) * item.quantity), 0);
            cartTotal.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
        }
        
        function goToCheckout() {
            if (cart.length === 0) return;
            
            // Store cart in sessionStorage
            sessionStorage.setItem('cart', JSON.stringify(cart));
            window.location.href = '/cart';
        }
        
        // Cart sidebar toggle
        document.getElementById('cart-button').addEventListener('click', () => {
            document.getElementById('cart-sidebar').classList.remove('translate-x-full');
            document.getElementById('cart-overlay').classList.remove('hidden');
        });
        
        document.getElementById('close-cart').addEventListener('click', () => {
            document.getElementById('cart-sidebar').classList.add('translate-x-full');
            document.getElementById('cart-overlay').classList.add('hidden');
        });
        
        document.getElementById('cart-overlay').addEventListener('click', () => {
            document.getElementById('cart-sidebar').classList.add('translate-x-full');
            document.getElementById('cart-overlay').classList.add('hidden');
        });
        
        // Category filter functionality
        document.getElementById('category-filter').addEventListener('change', function() {
            const selectedCategory = this.value;
            const categorySections = document.querySelectorAll('.category-section');
            
            categorySections.forEach(section => {
                if (selectedCategory === '' || section.id === selectedCategory) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        });

        // Auto-hide success message
        setTimeout(() => {
            const successMsg = document.getElementById('success-message');
            if (successMsg) {
                successMsg.style.transition = 'opacity 0.5s';
                successMsg.style.opacity = '0';
                setTimeout(() => successMsg.remove(), 500);
            }
        }, 5000);
    </script>
</body>
</html>

