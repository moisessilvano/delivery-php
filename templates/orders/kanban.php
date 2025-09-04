<?php
$title = 'Pedidos - ' . $establishment['name'];
$showNavbar = true;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center space-y-4 md:space-y-0">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pedidos</h1>
            <p class="text-gray-600">Gerencie os pedidos em tempo real</p>
        </div>
        <div class="flex flex-col md:flex-row md:items-center space-y-3 md:space-y-0 md:space-x-4">
            <div class="flex items-center space-x-3 text-sm">
                <div class="flex items-center space-x-2 text-gray-600">
                    <i class="fas fa-sync-alt text-green-500" id="refresh-indicator"></i>
                    <span class="hidden md:inline">Última atualização: <span id="last-update">agora</span></span>
                </div>
                <div class="flex items-center space-x-2 text-gray-600">
                    <i class="fas fa-clock text-blue-500"></i>
                    <span class="hidden md:inline">Próxima em: </span><span id="countdown">30</span>s
                </div>
                <button onclick="manualRefresh()" 
                        class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        id="refresh-btn">
                    <i class="fas fa-sync-alt mr-1" id="refresh-btn-icon"></i>
                    <span class="hidden md:inline">Atualizar</span>
                </button>
            </div>
            <div class="flex space-x-2">
                <a href="/orders/history" class="btn-secondary">
                    <i class="fas fa-history mr-2"></i>Histórico de Pedidos
                </a>
                <a href="/orders/create" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i><span class="hidden md:inline">Novo Pedido</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Status Filter Buttons -->
    <div id="mobile-filters" class="bg-white rounded-lg shadow-sm p-4 mb-4" style="display: none;">
        <div class="grid grid-cols-2 gap-2">
            <button onclick="filterByStatus('pending')" 
                    class="status-filter-btn flex items-center justify-center px-3 py-2 rounded-lg text-sm font-medium transition-colors active"
                    data-status="pending">
                <i class="fas fa-plus-circle mr-2"></i>
                <span>Novo</span>
                <span class="ml-2 bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-xs" id="mobile-count-pending">0</span>
            </button>
            <button onclick="filterByStatus('preparing')" 
                    class="status-filter-btn flex items-center justify-center px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    data-status="preparing">
                <i class="fas fa-clock mr-2"></i>
                <span>Em Preparação</span>
                <span class="ml-2 bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full text-xs" id="mobile-count-preparing">0</span>
            </button>
            <button onclick="filterByStatus('ready')" 
                    class="status-filter-btn flex items-center justify-center px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    data-status="ready">
                <i class="fas fa-check-circle mr-2"></i>
                <span>Pronto</span>
                <span class="ml-2 bg-green-100 text-green-800 px-2 py-0.5 rounded-full text-xs" id="mobile-count-ready">0</span>
            </button>
            <button onclick="filterByStatus('delivering')" 
                    class="status-filter-btn flex items-center justify-center px-3 py-2 rounded-lg text-sm font-medium transition-colors"
                    data-status="delivering">
                <i class="fas fa-truck mr-2"></i>
                <span>A Caminho</span>
                <span class="ml-2 bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full text-xs" id="mobile-count-delivering">0</span>
            </button>
        </div>
    </div>

    <!-- Mobile Orders Container -->
    <div id="mobile-orders-container" class="space-y-4" style="display: none;">
        <!-- Orders will be loaded here for mobile -->
    </div>

    <!-- Kanban Board (Desktop Only) -->
    <div id="kanban-board" class="kanban-grid">
        <!-- Novo -->
        <div class="kanban-column bg-white rounded-xl shadow-lg">
            <div class="kanban-column-header p-4 border-b border-gray-200 bg-blue-50">
                <h3 class="font-semibold text-blue-900 flex items-center">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Novo
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" id="count-pending">0</span>
                </h3>
            </div>
            <div class="p-4 space-y-4 min-h-96" id="column-pending" data-status="pending">
                <!-- Orders will be loaded here -->
            </div>
        </div>

        <!-- Em Preparação -->
        <div class="kanban-column bg-white rounded-xl shadow-lg">
            <div class="kanban-column-header p-4 border-b border-gray-200 bg-yellow-50">
                <h3 class="font-semibold text-yellow-900 flex items-center">
                    <i class="fas fa-clock mr-2"></i>
                    Em Preparação
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800" id="count-preparing">0</span>
                </h3>
            </div>
            <div class="p-4 space-y-4 min-h-96" id="column-preparing" data-status="preparing">
                <!-- Orders will be loaded here -->
            </div>
        </div>

        <!-- Pronto -->
        <div class="kanban-column bg-white rounded-xl shadow-lg">
            <div class="kanban-column-header p-4 border-b border-gray-200 bg-green-50">
                <h3 class="font-semibold text-green-900 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    Pronto
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" id="count-ready">0</span>
                </h3>
            </div>
            <div class="p-4 space-y-4 min-h-96" id="column-ready" data-status="ready">
                <!-- Orders will be loaded here -->
            </div>
        </div>

        <!-- A Caminho -->
        <div class="kanban-column bg-white rounded-xl shadow-lg">
            <div class="kanban-column-header p-4 border-b border-gray-200 bg-purple-50">
                <h3 class="font-semibold text-purple-900 flex items-center">
                    <i class="fas fa-truck mr-2"></i>
                    A Caminho
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800" id="count-delivering">0</span>
                </h3>
            </div>
            <div class="p-4 space-y-4 min-h-96" id="column-delivering" data-status="delivering">
                <!-- Orders will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div id="order-details-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-end md:items-center justify-center p-0 md:p-4">
    <div class="bg-white rounded-t-3xl md:rounded-xl w-full max-w-md md:max-w-2xl max-h-[90vh] overflow-hidden">
        <div class="sticky top-0 bg-white border-b p-4 flex justify-between items-center">
            <h2 id="modal-order-title" class="text-lg md:text-xl font-bold">Detalhes do Pedido</h2>
            <button onclick="closeOrderModal()" class="text-gray-500 hover:text-gray-700 p-1">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="modal-order-content" class="p-4 overflow-y-auto" style="max-height: calc(90vh - 140px);">
            <!-- Order details will be loaded here -->
        </div>
        
        <div class="sticky bottom-0 bg-gray-50 border-t p-4 flex flex-col md:flex-row justify-end space-y-2 md:space-y-0 md:space-x-3">
            <button onclick="closeOrderModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 order-2 md:order-1">
                Fechar
            </button>
            <a id="modal-full-details-btn" href="#" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 text-center order-1 md:order-2">
                <i class="fas fa-external-link-alt mr-2"></i>Ver Detalhes Completos
            </a>
        </div>
    </div>
</div>

<style>
.order-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.order-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.order-card.dragging {
    opacity: 0.5;
    transform: rotate(5deg);
}

.column.drag-over {
    background-color: #f3f4f6;
    border: 2px dashed #3b82f6;
    border-radius: 8px;
}

.kanban-column {
    min-height: 600px;
    background: #f8fafc;
    border-radius: 12px;
    width: 100%;
    min-width: 280px;
    max-width: 350px;
}

.kanban-column-header {
    border-radius: 12px 12px 0 0;
}

.kanban-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    gap: 24px;
    width: 100%;
    min-width: 1200px;
    overflow-x: auto;
}

/* Status filter buttons */
.status-filter-btn {
    background-color: #f9fafb;
    color: #6b7280;
    border: 1px solid #e5e7eb;
}

.status-filter-btn.active {
    background-color: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.status-filter-btn:hover:not(.active) {
    background-color: #f3f4f6;
    color: #374151;
}

/* Mobile optimizations */
@media (max-width: 768px) {
    .order-card {
        padding: 12px !important;
        margin-bottom: 12px;
    }
    
    .order-card .order-id {
        font-size: 14px;
    }
    
    .order-card .customer-info {
        font-size: 13px;
    }
    
    .order-card .order-total {
        font-size: 16px;
    }
    
    .kanban-column-header {
        padding: 12px 16px !important;
    }
    
    .kanban-column-header h3 {
        font-size: 14px !important;
    }
    
    .btn-details {
        padding: 8px 12px !important;
        font-size: 12px !important;
        width: 100%;
        justify-content: center;
    }
}

/* Improve touch targets for mobile */
@media (max-width: 768px) {
    .order-card {
        cursor: pointer;
        min-height: 44px;
    }
    
    .btn-details {
        min-height: 44px;
        background-color: #3B82F6;
        color: white;
        border-radius: 8px;
        font-weight: 500;
        border: none;
        margin-top: 8px;
    }
    
    .btn-details:hover {
        background-color: #2563EB;
        color: white;
    }
}

/* Utility classes */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Desktop modal improvements */
@media (min-width: 768px) {
    .btn-details {
        background-color: transparent;
        color: #3B82F6;
        border: 1px solid #E5E7EB;
    }
    
    .btn-details:hover {
        background-color: #F3F4F6;
        color: #2563EB;
    }
}
</style>

<script>
let orders = [];
let lastUpdate = new Date();
let countdownInterval = null;
let autoRefreshInterval = null;
let countdown = 30;
let notificationQueue = [];
let isPlayingNotification = false;

// Load orders from server
async function loadOrders() {
    try {
        const response = await fetch('/api/orders');
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        orders = await response.json();
        renderOrders();
        updateLastUpdate();
        updateRefreshIndicator(true);
        resetCountdown();
        
        // Check for new orders and play notifications
        await checkForNewOrders();
    } catch (error) {
        console.error('Erro ao carregar pedidos:', error);
        updateRefreshIndicator(false);
        
        // Remove existing error messages first
        const existingErrors = document.querySelectorAll('.bg-red-100.border.border-red-400');
        existingErrors.forEach(error => error.remove());
        
        // Show error message to user
        const errorDiv = document.createElement('div');
        errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
        errorDiv.innerHTML = `<strong>Erro:</strong> Não foi possível carregar os pedidos. ${error.message}`;
        
        const container = document.querySelector('.space-y-6');
        if (container) {
            container.insertBefore(errorDiv, container.firstChild);
        }
    }
}

// Manual refresh function
async function manualRefresh() {
    const btn = document.getElementById('refresh-btn');
    const icon = document.getElementById('refresh-btn-icon');
    
    // Show loading state
    if (btn) btn.disabled = true;
    if (icon) icon.className = 'fas fa-spinner fa-spin mr-1';
    
    await loadOrders();
    
    // Reset button state
    if (btn) btn.disabled = false;
    if (icon) icon.className = 'fas fa-sync-alt mr-1';
}

// Render orders in kanban columns
function renderOrders() {
    const columns = {
        pending: document.getElementById('column-pending'),
        preparing: document.getElementById('column-preparing'),
        ready: document.getElementById('column-ready'),
        delivering: document.getElementById('column-delivering')
    };

    // Clear columns
    Object.values(columns).forEach(column => {
        if (column) {
            column.innerHTML = '';
        }
    });

    // Count orders by status
    const counts = {
        pending: 0,
        preparing: 0,
        ready: 0,
        delivering: 0
    };

    // Render each order
    orders.forEach(order => {
        const column = columns[order.status];
        if (column) {
            column.appendChild(createOrderCard(order));
            counts[order.status]++;
        }
    });

    // Update counts
    const countElements = {
        pending: document.getElementById('count-pending'),
        preparing: document.getElementById('count-preparing'),
        ready: document.getElementById('count-ready'),
        delivering: document.getElementById('count-delivering')
    };

    Object.keys(counts).forEach(status => {
        if (countElements[status]) {
            countElements[status].textContent = counts[status];
        }
    });
    
    // Update mobile view
    updateMobileCounts();
    renderMobileOrders();
}

// Create order card element
function createOrderCard(order) {
    const card = document.createElement('div');
    card.className = 'order-card bg-white border border-gray-200 rounded-lg p-4 cursor-move shadow-sm hover:shadow-lg transition-shadow duration-200';
    card.draggable = true;
    card.dataset.orderId = order.public_id;
    
    const timeAgo = getTimeAgo(order.created_at);
    const statusColors = {
        pending: 'bg-blue-100 text-blue-800',
        preparing: 'bg-yellow-100 text-yellow-800',
        ready: 'bg-green-100 text-green-800',
        delivering: 'bg-purple-100 text-purple-800'
    };

    card.innerHTML = `
        <div class="flex justify-between items-start mb-2 md:mb-3">
            <span class="order-id text-base md:text-lg font-bold text-gray-900">#${order.public_id}</span>
            <span class="text-xs text-gray-500">${timeAgo}</span>
        </div>
        
        <div class="customer-info mb-2 md:mb-3">
            <h4 class="font-medium text-gray-900 text-sm md:text-base">${order.customer_name}</h4>
            <p class="text-xs md:text-sm text-gray-600">
                <i class="fas fa-phone mr-1"></i>${order.customer_phone}
            </p>
            ${order.delivery_type === 'pickup' ? 
                `<p class="text-xs md:text-sm text-gray-600"><i class="fas fa-store mr-1"></i>Retirada no Local</p>` : 
                (order.customer_address ? `<p class="text-xs md:text-sm text-gray-600 truncate"><i class="fas fa-map-marker-alt mr-1"></i>${order.customer_address}</p>` : '')
            }
        </div>
        
        <div class="mb-2 md:mb-3">
            <p class="text-xs md:text-sm text-gray-600">${order.items_count} item(ns)</p>
            <p class="order-total text-base md:text-lg font-bold text-green-600">R$ ${parseFloat(order.total_amount).toFixed(2).replace('.', ',')}</p>
        </div>
        
        ${order.payment_method ? `<div class="mb-2 md:mb-3"><span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"><i class="fas fa-credit-card mr-1"></i>${order.payment_method}</span></div>` : ''}
        
        ${order.notes ? `<div class="mb-2 md:mb-3"><p class="text-xs md:text-sm text-gray-600 italic line-clamp-2">"${order.notes}"</p></div>` : ''}
        
        <div class="flex flex-col space-y-2">
            <!-- Action Button -->
            <div class="flex justify-center">
                ${getStatusActionButton(order.status, order.public_id, order.delivery_type)}
            </div>
            <!-- View Details Button -->
            <div class="flex justify-center">
                <button onclick="openOrderModal('${order.public_id}')" class="btn-details w-full px-3 py-1 text-xs font-medium rounded-lg transition-colors flex items-center justify-center text-gray-600 hover:text-gray-800 hover:bg-gray-100">
                    <i class="fas fa-eye mr-1"></i>Ver detalhes
                </button>
            </div>
        </div>
    `;

    // Add drag event listeners
    card.addEventListener('dragstart', handleDragStart);
    card.addEventListener('dragend', handleDragEnd);

    return card;
}

// Get status action button
function getStatusActionButton(status, orderId, deliveryType) {
    const buttons = {
        pending: `<button onclick="updateOrderStatus('${orderId}', 'preparing')" class="w-full px-4 py-3 text-sm font-semibold text-white rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md" style="background-color: #d97706; border: none;" onmouseover="this.style.backgroundColor='#b45309'" onmouseout="this.style.backgroundColor='#d97706'">
                    <i class="fas fa-play mr-2"></i>Iniciar Preparação
                  </button>`,
        preparing: `<button onclick="updateOrderStatus('${orderId}', 'ready')" class="w-full px-4 py-3 text-sm font-semibold text-white rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md" style="background-color: #16a34a; border: none;" onmouseover="this.style.backgroundColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a'">
                      <i class="fas fa-check mr-2"></i>Marcar como Pronto
                    </button>`,
        ready: deliveryType === 'pickup' 
            ? `<button onclick="updateOrderStatus('${orderId}', 'completed')" class="w-full px-4 py-3 text-sm font-semibold text-white rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md" style="background-color: #16a34a; border: none;" onmouseover="this.style.backgroundColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a'">
                 <i class="fas fa-check-circle mr-2"></i>Pedido Retirado
               </button>`
            : `<button onclick="updateOrderStatus('${orderId}', 'delivering')" class="w-full px-4 py-3 text-sm font-semibold text-white rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md" style="background-color: #ea580c; border: none;" onmouseover="this.style.backgroundColor='#c2410c'" onmouseout="this.style.backgroundColor='#ea580c'">
                 <i class="fas fa-truck mr-2"></i>Enviar para Entrega
               </button>`,
        delivering: `<button onclick="updateOrderStatus('${orderId}', 'completed')" class="w-full px-4 py-3 text-sm font-semibold text-white rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md" style="background-color: #2563eb; border: none;" onmouseover="this.style.backgroundColor='#1d4ed8'" onmouseout="this.style.backgroundColor='#2563eb'">
                       <i class="fas fa-check-circle mr-2"></i>Confirmar Entrega
                     </button>`
    };
    return buttons[status] || '';
}

// Update order status
async function updateOrderStatus(orderId, newStatus) {
    const statusMessages = {
        preparing: 'Iniciar a preparação deste pedido?',
        ready: 'Marcar este pedido como pronto?',
        delivering: 'Enviar este pedido para entrega?',
        completed: 'Confirmar que este pedido foi entregue/retirado?'
    };
    
    const message = statusMessages[newStatus] || 'Deseja alterar o status deste pedido?';
    
    if (!confirm(`Pedido #${orderId}\n\n${message}`)) {
        return;
    }

    try {
        const response = await fetch(`/api/orders/${orderId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ status: newStatus })
        });

        if (response.ok) {
            // Refresh orders
            await loadOrders();
            showToast(`Status atualizado para: ${getStatusName(newStatus)}`, 'success');
        } else {
            throw new Error('Erro ao atualizar status');
        }
    } catch (error) {
        console.error('Error updating status:', error);
        showToast('Erro ao atualizar status do pedido', 'error');
    }
}

// Get status display name
function getStatusName(status) {
    const names = {
        pending: 'Novo',
        preparing: 'Em Preparação',
        ready: 'Pronto',
        delivering: 'A Caminho',
        completed: 'Concluído'
    };
    return names[status] || status;
}

// Mobile filter functionality
let currentMobileFilter = 'pending';

function filterByStatus(status) {
    currentMobileFilter = status;
    
    // Update active button
    document.querySelectorAll('.status-filter-btn').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.status === status) {
            btn.classList.add('active');
        }
    });
    
    renderMobileOrders();
}

// Render orders for mobile view
function renderMobileOrders() {
    const container = document.getElementById('mobile-orders-container');
    if (!container) return;
    
    container.innerHTML = '';
    
    const filteredOrders = orders.filter(order => order.status === currentMobileFilter);
    
    if (filteredOrders.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-3xl mb-3"></i>
                <p>Nenhum pedido encontrado com status: ${getStatusName(currentMobileFilter)}</p>
            </div>
        `;
        return;
    }
    
    filteredOrders.forEach(order => {
        const card = createOrderCard(order);
        card.className = 'order-card bg-white border border-gray-200 rounded-lg p-4 shadow-sm';
        card.draggable = false; // Disable drag on mobile
        // Remove cursor-move on mobile
        card.style.cursor = 'default';
        container.appendChild(card);
    });
}

// Update mobile counters
function updateMobileCounts() {
    const counts = {
        pending: 0,
        preparing: 0,
        ready: 0,
        delivering: 0
    };

    orders.forEach(order => {
        if (counts.hasOwnProperty(order.status)) {
            counts[order.status]++;
        }
    });

    // Safely update mobile counters with null checks
    const mobileCountElements = {
        pending: document.getElementById('mobile-count-pending'),
        preparing: document.getElementById('mobile-count-preparing'),
        ready: document.getElementById('mobile-count-ready'),
        delivering: document.getElementById('mobile-count-delivering')
    };

    Object.keys(counts).forEach(status => {
        if (mobileCountElements[status]) {
            mobileCountElements[status].textContent = counts[status];
        }
    });
}

// Toast notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg text-white text-sm font-medium shadow-lg transform transition-all duration-300 translate-x-full`;
    
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    toast.classList.add(bgColor);
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Check for new orders and add to notification queue
async function checkForNewOrders() {
    try {
        const response = await fetch('/api/orders/new');
        const data = await response.json();
        
        if (data.new_orders && data.new_orders.length > 0) {
            // Add each new order to notification queue
            data.new_orders.forEach(order => {
                notificationQueue.push({
                    id: order.public_id,
                    customer: order.customer_name,
                    amount: order.total_amount
                });
            });
            
            // Start playing notifications if not already playing
            if (!isPlayingNotification) {
                playNextNotification();
            }
            
            // Mark orders as notified
            const orderIds = data.new_orders.map(order => order.public_id);
            await markOrdersAsNotified(orderIds);
        }
    } catch (error) {
        console.error('Erro ao verificar novos pedidos:', error);
    }
}

// Play next notification in queue
async function playNextNotification() {
    if (notificationQueue.length === 0) {
        isPlayingNotification = false;
        return;
    }
    
    isPlayingNotification = true;
    const order = notificationQueue.shift();
    
    // Play notification sound
    const audio = new Audio('/audio/notification.wav');
    audio.volume = 0.8;
    
    try {
        await audio.play();
        
        // Show visual notification
        showToast(`Novo pedido: ${order.customer} - R$ ${parseFloat(order.amount).toFixed(2).replace('.', ',')}`, 'info');
        
        // Wait for audio to finish + small delay before next
        audio.addEventListener('ended', () => {
            setTimeout(() => {
                playNextNotification(); // Play next in queue
            }, 500); // 500ms delay between notifications
        });
        
    } catch (error) {
        console.error('Erro ao reproduzir notificação:', error);
        // Continue with next notification even if this one failed
        setTimeout(() => {
            playNextNotification();
        }, 1000);
    }
}

// Mark orders as notified
async function markOrdersAsNotified(orderIds) {
    try {
        await fetch('/api/orders/mark-notified', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ order_ids: orderIds })
        });
    } catch (error) {
        console.error('Erro ao marcar pedidos como notificados:', error);
    }
}

// Drag and drop functionality
let draggedElement = null;

function handleDragStart(e) {
    draggedElement = e.target;
    e.target.classList.add('dragging');
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
    draggedElement = null;
}

// Initialize drag and drop for columns
function initializeDragDrop() {
    const columns = document.querySelectorAll('[data-status]');
    
    columns.forEach(column => {
        column.addEventListener('dragover', handleDragOver);
        column.addEventListener('drop', handleDrop);
        column.addEventListener('dragenter', handleDragEnter);
        column.addEventListener('dragleave', handleDragLeave);
    });
}

function handleDragOver(e) {
    e.preventDefault();
}

function handleDragEnter(e) {
    e.preventDefault();
    e.target.closest('[data-status]').classList.add('drag-over');
}

function handleDragLeave(e) {
    if (!e.target.closest('[data-status]').contains(e.relatedTarget)) {
        e.target.closest('[data-status]').classList.remove('drag-over');
    }
}

async function handleDrop(e) {
    e.preventDefault();
    const column = e.target.closest('[data-status]');
    column.classList.remove('drag-over');
    
    if (draggedElement) {
        const orderId = draggedElement.dataset.orderId;
        const newStatus = column.dataset.status;
        
        // Update order status on server
        try {
            const response = await fetch(`/api/orders/${orderId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ status: newStatus })
            });
            
            if (response.ok) {
                // Update local order data
                const order = orders.find(o => o.public_id === orderId);
                if (order) {
                    order.status = newStatus;
                }
                
                // Re-render orders immediately
                renderOrders();
                
                // Show notification
                showNotification(`Pedido #${orderId.substr(-8)} movido para ${getStatusName(newStatus)}`, 'success');
            } else {
                throw new Error('Erro ao atualizar status');
            }
        } catch (error) {
            console.error('Erro ao atualizar status:', error);
            showNotification('Erro ao atualizar status do pedido', 'error');
        }
    }
}

// Helper functions
function getTimeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const diffInMinutes = Math.floor((now - date) / (1000 * 60));
    
    if (diffInMinutes < 1) return 'agora';
    if (diffInMinutes < 60) return `${diffInMinutes}min atrás`;
    
    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `${diffInHours}h atrás`;
    
    const diffInDays = Math.floor(diffInHours / 24);
    return `${diffInDays}d atrás`;
}

function getStatusName(status) {
    const names = {
        pending: 'Novo',
        preparing: 'Em Preparação',
        ready: 'Pronto',
        delivering: 'A Caminho'
    };
    return names[status] || status;
}

function updateLastUpdate() {
    lastUpdate = new Date();
    const lastUpdateElement = document.getElementById('last-update');
    if (lastUpdateElement) {
        lastUpdateElement.textContent = lastUpdate.toLocaleTimeString();
    }
}

function updateRefreshIndicator(success) {
    const indicator = document.getElementById('refresh-indicator');
    if (indicator) {
        indicator.className = success ? 'fas fa-sync-alt text-green-500' : 'fas fa-exclamation-triangle text-red-500';
    }
}

// Countdown timer
function startCountdown() {
    countdownInterval = setInterval(() => {
        countdown--;
        const countdownElement = document.getElementById('countdown');
        if (countdownElement) {
            countdownElement.textContent = countdown;
        }
        
        if (countdown <= 0) {
            clearInterval(countdownInterval);
        }
    }, 1000);
}

function resetCountdown() {
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
    countdown = 30;
    const countdownElement = document.getElementById('countdown');
    if (countdownElement) {
        countdownElement.textContent = countdown;
    }
    startCountdown();
}

// Auto-refresh orders every 30 seconds
function startAutoRefresh() {
    autoRefreshInterval = setInterval(() => {
        loadOrders();
    }, 30000);
}

// Show notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Modal functions
function openOrderModal(orderId) {
    document.getElementById('modal-order-title').textContent = `Pedido #${orderId}`;
    document.getElementById('modal-full-details-btn').href = `/orders/${orderId}`;
    
    loadOrderDetails(orderId);
    
    document.getElementById('order-details-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeOrderModal() {
    document.getElementById('order-details-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

async function loadOrderDetails(orderId) {
    const content = document.getElementById('modal-order-content');
    content.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i><p class="text-gray-500 mt-2">Carregando...</p></div>';
    
    try {
        const response = await fetch(`/api/orders/${orderId}/details`);
        if (!response.ok) throw new Error('Erro ao carregar detalhes');
        
        const orderDetails = await response.json();
        renderOrderDetails(orderDetails);
        
    } catch (error) {
        content.innerHTML = '<div class="text-center py-8 text-red-500"><i class="fas fa-exclamation-circle text-2xl"></i><p class="mt-2">Erro ao carregar detalhes do pedido</p></div>';
    }
}

function renderOrderDetails(order) {
    const content = document.getElementById('modal-order-content');
    const timeAgo = getTimeAgo(order.created_at);
    
    content.innerHTML = `
        <div class="space-y-4 md:space-y-6">
            <!-- Customer Info -->
            <div class="bg-gray-50 rounded-lg p-3 md:p-4">
                <h3 class="font-semibold text-gray-900 mb-3">
                    ${order.delivery_type === 'pickup' ? 'Informações de Retirada' : 'Informações de Entrega'}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Cliente:</p>
                        <p class="font-medium">${order.customer_name}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Telefone:</p>
                        <p class="font-medium">${order.customer_phone}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600">
                            ${order.delivery_type === 'pickup' ? 'Tipo de Pedido:' : 'Endereço:'}
                        </p>
                        <p class="font-medium">
                            ${order.delivery_type === 'pickup' ? 
                                '<i class="fas fa-store mr-2"></i>Retirada no Local' : 
                                order.customer_address
                            }
                        </p>
                    </div>
                </div>
                <div class="mt-3 md:mt-4 flex items-center text-sm text-gray-500">
                    <i class="fas fa-clock mr-2"></i>
                    <span>Pedido feito ${timeAgo}</span>
                </div>
            </div>
            
            <!-- Order Items -->
            <div>
                <h3 class="font-semibold text-gray-900 mb-3">Itens do Pedido</h3>
                <div class="space-y-2 md:space-y-3">
                    ${order.items.map(item => `
                        <div class="flex justify-between items-start p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h4 class="font-medium text-sm md:text-base">${item.product_name}</h4>
                                ${item.options && item.options.length > 0 ? `
                                    <div class="text-xs md:text-sm text-gray-600 mt-1">
                                        <strong>Customizações:</strong>
                                        <ul class="ml-4 mt-1">
                                            ${item.options.map(opt => 
                                                `<li class="text-xs">• ${opt.name}${opt.price > 0 ? ` (+R$ ${opt.price.toFixed(2).replace('.', ',')})` : ''}</li>`
                                            ).join('')}
                                        </ul>
                                    </div>
                                ` : ''}
                                <p class="text-xs md:text-sm text-gray-600 mt-1">
                                    ${item.quantity}x R$ ${parseFloat(item.product_price).toFixed(2).replace('.', ',')}
                                </p>
                            </div>
                            <div class="text-right ml-2">
                                <p class="font-bold text-sm md:text-base">R$ ${(parseFloat(item.product_price) * parseInt(item.quantity)).toFixed(2).replace('.', ',')}</p>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="bg-gray-50 rounded-lg p-3 md:p-4">
                <div class="flex justify-between items-center text-lg md:text-xl font-bold">
                    <span>Total do Pedido:</span>
                    <span class="text-green-600">R$ ${parseFloat(order.total_amount).toFixed(2).replace('.', ',')}</span>
                </div>
                ${order.payment_method ? `
                    <div class="flex justify-between items-center text-sm text-gray-600 mt-2">
                        <span>Forma de Pagamento:</span>
                        <span class="font-medium">${order.payment_method}</span>
                    </div>
                ` : ''}
                ${order.notes ? `
                    <div class="mt-3 md:mt-4">
                        <p class="text-sm text-gray-600 font-semibold mb-2">Observações:</p>
                        <p class="text-sm text-gray-700 italic bg-white p-2 rounded border-l-4 border-blue-500">"${order.notes}"</p>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
}

// Control responsive layout
function updateLayout() {
    const screenWidth = window.innerWidth;
    const kanbanBoard = document.getElementById('kanban-board');
    const mobileContainer = document.getElementById('mobile-orders-container');
    const mobileFilters = document.getElementById('mobile-filters');
    
    if (screenWidth >= 1024) {
        // Desktop: Show kanban, hide mobile
        if (kanbanBoard) kanbanBoard.style.display = 'grid';
        if (mobileContainer) mobileContainer.style.display = 'none';
        if (mobileFilters) mobileFilters.style.display = 'none';
    } else {
        // Mobile: Show mobile, hide kanban
        if (kanbanBoard) kanbanBoard.style.display = 'none';
        if (mobileContainer) mobileContainer.style.display = 'block';
        if (mobileFilters) mobileFilters.style.display = 'block';
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Set initial layout
    updateLayout();
    
    // Update layout on resize
    window.addEventListener('resize', updateLayout);
    
    loadOrders();
    initializeDragDrop();
    startAutoRefresh();
    startCountdown();
});
</script>
