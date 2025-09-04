<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($establishment['name']) ?> - Cardápio</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="<?= $establishment['primary_color'] ?? '#3B82F6' ?>">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="<?= htmlspecialchars($establishment['name']) ?>">
    <meta name="msapplication-TileColor" content="<?= $establishment['primary_color'] ?? '#3B82F6' ?>">
    
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
        
        /* Desktop improvements */
        @media (min-width: 768px) {
            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 2rem;
            }
            
            .product-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 1rem;
            }
            
            .mobile-nav {
                display: none;
            }
            
            .cart-footer {
                position: fixed;
                bottom: 2rem;
                right: 2rem;
                left: auto;
                width: auto;
                min-width: 300px;
                border-radius: 1rem;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            }
        }
        
        .primary-bg { background-color: var(--primary-color); }
        .primary-text { color: var(--primary-color); }
        .secondary-bg { background-color: var(--secondary-color); }
        .secondary-text { color: var(--secondary-color); }
        
        /* Status colors - using primary/secondary as base */
        .success-text { color: #10B981; }
        .warning-bg { background-color: #FEF3C7; }
        .warning-border { border-color: #F59E0B; }
        .warning-text { color: #92400E; }
        .error-text { color: #EF4444; }
        
        /* Interactive states */
        .primary-bg:hover { background-color: var(--secondary-color); }
        .primary-bg:disabled { background-color: #9CA3AF; }
        
        /* Navigation */
        .nav-item.active { color: var(--primary-color); }
        .nav-item { color: #6B7280; transition: color 0.2s; }
        .nav-item:hover { color: var(--primary-color); }
        
        /* Modal icons */
        .icon-bg-primary { background-color: var(--primary-color); opacity: 0.1; }
        
        /* Today highlight */
        .today-highlight { background-color: var(--primary-color); opacity: 0.1; }
        .today-text { color: var(--primary-color); font-weight: bold; }
        
        .cart-item { transition: all 0.3s ease; }
        .fade-in { animation: fadeIn 0.3s ease-in; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .sticky-header {
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.95);
        }
        
        /* Mobile Navigation */
        .mobile-nav {
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .nav-item.active {
            color: var(--primary-color);
        }
        
        /* Product Card */
        .product-card {
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.2s ease;
        }
        
        .product-card:active {
            transform: scale(0.98);
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
                        <?php if ($establishment['logo']): ?>
                        <img src="/<?= htmlspecialchars($establishment['logo']) ?>" 
                             alt="<?= htmlspecialchars($establishment['name']) ?>" 
                             class="w-10 h-10 rounded-full object-cover">
                        <?php endif; ?>
                        <div>
                            <h1 class="text-lg font-bold"><?= htmlspecialchars($establishment['name']) ?></h1>
                            <div class="flex items-center space-x-4 text-sm">
                                <span class="text-gray-500">
                                    <i class="fas fa-truck mr-1"></i>
                                    <?= $establishment['delivery_time'] ?? 30 ?> min
                                </span>
                                <?php if ($establishment['phone']): ?>
                                    <?php if ($establishment['is_whatsapp']): ?>
                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $establishment['phone']) ?>" 
                                       target="_blank" class="text-green-600 hover:text-green-700">
                                        <i class="fab fa-whatsapp mr-1"></i>
                                        WhatsApp
                                    </a>
                                    <?php else: ?>
                                    <a href="tel:<?= htmlspecialchars($establishment['phone']) ?>" 
                                       class="text-gray-600 hover:text-gray-700">
                                        <i class="fas fa-phone mr-1"></i>
                                        <?= htmlspecialchars($establishment['phone']) ?>
                                    </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <div id="business-status" class="flex items-center">
                                    <!-- Status will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <button id="cart-button" class="relative primary-bg text-white px-4 py-2 rounded-full font-medium">
                        <i class="fas fa-shopping-cart mr-1"></i>
                        <span id="cart-count" class="hidden absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Business Hours Modal -->
    <div id="hours-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl m-4 max-w-md w-full">
            <div class="p-4 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold">Horário de Funcionamento</h3>
                    <button id="close-hours" class="text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-4 space-y-3">
                <?php 
                $daysOfWeek = [
                    0 => 'Domingo', 1 => 'Segunda', 2 => 'Terça', 3 => 'Quarta',
                    4 => 'Quinta', 5 => 'Sexta', 6 => 'Sábado'
                ];
                foreach ($business_hours as $hour): 
                    $today = date('w');
                    $isToday = $hour['day_of_week'] == $today;
                ?>
                <div class="flex justify-between items-center <?= $isToday ? 'today-highlight p-2 rounded' : '' ?>">
                    <span class="<?= $isToday ? 'today-text' : 'text-gray-700' ?>">
                        <?= $daysOfWeek[$hour['day_of_week']] ?>
                        <?= $isToday ? ' (hoje)' : '' ?>
                    </span>
                    <span class="<?= $isToday ? 'today-text' : 'text-gray-600' ?>">
                        <?php if ($hour['is_closed']): ?>
                            Fechado
                        <?php else: ?>
                            <?= substr($hour['open_time'], 0, 5) ?> - <?= substr($hour['close_time'], 0, 5) ?>
                        <?php endif; ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Category Filter -->
    <div class="sticky top-16 z-30 bg-white border-b">
        <div class="container p-4">
            <select id="category-filter" class="w-full max-w-md mx-auto md:mx-0 p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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

    <!-- Horários de Funcionamento -->
    <?php if (!empty($establishment['special_hours_note'])): ?>
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mx-4 mb-4">
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

    <!-- Menu Content -->
    <main class="container p-4 space-y-6">
        <?php
        $currentCategoryId = null;
        foreach ($categories as $item):
            if ($currentCategoryId !== $item['id']):
                if ($currentCategoryId !== null): ?>
                    </div>
                </section>
                <?php endif;
                $currentCategoryId = $item['id']; ?>
                
        <section id="category-<?= $item['id'] ?>" class="category-section">
            <h2 class="text-xl font-bold mb-4 primary-text"><?= htmlspecialchars($item['name']) ?></h2>
            <div class="space-y-3 md:product-grid">
            <?php endif;
            
            if ($item['product_id']): ?>
                <div class="product-card bg-white rounded-2xl p-4 border border-gray-100 cursor-pointer hover:shadow-md transition-shadow" 
                     data-product-id="<?= $item['product_id'] ?>" 
                     onclick="openProductModal(<?= $item['product_id'] ?>)">
                    <div class="flex items-start space-x-3">
                        <?php if ($item['product_image']): ?>
                        <img src="/<?= htmlspecialchars($item['product_image']) ?>" 
                             alt="<?= htmlspecialchars($item['product_name']) ?>" 
                             class="w-16 h-16 rounded-xl object-cover flex-shrink-0">
                        <?php endif; ?>
                        
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 mb-1"><?= htmlspecialchars($item['product_name']) ?></h3>
                            <?php if ($item['product_description']): ?>
                            <p class="text-sm text-gray-600 mb-2 line-clamp-2"><?= htmlspecialchars($item['product_description']) ?></p>
                            <?php endif; ?>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold primary-text">
                                    R$ <?= number_format($item['product_price'], 2, ',', '.') ?>
                                </span>
                                
                                <div class="w-8 h-8 rounded-full primary-bg text-white flex items-center justify-center">
                                    <i class="fas fa-plus text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif;
        endforeach;
        
        if ($currentCategoryId !== null): ?>
            </div>
        </section>
        <?php endif; ?>
    </main>

    <!-- Mobile Cart Footer -->
    <div id="cart-footer" class="hidden fixed bottom-16 left-0 right-0 bg-white border-t mobile-nav p-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-600">Total do pedido</div>
                <div class="text-lg font-bold primary-text" id="cart-total">R$ 0,00</div>
            </div>
            <button id="checkout-btn" class="primary-bg text-white px-6 py-3 rounded-xl font-medium">
                Finalizar Pedido
            </button>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t mobile-nav">
        <div class="flex items-center justify-around py-2">
            <button class="nav-item active flex flex-col items-center py-2 px-4" data-page="menu">
                <i class="fas fa-utensils text-xl mb-1"></i>
                <span class="text-xs">Cardápio</span>
            </button>
            <button class="nav-item flex flex-col items-center py-2 px-4" data-page="orders" onclick="window.location.href='/orders-list'">
                <i class="fas fa-receipt text-xl mb-1"></i>
                <span class="text-xs">Pedidos</span>
            </button>
            <button class="nav-item flex flex-col items-center py-2 px-4" data-page="profile" onclick="window.location.href='/profile'">
                <i class="fas fa-user text-xl mb-1"></i>
                <span class="text-xs">Perfil</span>
            </button>
        </div>
    </nav>

    <!-- Notification Permission Modal -->
    <div id="notification-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6">
            <div class="text-center">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 icon-bg-primary">
                    <i class="fas fa-bell text-2xl primary-text"></i>
                </div>
                
                <h3 class="text-lg font-bold text-gray-900 mb-2">Receber Notificações</h3>
                <p class="text-gray-600 mb-6">
                    Quer ser notificado sobre o status do seu pedido e receber novidades? 
                    Ative as notificações para não perder nenhuma atualização!
                </p>
                
                <div class="space-y-3">
                    <button id="enable-notifications" class="w-full primary-bg text-white py-3 rounded-lg font-medium hover:opacity-90 transition-opacity">
                        <i class="fas fa-bell mr-2"></i>
                        Ativar Notificações
                    </button>
                    
                    <button id="dismiss-notifications" class="w-full bg-gray-200 text-gray-700 py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                        Agora Não
                    </button>
                </div>
                
                <p class="text-xs text-gray-500 mt-4">
                    Você pode alterar essa configuração a qualquer momento nas configurações do seu navegador.
                </p>
            </div>
        </div>
    </div>

    <!-- PWA Install Banner -->
    <div id="pwa-install-banner" class="hidden fixed bottom-20 left-4 right-4 bg-white border border-gray-200 rounded-xl shadow-lg p-4 z-40">
        <div class="flex items-start space-x-3">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 icon-bg-primary">
                <i class="fas fa-download primary-text"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="font-medium text-gray-900 text-sm">Instalar App</h4>
                <p class="text-xs text-gray-600">Adicione à tela inicial para acesso rápido</p>
            </div>
            <div class="flex space-x-2">
                <button id="install-pwa" class="text-blue-600 text-sm font-medium">Instalar</button>
                <button id="dismiss-pwa" class="text-gray-500 text-sm">×</button>
            </div>
        </div>
    </div>



    <!-- Cart Modal -->
    <div id="cart-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl max-h-96 overflow-hidden">
            <div class="p-4 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold">Seu Pedido</h3>
                    <button id="close-cart" class="text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <div id="cart-items" class="p-4 overflow-y-auto max-h-64 space-y-3">
                <!-- Cart items will be populated here -->
            </div>
            
            <div class="p-4 border-t bg-gray-50">
                <div class="flex items-center justify-between mb-3">
                    <span class="font-medium">Total:</span>
                    <span class="text-xl font-bold primary-text" id="modal-cart-total">R$ 0,00</span>
                </div>
                <button id="modal-checkout-btn" class="w-full primary-bg text-white py-3 rounded-xl font-medium">
                    Continuar
                </button>
            </div>
        </div>
    </div>

    <!-- Product Modal -->
    <div id="product-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="bg-white w-full h-full overflow-y-auto md:max-w-lg md:max-h-[90vh] md:mx-auto md:my-4 md:rounded-lg">
            <div class="sticky top-0 bg-white border-b p-4 flex justify-between items-center">
                <h2 id="modal-product-name" class="text-xl font-bold"></h2>
                <button onclick="closeProductModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="p-4 pb-20 md:pb-4">
                <div id="modal-product-image" class="mb-4"></div>
                <p id="modal-product-description" class="text-gray-600 mb-4"></p>
                <div id="modal-product-price" class="text-2xl font-bold text-primary-600 mb-6"></div>
                
                <div id="modal-customizations" class="space-y-6">
                    <!-- Customizations will be loaded here -->
                </div>
                
                <div class="flex items-center justify-between mb-4 p-4 bg-gray-50 rounded-lg">
                    <span class="font-medium">Quantidade:</span>
                    <div class="flex items-center space-x-3">
                        <button onclick="updateModalQuantity(-1)" class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span id="modal-quantity" class="text-xl font-bold w-8 text-center">1</span>
                        <button onclick="updateModalQuantity(1)" class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                
                <div id="modal-total-price" class="text-xl font-bold text-center mb-4"></div>
            </div>
            
            <!-- Fixed bottom button for mobile, normal button for desktop -->
            <div class="fixed bottom-0 left-0 right-0 bg-white border-t p-4 z-10 md:relative md:border-0 md:p-0 md:mt-4">
                <button id="add-to-cart-btn-mobile" onclick="addProductToCart()" 
                        class="w-full bg-primary-600 text-white py-3 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                    Adicionar ao Carrinho
                </button>
            </div>
        </div>
    </div>

    <script src="/js/app.js"></script>
    <script>
        let cart = [];
        let currentProduct = null;
        let modalQuantity = 1;
        
        // Product Modal functions
        async function openProductModal(productId) {
            try {
                const response = await fetch(`/api/product/${productId}`);
                const product = await response.json();
                
                currentProduct = product;
                modalQuantity = 1;
                
                // Populate modal
                document.getElementById('modal-product-name').textContent = product.name;
                document.getElementById('modal-product-description').textContent = product.description || '';
                document.getElementById('modal-product-price').textContent = `A partir de R$ ${product.price.toFixed(2).replace('.', ',')}`;
                document.getElementById('modal-quantity').textContent = modalQuantity;
                
                // Handle product image
                const imageContainer = document.getElementById('modal-product-image');
                if (product.image) {
                    imageContainer.innerHTML = `<img src="/${product.image}" alt="${product.name}" class="w-full h-48 object-cover rounded-lg">`;
                } else {
                    imageContainer.innerHTML = '';
                }
                
                // Populate customizations
                populateCustomizations(product.option_groups || []);
                
                // Update total price
                updateModalTotalPrice();
                
                // Show modal
                document.getElementById('product-modal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
            } catch (error) {
                console.error('Error loading product:', error);
            }
        }
        
        function closeProductModal() {
            document.getElementById('product-modal').classList.add('hidden');
            document.body.style.overflow = '';
            currentProduct = null;
            modalQuantity = 1;
        }
        
        function populateCustomizations(optionGroups) {
            const container = document.getElementById('modal-customizations');
            container.innerHTML = '';
            
            optionGroups.forEach(group => {
                const groupDiv = document.createElement('div');
                groupDiv.className = 'border border-gray-200 rounded-lg p-4';
                
                const title = document.createElement('h3');
                title.className = 'font-medium mb-3';
                title.textContent = group.name + (group.is_required ? ' *' : '');
                groupDiv.appendChild(title);
                
                const optionsContainer = document.createElement('div');
                optionsContainer.className = 'space-y-2';
                
                group.options.forEach(option => {
                    const optionDiv = document.createElement('div');
                    optionDiv.className = 'flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors';
                    
                    const input = document.createElement('input');
                    input.type = (group.max_selections > 1) ? 'checkbox' : 'radio';
                    input.name = `group_${group.id}`;
                    input.value = option.id;
                    input.dataset.price = option.price;
                    input.className = 'sr-only'; // Hide the actual input
                    input.addEventListener('change', updateModalTotalPrice);
                    
                    const leftSide = document.createElement('div');
                    leftSide.className = 'flex items-center space-x-3 flex-1';
                    
                    // Visual radio/checkbox indicator
                    const indicator = document.createElement('div');
                    indicator.className = 'w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center transition-colors';
                    if (group.max_selections > 1) {
                        indicator.className = 'w-5 h-5 border-2 border-gray-300 rounded flex items-center justify-center transition-colors';
                    }
                    
                    const indicatorInner = document.createElement('div');
                    indicatorInner.className = 'w-2 h-2 bg-primary-600 rounded-full hidden';
                    if (group.max_selections > 1) {
                        indicatorInner.className = 'w-2 h-2 bg-primary-600 rounded hidden';
                    }
                    indicator.appendChild(indicatorInner);
                    
                    const contentDiv = document.createElement('div');
                    contentDiv.className = 'flex items-center space-x-3 flex-1';
                    
                    if (option.image) {
                        const img = document.createElement('img');
                        img.src = `/${option.image}`;
                        img.alt = option.name;
                        img.className = 'w-12 h-12 object-cover rounded';
                        contentDiv.appendChild(img);
                    }
                    
                    const textDiv = document.createElement('div');
                    textDiv.innerHTML = `
                        <div class="font-medium">${option.name}</div>
                        ${option.price > 0 ? `<div class="text-sm text-gray-600">+R$ ${option.price.toFixed(2).replace('.', ',')}</div>` : ''}
                    `;
                    contentDiv.appendChild(textDiv);
                    
                    leftSide.appendChild(indicator);
                    leftSide.appendChild(contentDiv);
                    optionDiv.appendChild(input);
                    optionDiv.appendChild(leftSide);
                    
                    if (option.price > 0) {
                        const priceSpan = document.createElement('span');
                        priceSpan.className = 'text-primary-600 font-medium';
                        priceSpan.textContent = `+R$ ${option.price.toFixed(2).replace('.', ',')}`;
                        optionDiv.appendChild(priceSpan);
                    }
                    
                    // Make entire div clickable
                    optionDiv.addEventListener('click', function(e) {
                        e.preventDefault();
                        input.checked = !input.checked;
                        input.dispatchEvent(new Event('change'));
                    });
                    
                    // Update visual state when input changes
                    input.addEventListener('change', function() {
                        if (input.checked) {
                            indicator.classList.add('border-primary-600', 'bg-primary-50');
                            indicatorInner.classList.remove('hidden');
                        } else {
                            indicator.classList.remove('border-primary-600', 'bg-primary-50');
                            indicatorInner.classList.add('hidden');
                        }
                    });
                    
                    optionsContainer.appendChild(optionDiv);
                });
                
                groupDiv.appendChild(optionsContainer);
                container.appendChild(groupDiv);
            });
        }
        
        function updateModalQuantity(change) {
            modalQuantity = Math.max(1, modalQuantity + change);
            document.getElementById('modal-quantity').textContent = modalQuantity;
            updateModalTotalPrice();
        }
        
        function updateModalTotalPrice() {
            if (!currentProduct) return;
            
            let totalPrice = currentProduct.price;
            
            // Add selected options prices
            const selectedInputs = document.querySelectorAll('#modal-customizations input:checked');
            selectedInputs.forEach(input => {
                totalPrice += parseFloat(input.dataset.price || 0);
            });
            
            totalPrice *= modalQuantity;
            
            document.getElementById('modal-total-price').textContent = `Total: R$ ${totalPrice.toFixed(2).replace('.', ',')}`;
            
            // Update button state
            const requiredGroups = document.querySelectorAll('#modal-customizations h3');
            let allRequiredSelected = true;
            
            requiredGroups.forEach(title => {
                if (title.textContent.includes('*')) {
                    const groupDiv = title.parentElement;
                    const hasSelection = groupDiv.querySelector('input:checked');
                    if (!hasSelection) {
                        allRequiredSelected = false;
                    }
                }
            });
            
            const addBtnMobile = document.getElementById('add-to-cart-btn-mobile');
            
            const enabledClass = 'w-full bg-primary-600 text-white py-3 rounded-lg hover:bg-primary-700 transition-colors font-medium';
            const disabledClass = 'w-full bg-gray-400 text-white py-3 rounded-lg font-medium cursor-not-allowed';
            
            if (addBtnMobile) {
                addBtnMobile.disabled = !allRequiredSelected;
                addBtnMobile.className = allRequiredSelected ? enabledClass : disabledClass;
            }
        }
        
        function addProductToCart() {
            if (!currentProduct) return;
            
            // Get selected options
            const selectedOptions = [];
            const selectedInputs = document.querySelectorAll('#modal-customizations input:checked');
            selectedInputs.forEach(input => {
                const optionDiv = input.closest('.flex.items-center.justify-between');
                const optionName = optionDiv.querySelector('.font-medium').textContent;
                const optionPrice = parseFloat(input.dataset.price || 0);
                
                selectedOptions.push({
                    id: input.value,
                    name: optionName,
                    price: optionPrice
                });
            });
            
            // Calculate item total price
            let itemPrice = currentProduct.price;
            selectedOptions.forEach(option => {
                itemPrice += option.price;
            });
            
            // Create cart item
            const cartItem = {
                id: `${currentProduct.id}_${Date.now()}`, // Unique ID for each customization
                product_id: currentProduct.id,
                name: currentProduct.name,
                price: itemPrice,
                quantity: modalQuantity,
                image: currentProduct.image,
                options: selectedOptions
            };
            
            // Add to cart
            cart.push(cartItem);
            updateUI();
            
            // Close modal and show success message
            closeProductModal();
            showSuccessMessage(`${currentProduct.name} adicionado ao carrinho!`);
        }
        
        function showSuccessMessage(message) {
            // Create temporary success message
            const messageDiv = document.createElement('div');
            messageDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
            messageDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(messageDiv);
            
            // Animate in
            setTimeout(() => {
                messageDiv.classList.remove('translate-x-full');
            }, 100);
            
            // Animate out and remove
            setTimeout(() => {
                messageDiv.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(messageDiv);
                }, 300);
            }, 3000);
        }

        // Update UI
        function updateUI() {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            // Update cart counter
            const cartCount = document.getElementById('cart-count');
            if (totalItems > 0) {
                cartCount.textContent = totalItems;
                cartCount.classList.remove('hidden');
            } else {
                cartCount.classList.add('hidden');
            }
            
            // Update totals
            const formattedTotal = 'R$ ' + totalPrice.toFixed(2).replace('.', ',');
            document.getElementById('cart-total').textContent = formattedTotal;
            document.getElementById('modal-cart-total').textContent = formattedTotal;
            
            // Show/hide cart footer
            const cartFooter = document.getElementById('cart-footer');
            if (totalItems > 0) {
                cartFooter.classList.remove('hidden');
            } else {
                cartFooter.classList.add('hidden');
            }
        }
        
        function proceedToCheckout() {
            if (cart.length === 0) return;
            
            // Prepare cart data for checkout with simplified structure
            const cartData = {};
            const productsData = {};
            
            cart.forEach(item => {
                // Use product_id as key, but handle customizations
                const key = item.product_id;
                
                if (!cartData[key]) {
                    cartData[key] = 0;
                }
                cartData[key] += item.quantity;
                
                if (!productsData[key]) {
                    productsData[key] = {
                        id: item.product_id,
                        name: item.name,
                        price: item.price, // This includes customization prices
                        image: item.image,
                        customizations: []
                    };
                }
                
                // Add customizations info
                if (item.options && item.options.length > 0) {
                    productsData[key].customizations.push({
                        cart_item_id: item.id,
                        quantity: item.quantity,
                        options: item.options,
                        total_price: item.price
                    });
                }
            });
            
            // Store in localStorage for checkout process
            localStorage.setItem('cart', JSON.stringify(cartData));
            localStorage.setItem('products', JSON.stringify(productsData));
            localStorage.setItem('cart_items', JSON.stringify(cart)); // Full cart for processing
            
            window.location.href = '/checkout-step1';
        }



        // Cart modal handlers
        document.getElementById('cart-button').addEventListener('click', openCartModal);
        document.getElementById('checkout-btn').addEventListener('click', proceedToCheckout);
        document.getElementById('close-cart').addEventListener('click', closeCartModal);
        document.getElementById('modal-checkout-btn').addEventListener('click', proceedToCheckout);

        function openCartModal() {
            updateCartModal();
            document.getElementById('cart-modal').classList.remove('hidden');
        }

        function closeCartModal() {
            document.getElementById('cart-modal').classList.add('hidden');
        }

        function updateCartModal() {
            const cartItems = document.getElementById('cart-items');
            cartItems.innerHTML = '';
            
            cart.forEach(item => {
                const total = item.price * item.quantity;
                
                const itemDiv = document.createElement('div');
                itemDiv.className = 'flex items-start justify-between py-2';
                itemDiv.innerHTML = `
                    <div class="flex-1">
                        <div class="font-medium">${item.name}</div>
                        ${item.options && item.options.length > 0 ? `
                            <div class="text-xs text-gray-500 mt-1">
                                ${item.options.map(opt => opt.name + (opt.price > 0 ? ` (+R$ ${opt.price.toFixed(2).replace('.', ',')})` : '')).join(', ')}
                            </div>
                        ` : ''}
                        <div class="text-sm text-gray-600">${item.quantity}x R$ ${item.price.toFixed(2).replace('.', ',')}</div>
                    </div>
                    <div class="font-bold">R$ ${total.toFixed(2).replace('.', ',')}</div>
                `;
                cartItems.appendChild(itemDiv);
            });
        }

        // Category filter
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

        // Mobile navigation
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function() {
                const page = this.dataset.page;
                
                // Remove active class from all items
                document.querySelectorAll('.nav-item').forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');
                
                // Handle navigation
                switch(page) {
                    case 'menu':
                        // Already on menu
                        break;
                    case 'orders':
                        window.location.href = '/orders-list';
                        break;
                    case 'profile':
                        window.location.href = '/profile';
                        break;
                }
            });
        });

        // Business hours functionality
        const businessHours = <?= json_encode($business_hours) ?>;
        
        function checkBusinessStatus() {
            const now = new Date();
            const currentDay = now.getDay();
            const currentTime = now.getHours() * 60 + now.getMinutes(); // minutes since midnight
            
            const todayHours = businessHours.find(h => h.day_of_week == currentDay);
            
            if (!todayHours || todayHours.is_closed == 1) {
                showClosedStatus();
                return false;
            }
            
            const openTime = parseTime(todayHours.open_time);
            const closeTime = parseTime(todayHours.close_time);
            
            const isOpen = currentTime >= openTime && currentTime <= closeTime;
            
            if (isOpen) {
                showOpenStatus(todayHours.close_time);
            } else {
                showClosedStatus(todayHours.open_time);
            }
            
            return isOpen;
        }
        
        function parseTime(timeString) {
            const [hours, minutes] = timeString.split(':').map(Number);
            return hours * 60 + minutes;
        }
        
        function showOpenStatus(closeTime) {
            const statusDiv = document.getElementById('business-status');
            statusDiv.innerHTML = `
                <span class="text-green-600 font-medium cursor-pointer" onclick="openHoursModal()">
                    <i class="fas fa-clock mr-1"></i>Aberto até ${closeTime.substring(0,5)}
                </span>
            `;
        }
        
        function showClosedStatus(nextOpenTime = null) {
            const statusDiv = document.getElementById('business-status');
            const message = nextOpenTime ? `Fechado - Abre às ${nextOpenTime.substring(0,5)}` : 'Fechado';
            statusDiv.innerHTML = `
                <span class="text-red-600 font-medium cursor-pointer" onclick="openHoursModal()">
                    <i class="fas fa-clock mr-1"></i>${message}
                </span>
            `;
            
            // Disable ordering
            disableOrdering();
        }
        
        function disableOrdering() {
            const checkoutBtns = document.querySelectorAll('#checkout-btn, #modal-checkout-btn');
            checkoutBtns.forEach(btn => {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                btn.textContent = 'Estabelecimento Fechado';
            });
        }
        
        function openHoursModal() {
            document.getElementById('hours-modal').classList.remove('hidden');
        }
        
        function closeHoursModal() {
            document.getElementById('hours-modal').classList.add('hidden');
        }
        
        // Event listeners for hours modal
        document.getElementById('close-hours').addEventListener('click', closeHoursModal);
        document.getElementById('hours-modal').addEventListener('click', function(e) {
            if (e.target === this) closeHoursModal();
        });

        // PWA and Notifications functionality
        let deferredPrompt;
        let notificationPermission = localStorage.getItem('notification-permission');
        
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered: ', registration);
                        
                        // Check for notification permission after SW registration
                        setTimeout(checkNotificationPermission, 2000);
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
        
        // PWA Install Banner
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            showPWAInstallBanner();
        });
        
        function showPWAInstallBanner() {
            if (localStorage.getItem('pwa-dismissed') !== 'true') {
                document.getElementById('pwa-install-banner').classList.remove('hidden');
            }
        }
        
        document.getElementById('install-pwa').addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log(`User response to the install prompt: ${outcome}`);
                deferredPrompt = null;
                hidePWAInstallBanner();
            }
        });
        
        document.getElementById('dismiss-pwa').addEventListener('click', () => {
            localStorage.setItem('pwa-dismissed', 'true');
            hidePWAInstallBanner();
        });
        
        function hidePWAInstallBanner() {
            document.getElementById('pwa-install-banner').classList.add('hidden');
        }
        
        // Notification Permission
        function checkNotificationPermission() {
            if (!notificationPermission && 'Notification' in window) {
                setTimeout(() => {
                    document.getElementById('notification-modal').classList.remove('hidden');
                }, 3000); // Show after 3 seconds
            }
        }
        
        document.getElementById('enable-notifications').addEventListener('click', async () => {
            if ('Notification' in window) {
                const permission = await Notification.requestPermission();
                localStorage.setItem('notification-permission', permission);
                
                if (permission === 'granted') {
                    await subscribeToNotifications();
                    showNotification('Notificações ativadas! Você será avisado sobre seus pedidos.', 'success');
                }
            }
            // Always close modal after attempting to enable notifications
            document.getElementById('notification-modal').classList.add('hidden');
        });
        
        document.getElementById('dismiss-notifications').addEventListener('click', () => {
            localStorage.setItem('notification-permission', 'dismissed');
            document.getElementById('notification-modal').classList.add('hidden');
        });
        
        // Subscribe to push notifications
        async function subscribeToNotifications() {
            try {
                const registration = await navigator.serviceWorker.ready;
                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array('BKd-p8C9M3BuOj_1k6RjF7Z_dHsxm5i4n8I8-3dQ4K6_Aj5D6Mn8Z_pQh4d6R5mQ3l8f2A4g1N8c9q6s8a2n') // Default VAPID key for development
                });
                
                // Send subscription to server
                await fetch('/api/subscribe-notifications', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        subscription: subscription,
                        phone: localStorage.getItem('user_phone')
                    })
                });
                
            } catch (error) {
                console.error('Failed to subscribe to notifications:', error);
            }
        }
        
        // Helper function for VAPID key
        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');
            
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
        
        // Store user phone for notifications
        function storeUserPhone(phone) {
            localStorage.setItem('user_phone', phone);
        }

        // Check if user is already logged in
        function checkUserLogin() {
            const userPhone = localStorage.getItem('user_phone');
            if (userPhone) {
                storeUserPhone(userPhone);
            }
        }

        // Delivery calculation functions
        async function calculateDeliveryFee(subtotal) {
            const userPhone = localStorage.getItem('user_phone');
            if (!userPhone || subtotal === 0) return;

            try {
                const response = await fetch('/api/calculate-delivery', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ phone: userPhone })
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        updateCartWithDelivery(subtotal, data.delivery_fee, data.is_free, data.min_order_value);
                    }
                }
            } catch (error) {
                console.log('Error calculating delivery:', error);
            }
        }

        function updateCartWithDelivery(subtotal, deliveryFee, isFree, minOrderValue) {
            const cartModal = document.getElementById('cart-modal');
            if (!cartModal) return;

            // Find or create delivery info element
            let deliveryInfo = cartModal.querySelector('.delivery-info');
            if (!deliveryInfo) {
                deliveryInfo = document.createElement('div');
                deliveryInfo.className = 'delivery-info border-t pt-3 mt-3';
                
                // Insert before the checkout button
                const checkoutBtn = cartModal.querySelector('.bg-blue-600');
                if (checkoutBtn && checkoutBtn.parentNode) {
                    checkoutBtn.parentNode.insertBefore(deliveryInfo, checkoutBtn);
                }
            }

            const total = subtotal + (isFree ? 0 : deliveryFee);
            const showMinOrderWarning = subtotal < minOrderValue;

            deliveryInfo.innerHTML = `
                <div class="space-y-2">
                    <div class="flex justify-between items-center text-sm">
                        <span>Subtotal:</span>
                        <span>R$ ${subtotal.toFixed(2).replace('.', ',')}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span>Taxa de entrega:</span>
                        <span class="${isFree ? 'success-text font-medium' : ''}">
                            ${isFree ? 'Grátis' : `R$ ${deliveryFee.toFixed(2).replace('.', ',')}`}
                        </span>
                    </div>
                    ${showMinOrderWarning ? `
                        <div class="warning-bg warning-border rounded p-2">
                            <p class="warning-text text-xs">
                                Valor mínimo: R$ ${minOrderValue.toFixed(2).replace('.', ',')}
                            </p>
                        </div>
                    ` : ''}
                    <div class="flex justify-between items-center font-bold text-lg border-t pt-2">
                        <span>Total:</span>
                        <span>R$ ${total.toFixed(2).replace('.', ',')}</span>
                    </div>
                </div>
            `;

            // Update cart button and modal total
            const cartTotal = document.getElementById('cart-total');
            const modalCartTotal = document.getElementById('modal-cart-total');
            
            if (cartTotal) {
                cartTotal.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
            }
            if (modalCartTotal) {
                modalCartTotal.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
            }

            // Update checkout button
            const checkoutBtn = cartModal.querySelector('.primary-bg, .primary-bg[disabled]');
            if (checkoutBtn) {
                if (showMinOrderWarning) {
                    checkoutBtn.disabled = true;
                    checkoutBtn.textContent = `Mín. R$ ${minOrderValue.toFixed(2).replace('.', ',')}`;
                } else {
                    checkoutBtn.disabled = false;
                    checkoutBtn.textContent = 'Finalizar Pedido';
                }
            }
        }

        // Initialize
        updateUI();
        checkBusinessStatus();
        checkUserLogin();
    </script>
</body>
</html>
