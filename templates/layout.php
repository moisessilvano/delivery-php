<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Comida SM' ?></title>
    <link href="/css/output.css" rel="stylesheet">
    <!-- Debug: CSS path -->
    <style>
        /* Fallback styles while CSS loads */
        body { font-family: Arial, sans-serif; }
        .bg-gray-50 { background-color: #f9fafb; }
        .text-primary-600 { color: #2563eb; }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php if (isset($showNavbar) && $showNavbar): ?>
    <!-- Desktop Navigation -->
    <nav class="bg-white shadow-sm border-b hidden md:block">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <a href="/dashboard" class="text-xl font-bold text-primary-600">
                        Comida SM
                    </a>
                    
                    <!-- Navigation Menu -->
                    <div class="flex space-x-6">
                        <a href="/dashboard" class="text-gray-700 hover:text-primary-600 px-3 py-2 text-sm font-medium">
                            <i class="fas fa-chart-line mr-1"></i>Dashboard
                        </a>
                        <a href="/categories" class="text-gray-700 hover:text-primary-600 px-3 py-2 text-sm font-medium">
                            <i class="fas fa-tags mr-1"></i>Categorias
                        </a>
                        <a href="/products" class="text-gray-700 hover:text-primary-600 px-3 py-2 text-sm font-medium">
                            <i class="fas fa-box mr-1"></i>Produtos
                        </a>
                        <a href="/customers" class="text-gray-700 hover:text-primary-600 px-3 py-2 text-sm font-medium">
                            <i class="fas fa-users mr-1"></i>Clientes
                        </a>
                        <a href="/orders" class="text-gray-700 hover:text-primary-600 px-3 py-2 text-sm font-medium">
                            <i class="fas fa-shopping-cart mr-1"></i>Pedidos
                        </a>
                        
                        <!-- Dropdown for Settings -->
                        <div class="relative group">
                            <button class="text-gray-700 hover:text-primary-600 px-3 py-2 text-sm font-medium flex items-center">
                                <i class="fas fa-cog mr-1"></i>Configurações
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <div class="py-1">
                                    <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-store mr-2"></i>Perfil do Estabelecimento
                                    </a>
                                    <a href="/payment-methods" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-credit-card mr-2"></i>Formas de Pagamento
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700"><?= $_SESSION['user_name'] ?? '' ?></span>
                    <a href="/logout" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation -->
    <nav class="bg-white shadow-sm border-b md:hidden">
        <div class="px-4">
            <div class="flex justify-between items-center h-16">
                <a href="/dashboard" class="text-lg font-bold text-primary-600">
                    Comida SM
                </a>
                <button onclick="toggleMobileMenu()" class="p-2 rounded-md text-gray-700 hover:text-primary-600 hover:bg-gray-100">
                    <i class="fas fa-bars text-xl" id="mobile-menu-icon"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden bg-white border-t border-gray-200">
            <div class="px-4 py-2 space-y-1">
                <a href="/dashboard" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-100 rounded-md">
                    <i class="fas fa-chart-line mr-3"></i>Dashboard
                </a>
                <a href="/categories" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-100 rounded-md">
                    <i class="fas fa-tags mr-3"></i>Categorias
                </a>
                <a href="/products" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-100 rounded-md">
                    <i class="fas fa-box mr-3"></i>Produtos
                </a>
                <a href="/customers" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-100 rounded-md">
                    <i class="fas fa-users mr-3"></i>Clientes
                </a>
                <a href="/orders" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-100 rounded-md">
                    <i class="fas fa-shopping-cart mr-3"></i>Pedidos
                </a>
                <div class="border-t border-gray-200 my-2"></div>
                <a href="/profile" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-100 rounded-md">
                    <i class="fas fa-store mr-3"></i>Perfil do Estabelecimento
                </a>
                <a href="/payment-methods" class="flex items-center px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-100 rounded-md">
                    <i class="fas fa-credit-card mr-3"></i>Formas de Pagamento
                </a>
                <div class="border-t border-gray-200 my-2"></div>
                <div class="flex items-center justify-between px-3 py-2">
                    <span class="text-gray-700"><?= $_SESSION['user_name'] ?? '' ?></span>
                    <a href="/logout" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-sign-out-alt mr-2"></i>Sair
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <main class="<?= isset($showNavbar) && $showNavbar ? 'max-w-7xl mx-auto py-4 md:py-6 px-4 sm:px-6 lg:px-8' : '' ?>">
        <?php if (isset($error) && $error): ?>
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <?php if (isset($success) && $success): ?>
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <?= htmlspecialchars($success) ?>
        </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <script src="/js/app.js"></script>
    <script>
        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('mobile-menu-icon');
            
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                menu.classList.add('hidden');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobile-menu');
            const button = document.querySelector('[onclick="toggleMobileMenu()"]');
            
            if (menu && !menu.classList.contains('hidden')) {
                if (!menu.contains(event.target) && !button.contains(event.target)) {
                    menu.classList.add('hidden');
                    const icon = document.getElementById('mobile-menu-icon');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });

        // Auto-hide alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.bg-red-100, .bg-green-100');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>

