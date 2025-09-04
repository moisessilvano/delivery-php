<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Endereço de Entrega - <?= htmlspecialchars($establishment['name']) ?></title>
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
            <h1 class="text-lg font-bold">Endereço de Entrega</h1>
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
                <div class="flex items-center">
                    <div class="w-8 h-8 primary-bg text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                    <span class="ml-2 text-sm font-medium">Endereço</span>
                </div>
                <div class="flex-1 mx-4 h-1 bg-gray-200 rounded">
                    <div class="h-1 primary-bg rounded" style="width: 66%"></div>
                </div>
                <div class="flex items-center text-gray-400">
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-sm">3</div>
                    <span class="ml-2 text-sm">Confirmar</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <main class="p-4">
        <form id="address-form" class="space-y-4">
            <div class="bg-white rounded-xl p-4 shadow-sm">
                <h2 class="text-lg font-bold mb-4">Onde você está?</h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2">
                            <label for="cep" class="block text-sm font-medium text-gray-700 mb-1">CEP *</label>
                            <input type="text" id="cep" name="cep" required
                                   class="w-full p-3 border border-gray-300 rounded-lg cep-mask focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="00000-000">
                        </div>
                        <div>
                            <label for="number" class="block text-sm font-medium text-gray-700 mb-1">Número *</label>
                            <input type="text" id="number" name="number" required
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="123">
                        </div>
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Endereço *</label>
                        <input type="text" id="address" name="address" required
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Rua, avenida...">
                    </div>

                    <div>
                        <label for="complement" class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                        <input type="text" id="complement" name="complement"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Apto, bloco, casa...">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="neighborhood" class="block text-sm font-medium text-gray-700 mb-1">Bairro *</label>
                            <input type="text" id="neighborhood" name="neighborhood" required
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="Centro">
                        </div>
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Cidade *</label>
                            <input type="text" id="city" name="city" required
                                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="São Paulo">
                        </div>
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                        <select id="state" name="state" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Selecione o estado</option>
                            <?php 
                            $states = [
                                'AC' => 'Acre', 'AL' => 'Alagoas', 'AP' => 'Amapá', 'AM' => 'Amazonas',
                                'BA' => 'Bahia', 'CE' => 'Ceará', 'DF' => 'Distrito Federal', 'ES' => 'Espírito Santo',
                                'GO' => 'Goiás', 'MA' => 'Maranhão', 'MT' => 'Mato Grosso', 'MS' => 'Mato Grosso do Sul',
                                'MG' => 'Minas Gerais', 'PA' => 'Pará', 'PB' => 'Paraíba', 'PR' => 'Paraná',
                                'PE' => 'Pernambuco', 'PI' => 'Piauí', 'RJ' => 'Rio de Janeiro', 'RN' => 'Rio Grande do Norte',
                                'RS' => 'Rio Grande do Sul', 'RO' => 'Rondônia', 'RR' => 'Roraima', 'SC' => 'Santa Catarina',
                                'SP' => 'São Paulo', 'SE' => 'Sergipe', 'TO' => 'Tocantins'
                            ];
                            foreach ($states as $code => $name): ?>
                            <option value="<?= $code ?>"><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="reference" class="block text-sm font-medium text-gray-700 mb-1">Ponto de Referência</label>
                        <input type="text" id="reference" name="reference"
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Próximo ao mercado, em frente à escola...">
                    </div>
                </div>
            </div>

            <!-- Delivery Info -->
            <div class="bg-blue-50 rounded-xl p-4">
                <div class="flex items-center text-blue-800 mb-2">
                    <i class="fas fa-truck mr-2"></i>
                    <span class="font-medium">Informações de Entrega</span>
                </div>
                <div class="text-sm text-blue-700 space-y-1">
                    <div><strong>Tempo estimado:</strong> <?= $establishment['delivery_time'] ?? 30 ?> minutos</div>
                    <div><strong>Taxa de entrega:</strong> R$ <?= number_format($establishment['delivery_fee'] ?? 0, 2, ',', '.') ?></div>
                    <?php if ($establishment['min_order_value'] > 0): ?>
                    <div><strong>Pedido mínimo:</strong> R$ <?= number_format($establishment['min_order_value'], 2, ',', '.') ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit" class="w-full primary-bg text-white py-4 rounded-xl font-bold text-lg">
                Revisar Pedido
                <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </form>
    </main>

    <script src="/js/app.js"></script>
    <script>
        // Load existing customer address if available
        const customerData = JSON.parse(localStorage.getItem('customer_data') || '{}');
        if (customerData) {
            document.getElementById('cep').value = customerData.cep || '';
            document.getElementById('address').value = customerData.address || '';
            document.getElementById('number').value = customerData.number || '';
            document.getElementById('complement').value = customerData.complement || '';
            document.getElementById('neighborhood').value = customerData.neighborhood || '';
            document.getElementById('city').value = customerData.city || '';
            document.getElementById('state').value = customerData.state || '';
        }

        // Form submission
        document.getElementById('address-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const addressData = {
                cep: formData.get('cep'),
                address: formData.get('address'),
                number: formData.get('number'),
                complement: formData.get('complement'),
                neighborhood: formData.get('neighborhood'),
                city: formData.get('city'),
                state: formData.get('state'),
                reference: formData.get('reference')
            };

            // Store data for next step
            localStorage.setItem('checkout_address', JSON.stringify(addressData));

            // Proceed to step 3
            window.location.href = '/checkout-step3';
        });
    </script>
</body>
</html>
