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
                max-width: 800px;
                margin: 0 auto;
                padding: 0 2rem;
            }
            
            .mobile-nav {
                display: none;
            }
        }
        
        .order-card {
            transition: all 0.2s ease;
        }
        
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-weight: 500;
        }
        
        .status-pending { background-color: #dbeafe; color: #1e40af; }
        .status-preparing { background-color: #fef3c7; color: #92400e; }
        .status-ready { background-color: #d1fae5; color: #065f46; }
        .status-delivering { background-color: #e0e7ff; color: #3730a3; }
        .status-delivered { background-color: #d1fae5; color: #065f46; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }
        
        .pagination-btn {
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            background: white;
            color: #374151;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .pagination-btn:hover {
            background-color: #f3f4f6;
        }
        
        .pagination-btn.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
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
                            <h1 class="text-lg font-bold">Meus Pedidos</h1>
                            <p class="text-sm text-gray-500">Histórico e status dos seus pedidos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="container p-4">
        <!-- Filter Tabs -->
        <div class="mb-6">
            <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
                <button class="filter-tab active flex-1 py-2 px-3 text-sm font-medium rounded-md transition-colors" data-status="all">
                    Todos
                </button>
                <button class="filter-tab flex-1 py-2 px-3 text-sm font-medium rounded-md transition-colors" data-status="active">
                    Em Andamento
                </button>
                <button class="filter-tab flex-1 py-2 px-3 text-sm font-medium rounded-md transition-colors" data-status="delivered">
                    Entregues
                </button>
            </div>
        </div>

        <!-- Orders List -->
        <div id="orders-container">
            <?php if (empty($orders)): ?>
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-receipt text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum pedido encontrado</h3>
                <p class="text-gray-600 mb-6">Você ainda não fez nenhum pedido. Que tal começar agora?</p>
                <a href="/" class="primary-bg text-white px-6 py-3 rounded-lg font-medium hover:opacity-90 transition-opacity">
                    <i class="fas fa-utensils mr-2"></i>
                    Ver Cardápio
                </a>
            </div>
            <?php else: ?>
            <!-- Orders -->
            <div class="space-y-4" id="orders-list">
                <?php foreach ($orders as $order): ?>
                <div class="order-card bg-white rounded-xl p-4 border border-gray-100 shadow-sm" 
                     data-status="<?= $order['status'] ?>"
                     data-order-id="<?= $order['public_id'] ?>">
                    
                    <!-- Order Header -->
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-bold text-gray-900">Pedido #<?= substr($order['public_id'], -8) ?></h3>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-calendar mr-1"></i>
                                <?= date('d/m/Y \à\s H:i', strtotime($order['created_at'])) ?>
                            </p>
                        </div>
                        <span class="status-badge status-<?= $order['status'] ?>">
                            <?php
                            $statusNames = [
                                'pending' => 'Pendente',
                                'preparing' => 'Preparando',
                                'ready' => 'Pronto',
                                'delivering' => 'A Caminho',
                                'delivered' => 'Entregue',
                                'cancelled' => 'Cancelado'
                            ];
                            echo $statusNames[$order['status']] ?? ucfirst($order['status']);
                            ?>
                        </span>
                    </div>

                    <!-- Order Info -->
                    <div class="mb-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600"><?= $order['items_count'] ?> item(ns)</span>
                            <span class="font-bold text-lg primary-text">R$ <?= number_format($order['total_amount'], 2, ',', '.') ?></span>
                        </div>
                        
                        <?php if ($order['payment_method_name']): ?>
                        <div class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-credit-card mr-1"></i>
                            <?= htmlspecialchars($order['payment_method_name']) ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Order Items Preview -->
                    <?php if (!empty($order['items'])): ?>
                    <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                        <div class="text-sm space-y-1">
                            <?php 
                            $displayItems = array_slice($order['items'], 0, 2);
                            foreach ($displayItems as $item): 
                            ?>
                            <div class="flex justify-between">
                                <span><?= $item['quantity'] ?>x <?= htmlspecialchars($item['product_name']) ?></span>
                                <span>R$ <?= number_format($item['quantity'] * $item['product_price'], 2, ',', '.') ?></span>
                            </div>
                            <?php endforeach; ?>
                            
                            <?php if (count($order['items']) > 2): ?>
                            <div class="text-gray-500 text-xs mt-1">
                                + <?= count($order['items']) - 2 ?> item(ns) adicional(is)
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Actions -->
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-2">
                            <?php if (in_array($order['status'], ['pending', 'preparing', 'ready', 'delivering'])): ?>
                            <button class="track-order text-blue-600 text-sm font-medium" data-order-id="<?= $order['public_id'] ?>">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                Acompanhar
                            </button>
                            <?php endif; ?>
                            
                            <?php if ($order['status'] === 'delivered'): ?>
                            <button class="reorder-btn text-green-600 text-sm font-medium" data-order-id="<?= $order['public_id'] ?>">
                                <i class="fas fa-redo mr-1"></i>
                                Pedir Novamente
                            </button>
                            <?php endif; ?>
                        </div>
                        
                        <button class="view-details text-gray-600 text-sm" data-order-id="<?= $order['public_id'] ?>">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="flex justify-center mt-8">
                <div class="flex items-center space-x-1">
                    <?php if ($current_page > 1): ?>
                    <a href="?page=<?= $current_page - 1 ?>" class="pagination-btn rounded-l-lg">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++): ?>
                    <a href="?page=<?= $i ?>" class="pagination-btn <?= $i === $current_page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?= $current_page + 1 ?>" class="pagination-btn rounded-r-lg">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Load More Button (for infinite scroll alternative) -->
        <div id="load-more-container" class="hidden text-center mt-6">
            <button id="load-more-btn" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Carregar Mais Pedidos
            </button>
        </div>
    </main>

    <!-- Mobile Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t mobile-nav">
        <div class="flex items-center justify-around py-2">
            <button class="nav-item flex flex-col items-center py-2 px-4" data-page="menu" onclick="window.location.href='/'">
                <i class="fas fa-utensils text-xl mb-1"></i>
                <span class="text-xs">Cardápio</span>
            </button>
            <button class="nav-item active flex flex-col items-center py-2 px-4" data-page="orders">
                <i class="fas fa-receipt text-xl mb-1 primary-text"></i>
                <span class="text-xs primary-text">Pedidos</span>
            </button>
            <button class="nav-item flex flex-col items-center py-2 px-4" data-page="profile" onclick="window.location.href='/profile'">
                <i class="fas fa-user text-xl mb-1"></i>
                <span class="text-xs">Perfil</span>
            </button>
        </div>
    </nav>



    <!-- Order Details Modal -->
    <div id="order-details-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end justify-center md:items-center">
        <div class="bg-white rounded-t-3xl md:rounded-xl w-full max-w-md max-h-96 overflow-hidden md:max-h-none">
            <div class="p-4 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold">Detalhes do Pedido</h3>
                    <button id="close-details" class="text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div id="order-details-content" class="p-4 overflow-y-auto">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script src="/js/app.js"></script>
    <script>
        // Filter functionality
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Update active tab
                document.querySelectorAll('.filter-tab').forEach(t => {
                    t.classList.remove('active', 'bg-white', 'text-gray-900');
                    t.classList.add('text-gray-600');
                });
                
                this.classList.add('active', 'bg-white', 'text-gray-900');
                this.classList.remove('text-gray-600');
                
                // Filter orders
                const status = this.dataset.status;
                filterOrders(status);
            });
        });
        
        function filterOrders(status) {
            const orders = document.querySelectorAll('.order-card');
            
            orders.forEach(order => {
                const orderStatus = order.dataset.status;
                
                if (status === 'all') {
                    order.style.display = 'block';
                } else if (status === 'active') {
                    if (['pending', 'preparing', 'ready', 'delivering'].includes(orderStatus)) {
                        order.style.display = 'block';
                    } else {
                        order.style.display = 'none';
                    }
                } else if (status === 'delivered') {
                    if (['delivered', 'cancelled'].includes(orderStatus)) {
                        order.style.display = 'block';
                    } else {
                        order.style.display = 'none';
                    }
                }
            });
        }
        
        // Track order functionality
        document.querySelectorAll('.track-order').forEach(btn => {
            btn.addEventListener('click', function() {
                const orderId = this.dataset.orderId;
                window.location.href = `/order-success/${orderId}`;
            });
        });
        
        // Reorder functionality
        document.querySelectorAll('.reorder-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const orderId = this.dataset.orderId;
                
                try {
                    const response = await fetch(`/api/reorder/${orderId}`, {
                        method: 'POST'
                    });
                    
                    if (response.ok) {
                        showNotification('Itens adicionados ao carrinho!', 'success');
                        setTimeout(() => {
                            window.location.href = '/';
                        }, 1500);
                    } else {
                        throw new Error('Erro ao refazer pedido');
                    }
                } catch (error) {
                    showNotification('Erro ao refazer pedido. Tente novamente.', 'error');
                }
            });
        });
        
        // View details functionality
        document.querySelectorAll('.view-details').forEach(btn => {
            btn.addEventListener('click', async function() {
                const orderId = this.dataset.orderId;
                await loadOrderDetails(orderId);
            });
        });
        
        async function loadOrderDetails(orderId) {
            try {
                const response = await fetch(`/api/order-details/${orderId}`);
                const order = await response.json();
                
                if (response.ok) {
                    displayOrderDetails(order);
                    document.getElementById('order-details-modal').classList.remove('hidden');
                } else {
                    throw new Error('Erro ao carregar detalhes');
                }
            } catch (error) {
                showNotification('Erro ao carregar detalhes do pedido', 'error');
            }
        }
        
        function displayOrderDetails(order) {
            const content = document.getElementById('order-details-content');
            content.innerHTML = `
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold mb-2">Informações do Pedido</h4>
                        <div class="text-sm space-y-1">
                            <div>Pedido: #${order.public_id.substr(-8)}</div>
                            <div>Data: ${new Date(order.created_at).toLocaleString('pt-BR')}</div>
                            <div>Status: ${order.status}</div>
                            <div>Total: R$ ${parseFloat(order.total_amount).toFixed(2).replace('.', ',')}</div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold mb-2">Itens do Pedido</h4>
                        <div class="space-y-2">
                            ${order.items.map(item => `
                                <div class="flex justify-between text-sm">
                                    <span>${item.quantity}x ${item.product_name}</span>
                                    <span>R$ ${(item.quantity * item.product_price).toFixed(2).replace('.', ',')}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    
                    ${order.notes ? `
                    <div>
                        <h4 class="font-semibold mb-2">Observações</h4>
                        <p class="text-sm text-gray-600">${order.notes}</p>
                    </div>
                    ` : ''}
                </div>
            `;
        }
        
        // Close modal
        document.getElementById('close-details').addEventListener('click', () => {
            document.getElementById('order-details-modal').classList.add('hidden');
        });
        
        // Auto-refresh orders every 30 seconds
        setInterval(async () => {
            try {
                const response = await fetch('/api/orders-list');
                const data = await response.json();
                
                if (response.ok && data.orders) {
                    updateOrderStatuses(data.orders);
                }
            } catch (error) {
                console.log('Failed to refresh orders:', error);
            }
        }, 30000);
        
        function updateOrderStatuses(orders) {
            orders.forEach(order => {
                const orderCard = document.querySelector(`[data-order-id="${order.public_id}"]`);
                if (orderCard) {
                    const statusBadge = orderCard.querySelector('.status-badge');
                    if (statusBadge) {
                        statusBadge.className = `status-badge status-${order.status}`;
                        statusBadge.textContent = getStatusName(order.status);
                    }
                    orderCard.dataset.status = order.status;
                }
            });
        }
        
        function getStatusName(status) {
            const names = {
                'pending': 'Pendente',
                'preparing': 'Preparando',
                'ready': 'Pronto',
                'delivering': 'A Caminho',
                'delivered': 'Entregue',
                'cancelled': 'Cancelado'
            };
            return names[status] || status;
        }
        


        // Initialize filter
        document.querySelector('.filter-tab.active').click();
    </script>
</body>
</html>
