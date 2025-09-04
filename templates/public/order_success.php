<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Pedido Confirmado - <?= htmlspecialchars($establishment['name']) ?></title>
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
        
        .success-animation {
            animation: scaleUp 0.5s ease-out;
        }
        
        @keyframes scaleUp {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.1); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }
        
        @media (min-width: 768px) {
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 0 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container min-h-screen flex flex-col justify-center py-8">
        <!-- Success Icon -->
        <div class="text-center mb-8">
            <div class="success-animation inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                <i class="fas fa-check text-3xl text-green-600"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Pedido Confirmado!</h1>
            <p class="text-gray-600">Seu pedido foi recebido com sucesso</p>
        </div>

        <!-- Order Info -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <div class="border-b pb-4 mb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Pedido #<?= $order['public_id'] ?></h2>
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-calendar mr-1"></i>
                            <?= date('d/m/Y \à\s H:i', strtotime($order['created_at'])) ?>
                        </p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-clock mr-1"></i>
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="mb-6">
                <h3 class="font-semibold text-gray-900 mb-3">
                    <?= ($order['delivery_type'] ?? 'delivery') === 'pickup' ? 'Informações de Retirada' : 'Informações de Entrega' ?>
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-user mr-2 text-gray-400 w-4"></i>
                        <span><?= htmlspecialchars($order['customer_name']) ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-phone mr-2 text-gray-400 w-4"></i>
                        <span><?= htmlspecialchars($order['customer_phone']) ?></span>
                    </div>
                    <?php if (($order['delivery_type'] ?? 'delivery') === 'pickup'): ?>
                    <div class="flex items-center">
                        <i class="fas fa-store mr-2 text-gray-400 w-4"></i>
                        <span>Retirada no Local</span>
                    </div>
                    <?php else: ?>
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt mr-2 text-gray-400 w-4 mt-1"></i>
                        <span><?= htmlspecialchars($order['customer_address']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($order['payment_method_name']): ?>
                    <div class="flex items-center">
                        <i class="fas fa-credit-card mr-2 text-gray-400 w-4"></i>
                        <span><?= htmlspecialchars($order['payment_method_name']) ?> 
                            (<?= ($order['delivery_type'] ?? 'delivery') === 'pickup' ? 'na retirada' : 'na entrega' ?>)
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Items -->
            <div class="mb-6">
                <h3 class="font-semibold text-gray-900 mb-3">Itens do Pedido</h3>
                <div class="space-y-3">
                    <?php foreach ($order_items as $item): ?>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                        <div>
                            <div class="font-medium text-gray-900"><?= htmlspecialchars($item['product_name']) ?></div>
                            <div class="text-sm text-gray-600"><?= $item['quantity'] ?>x R$ <?= number_format($item['product_price'], 2, ',', '.') ?></div>
                        </div>
                        <div class="font-semibold text-gray-900">
                            R$ <?= number_format($item['quantity'] * $item['product_price'], 2, ',', '.') ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Order Total -->
            <div class="border-t pt-4">
                <div class="flex justify-between items-center text-lg font-bold">
                    <span>Total</span>
                    <span class="primary-text">R$ <?= number_format($order['total_amount'], 2, ',', '.') ?></span>
                </div>
            </div>

            <?php if ($order['notes']): ?>
            <!-- Notes -->
            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-900 mb-1">Observações</h4>
                <p class="text-sm text-gray-600"><?= htmlspecialchars($order['notes']) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Estimated Time -->
        <div class="bg-blue-50 rounded-xl p-4 mb-6">
            <div class="flex items-center text-blue-800">
                <?php if (($order['delivery_type'] ?? 'delivery') === 'pickup'): ?>
                <i class="fas fa-store mr-3 text-xl"></i>
                <div>
                    <div class="font-semibold">Tempo estimado para retirada</div>
                    <div class="text-sm"><?= $establishment['delivery_time'] ?? 30 ?> minutos</div>
                </div>
                <?php else: ?>
                <i class="fas fa-truck mr-3 text-xl"></i>
                <div>
                    <div class="font-semibold">Tempo estimado de entrega</div>
                    <div class="text-sm"><?= $establishment['delivery_time'] ?? 30 ?> minutos</div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- WhatsApp Contact -->
        <?php if ($establishment['whatsapp']): ?>
        <div class="bg-green-50 rounded-xl p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center text-green-800">
                    <i class="fab fa-whatsapp mr-3 text-xl"></i>
                    <div>
                        <div class="font-semibold">Acompanhe seu pedido</div>
                        <div class="text-sm">Entre em contato via WhatsApp</div>
                    </div>
                </div>
                <a href="https://wa.me/<?= preg_replace('/[^\d]/', '', $establishment['whatsapp']) ?>?text=Olá! Gostaria de acompanhar meu pedido %23<?= $order['public_id'] ?>" 
                   target="_blank" 
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fab fa-whatsapp mr-1"></i>
                    Contatar
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="space-y-3">
            <a href="/" class="w-full primary-bg text-white py-4 rounded-xl font-bold text-center block hover:opacity-90 transition-opacity">
                <i class="fas fa-home mr-2"></i>
                Voltar ao Cardápio
            </a>
<!--             
            <button onclick="window.print()" class="w-full bg-gray-600 text-white py-3 rounded-xl font-medium hover:bg-gray-700 transition-colors">
                <i class="fas fa-print mr-2"></i>
                Imprimir Comprovante
            </button> -->
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-sm text-gray-500">
            <p>Obrigado por escolher <?= htmlspecialchars($establishment['name']) ?>!</p>
            <p class="mt-1">Em caso de dúvidas, entre em contato conosco.</p>
        </div>
    </div>

    <script>
        // Auto refresh status every 30 seconds
        setInterval(function() {
            fetch('/api/order-status/<?= $order['public_id'] ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        // Update status display
                        const statusElement = document.querySelector('.bg-blue-100');
                        if (statusElement) {
                            let statusText = '';
                            let statusClass = '';
                            
                            switch(data.status) {
                                case 'pending':
                                    statusText = 'Pendente';
                                    statusClass = 'bg-blue-100 text-blue-800';
                                    break;
                                case 'preparing':
                                    statusText = 'Em Preparação';
                                    statusClass = 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'ready':
                                    statusText = 'Pronto';
                                    statusClass = 'bg-green-100 text-green-800';
                                    break;
                                case 'delivering':
                                    statusText = 'A Caminho';
                                    statusClass = 'bg-purple-100 text-purple-800';
                                    break;
                                case 'delivered':
                                    statusText = 'Entregue';
                                    statusClass = 'bg-green-100 text-green-800';
                                    break;
                            }
                            
                            statusElement.className = `inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${statusClass}`;
                            statusElement.innerHTML = `<i class="fas fa-clock mr-1"></i>${statusText}`;
                        }
                    }
                })
                .catch(error => console.log('Status update failed:', error));
        }, 30000);
    </script>
</body>
</html>
