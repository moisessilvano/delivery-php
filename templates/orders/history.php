<?php
$title = 'Histórico de Pedidos - ' . $establishment['name'];
$showNavbar = true;
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Histórico de Pedidos</h1>
            <p class="text-gray-600">Consulte pedidos anteriores com filtros avançados</p>
        </div>
        <a href="/orders/kanban" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Voltar ao Kanban
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Filtros</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="phone-filter" class="form-label">Telefone do Cliente</label>
                <input type="text" id="phone-filter" class="form-input" placeholder="(11) 99999-9999">
            </div>
            <div>
                <label for="date-from" class="form-label">Data Início</label>
                <input type="date" id="date-from" class="form-input">
            </div>
            <div>
                <label for="date-to" class="form-label">Data Fim</label>
                <input type="date" id="date-to" class="form-input">
            </div>
            <div class="flex items-end">
                <button onclick="applyFilters()" class="btn-primary w-full">
                    <i class="fas fa-search mr-2"></i>Filtrar
                </button>
            </div>
        </div>
        <div class="mt-4">
            <button onclick="clearFilters()" class="btn-secondary">
                <i class="fas fa-times mr-2"></i>Limpar Filtros
            </button>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Resultados</h3>
            <p class="text-sm text-gray-600" id="results-count">Carregando...</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pedido</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody id="orders-table-body" class="bg-white divide-y divide-gray-200">
                    <!-- Orders will be loaded here -->
                </tbody>
            </table>
        </div>
        
        <!-- Loading indicator -->
        <div id="loading-indicator" class="p-8 text-center">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
            <p class="text-gray-500 mt-2">Carregando pedidos...</p>
        </div>
        
        <!-- No results -->
        <div id="no-results" class="p-8 text-center hidden">
            <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Nenhum pedido encontrado com os filtros aplicados.</p>
        </div>
    </div>
</div>

<!-- Order Details Modal -->
<div id="order-details-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b p-4 flex justify-between items-center">
            <h2 id="modal-order-title" class="text-xl font-bold">Detalhes do Pedido</h2>
            <button onclick="closeOrderModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="modal-order-content" class="p-4">
            <!-- Order details will be loaded here -->
        </div>
        
        <div class="sticky bottom-0 bg-gray-50 border-t p-4 flex justify-end space-x-3">
            <button onclick="closeOrderModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                Fechar
            </button>
            <a id="modal-full-details-btn" href="#" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                <i class="fas fa-external-link-alt mr-2"></i>Ver Detalhes Completos
            </a>
        </div>
    </div>
</div>

<script>
let currentFilters = {};

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Set default date range (last 30 days)
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    document.getElementById('date-from').value = thirtyDaysAgo.toISOString().split('T')[0];
    document.getElementById('date-to').value = today.toISOString().split('T')[0];
    
    loadOrders();
});

function applyFilters() {
    const phone = document.getElementById('phone-filter').value;
    const dateFrom = document.getElementById('date-from').value;
    const dateTo = document.getElementById('date-to').value;
    
    currentFilters = {
        phone: phone,
        date_from: dateFrom,
        date_to: dateTo
    };
    
    loadOrders();
}

function clearFilters() {
    document.getElementById('phone-filter').value = '';
    document.getElementById('date-from').value = '';
    document.getElementById('date-to').value = '';
    currentFilters = {};
    loadOrders();
}

async function loadOrders() {
    const loadingIndicator = document.getElementById('loading-indicator');
    const noResults = document.getElementById('no-results');
    const tableBody = document.getElementById('orders-table-body');
    const resultsCount = document.getElementById('results-count');
    
    // Show loading
    loadingIndicator.classList.remove('hidden');
    noResults.classList.add('hidden');
    tableBody.innerHTML = '';
    
    try {
        const params = new URLSearchParams(currentFilters);
        const response = await fetch(`/api/orders/history?${params}`);
        const data = await response.json();
        
        loadingIndicator.classList.add('hidden');
        
        if (data.orders && data.orders.length > 0) {
            renderOrders(data.orders);
            resultsCount.textContent = `${data.orders.length} pedido(s) encontrado(s)`;
        } else {
            noResults.classList.remove('hidden');
            resultsCount.textContent = 'Nenhum pedido encontrado';
        }
        
    } catch (error) {
        loadingIndicator.classList.add('hidden');
        console.error('Error loading orders:', error);
        alert('Erro ao carregar pedidos. Tente novamente.');
    }
}

function renderOrders(orders) {
    const tableBody = document.getElementById('orders-table-body');
    
    orders.forEach(order => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        
        const statusColors = {
            pending: 'bg-blue-100 text-blue-800',
            preparing: 'bg-yellow-100 text-yellow-800',
            ready: 'bg-green-100 text-green-800',
            delivering: 'bg-purple-100 text-purple-800',
            completed: 'bg-gray-100 text-gray-800',
            cancelled: 'bg-red-100 text-red-800'
        };
        
        const statusTexts = {
            pending: 'Pendente',
            preparing: 'Preparando',
            ready: 'Pronto',
            delivering: 'Entregando',
            completed: 'Concluído',
            cancelled: 'Cancelado'
        };
        
        const formatDate = (dateString) => {
            const date = new Date(dateString);
            return date.toLocaleString('pt-BR');
        };
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">#${order.public_id}</div>
                <div class="text-sm text-gray-500">${order.items_count} item(ns)</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${order.customer_name}</div>
                <div class="text-sm text-gray-500">${order.customer_phone}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${formatDate(order.created_at)}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${order.delivery_type === 'pickup' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'}">
                    <i class="fas ${order.delivery_type === 'pickup' ? 'fa-store' : 'fa-truck'} mr-1"></i>
                    ${order.delivery_type === 'pickup' ? 'Retirada' : 'Entrega'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[order.status] || 'bg-gray-100 text-gray-800'}">
                    ${statusTexts[order.status] || order.status}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                R$ ${parseFloat(order.total_amount).toFixed(2).replace('.', ',')}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="openOrderModal('${order.public_id}')" class="text-primary-600 hover:text-primary-700 mr-3">
                    <i class="fas fa-eye mr-1"></i>Detalhes
                </button>
                <a href="/orders/${order.public_id}" class="text-gray-600 hover:text-gray-700">
                    <i class="fas fa-external-link-alt mr-1"></i>Abrir
                </a>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
}

// Modal functions (reusing from kanban)
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
        <div class="space-y-6">
            <!-- Customer Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-3">
                    ${order.delivery_type === 'pickup' ? 'Informações de Retirada' : 'Informações de Entrega'}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                <div class="mt-4 flex items-center text-sm text-gray-500">
                    <i class="fas fa-clock mr-2"></i>
                    <span>Pedido feito ${timeAgo}</span>
                </div>
            </div>
            
            <!-- Order Items -->
            <div>
                <h3 class="font-semibold text-gray-900 mb-3">Itens do Pedido</h3>
                <div class="space-y-3">
                    ${order.items.map(item => `
                        <div class="flex justify-between items-start p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h4 class="font-medium">${item.product_name}</h4>
                                ${item.options && item.options.length > 0 ? `
                                    <div class="text-sm text-gray-600 mt-1">
                                        <strong>Customizações:</strong>
                                        <ul class="ml-4 mt-1">
                                            ${item.options.map(opt => 
                                                `<li>• ${opt.name}${opt.price > 0 ? ` (+R$ ${opt.price.toFixed(2).replace('.', ',')})` : ''}</li>`
                                            ).join('')}
                                        </ul>
                                    </div>
                                ` : ''}
                                <p class="text-sm text-gray-600">
                                    ${item.quantity}x R$ ${parseFloat(item.product_price).toFixed(2).replace('.', ',')}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold">R$ ${(parseFloat(item.product_price) * parseInt(item.quantity)).toFixed(2).replace('.', ',')}</p>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
            
            <!-- Payment & Total -->
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-center mb-2">
                    <span>Subtotal:</span>
                    <span>R$ ${(parseFloat(order.total_amount) - (order.delivery_type === 'delivery' ? parseFloat(order.delivery_fee || 0) : 0)).toFixed(2).replace('.', ',')}</span>
                </div>
                ${order.delivery_type === 'delivery' ? `
                    <div class="flex justify-between items-center mb-2">
                        <span>Taxa de entrega:</span>
                        <span>R$ ${parseFloat(order.delivery_fee || 0).toFixed(2).replace('.', ',')}</span>
                    </div>
                ` : ''}
                <hr class="my-2">
                <div class="flex justify-between items-center font-bold text-lg">
                    <span>Total:</span>
                    <span class="text-green-600">R$ ${parseFloat(order.total_amount).toFixed(2).replace('.', ',')}</span>
                </div>
                <div class="mt-3 text-sm text-gray-600">
                    <i class="fas fa-credit-card mr-2"></i>
                    ${order.payment_method} (${order.delivery_type === 'pickup' ? 'na retirada' : 'na entrega'})
                </div>
            </div>
            
            ${order.notes ? `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Observações:</h3>
                    <p class="text-gray-700">${order.notes}</p>
                </div>
            ` : ''}
        </div>
    `;
}

function getTimeAgo(dateString) {
    const now = new Date();
    const past = new Date(dateString);
    const diffInMinutes = Math.floor((now - past) / (1000 * 60));
    
    if (diffInMinutes < 1) return 'agora';
    if (diffInMinutes < 60) return `${diffInMinutes} min atrás`;
    
    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) return `${diffInHours}h atrás`;
    
    const diffInDays = Math.floor(diffInHours / 24);
    return `${diffInDays} dia(s) atrás`;
}
</script>
