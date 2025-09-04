<?php
$title = 'Dashboard - ' . $establishment['name'];
$showNavbar = true;
?>

<div class="space-y-4 md:space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-4 md:p-6">
        <h1 class="text-xl md:text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm md:text-base text-gray-600">Bem-vindo ao painel de controle do <?= htmlspecialchars($establishment['name']) ?></p>
    </div>

    <!-- Setup Progress -->
    <?php if (!$setup_progress['is_complete']): ?>
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg text-white p-4 md:p-6" id="setup-progress">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-3 md:space-y-0">
            <div>
                <h2 class="text-lg md:text-xl font-bold">Configure seu Cardápio</h2>
                <p class="text-sm md:text-base text-blue-100">Complete os passos abaixo para seu cardápio ficar online</p>
            </div>
            <div class="text-center md:text-right">
                <div class="text-2xl md:text-3xl font-bold"><?= $setup_progress['percentage'] ?>%</div>
                <div class="text-xs md:text-sm text-blue-100"><?= $setup_progress['completed_count'] ?> de <?= $setup_progress['total_count'] ?> concluídos</div>
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="w-full bg-blue-400 rounded-full h-2 md:h-3 mb-4 md:mb-6">
            <div class="bg-white h-2 md:h-3 rounded-full transition-all duration-500" style="width: <?= $setup_progress['percentage'] ?>%"></div>
        </div>
        
        <!-- Setup Steps -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
            <?php foreach ($setup_progress['steps'] as $step): ?>
            <div class="bg-white bg-opacity-10 rounded-lg p-3 md:p-4 border border-white border-opacity-20">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <?php if ($step['completed']): ?>
                        <div class="w-7 h-7 md:w-8 md:h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs md:text-sm"></i>
                        </div>
                        <?php else: ?>
                        <div class="w-7 h-7 md:w-8 md:h-8 bg-orange-500 rounded-full flex items-center justify-center">
                            <i class="<?= $step['icon'] ?> text-white text-xs md:text-sm"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm md:text-base font-medium text-white"><?= htmlspecialchars($step['title']) ?></h3>
                        <p class="text-xs md:text-sm text-blue-100 mb-2"><?= htmlspecialchars($step['description']) ?></p>
                        <a href="<?= $step['action_url'] ?>" 
                           class="inline-flex items-center text-xs md:text-sm font-medium text-white bg-white bg-opacity-20 px-2 md:px-3 py-1 rounded-full hover:bg-opacity-30 transition-colors">
                            <?= htmlspecialchars($step['action_text']) ?>
                            <i class="fas fa-arrow-right ml-1 text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <!-- Completed Setup - Minimized -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-3 md:p-4" id="setup-complete">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
            <div class="flex items-center">
                <div class="w-8 h-8 md:w-10 md:h-10 bg-green-500 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-check text-white text-sm md:text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm md:text-base font-medium text-green-900">Configuração Completa!</h3>
                    <p class="text-xs md:text-sm text-green-700">Seu cardápio está pronto e funcionando</p>
                </div>
            </div>
            <div class="flex items-center justify-between md:justify-end space-x-3">
                <a href="https://<?= htmlspecialchars($establishment['subdomain']) ?>.localhost:8000" 
                   target="_blank" 
                   class="text-xs md:text-sm font-medium text-green-700 hover:text-green-800">
                    Ver Cardápio
                    <i class="fas fa-external-link-alt ml-1"></i>
                </a>
                <button onclick="toggleSetupDetails()" 
                        class="text-green-700 hover:text-green-800">
                    <i class="fas fa-chevron-down" id="setup-chevron"></i>
                </button>
            </div>
        </div>
        
        <!-- Expandable Details -->
        <div id="setup-details" class="hidden mt-3 md:mt-4 pt-3 md:pt-4 border-t border-green-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 md:gap-3">
                <?php foreach ($setup_progress['steps'] as $step): ?>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 md:w-5 md:h-5 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-white text-xs"></i>
                    </div>
                    <span class="text-xs md:text-sm text-green-800"><?= htmlspecialchars($step['title']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-3 md:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-box text-lg md:text-2xl text-primary-600"></i>
                    </div>
                    <div class="ml-3 md:ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-xs md:text-sm font-medium text-gray-500 truncate">Produtos</dt>
                            <dd class="text-base md:text-lg font-medium text-gray-900"><?= $stats['total_products'] ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-3 md:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-tags text-lg md:text-2xl text-green-600"></i>
                    </div>
                    <div class="ml-3 md:ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-xs md:text-sm font-medium text-gray-500 truncate">Categorias</dt>
                            <dd class="text-base md:text-lg font-medium text-gray-900"><?= $stats['total_categories'] ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-3 md:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shopping-cart text-lg md:text-2xl text-yellow-600"></i>
                    </div>
                    <div class="ml-3 md:ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-xs md:text-sm font-medium text-gray-500 truncate">Hoje</dt>
                            <dd class="text-base md:text-lg font-medium text-gray-900"><?= $stats['today_orders'] ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-3 md:p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-clock text-lg md:text-2xl text-red-600"></i>
                    </div>
                    <div class="ml-3 md:ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-xs md:text-sm font-medium text-gray-500 truncate">Pendentes</dt>
                            <dd class="text-base md:text-lg font-medium text-gray-900"><?= $stats['pending_orders'] ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow rounded-lg p-4 md:p-6">
        <h2 class="text-base md:text-lg font-medium text-gray-900 mb-3 md:mb-4">Ações Rápidas</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
            <a href="/products/create" class="flex items-center p-3 md:p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-plus text-primary-600 text-lg md:text-xl mr-3"></i>
                <div>
                    <h3 class="text-sm md:text-base font-medium text-gray-900">Adicionar Produto</h3>
                    <p class="text-xs md:text-sm text-gray-500">Criar novo item no cardápio</p>
                </div>
            </a>
            
            <a href="/categories/create" class="flex items-center p-3 md:p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-folder-plus text-green-600 text-lg md:text-xl mr-3"></i>
                <div>
                    <h3 class="text-sm md:text-base font-medium text-gray-900">Nova Categoria</h3>
                    <p class="text-xs md:text-sm text-gray-500">Organizar produtos</p>
                </div>
            </a>
            
            <a href="/orders" class="flex items-center p-3 md:p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors sm:col-span-2 lg:col-span-1">
                <i class="fas fa-list text-yellow-600 text-lg md:text-xl mr-3"></i>
                <div>
                    <h3 class="text-sm md:text-base font-medium text-gray-900">Ver Pedidos</h3>
                    <p class="text-xs md:text-sm text-gray-500">Gerenciar pedidos</p>
                </div>
            </a>
        </div>
    </div>

</div>

<script>
function toggleSetupDetails() {
    const details = document.getElementById('setup-details');
    const chevron = document.getElementById('setup-chevron');
    
    if (details.classList.contains('hidden')) {
        details.classList.remove('hidden');
        chevron.classList.remove('fa-chevron-down');
        chevron.classList.add('fa-chevron-up');
    } else {
        details.classList.add('hidden');
        chevron.classList.remove('fa-chevron-up');
        chevron.classList.add('fa-chevron-down');
    }
}
</script>

