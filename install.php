<?php
/**
 * Comida SM - Instalador
 * Script para instalação inicial da aplicação
 */

// Verificar se já foi instalado
if (file_exists('.env') && file_exists('database/installed.flag')) {
    // Auto-remover o instalador após instalação
    if (file_exists(__FILE__)) {
        unlink(__FILE__);
    }
    die('A aplicação já foi instalada. O instalador foi removido automaticamente por segurança.');
}

// Iniciar sessão para manter dados entre etapas
session_start();

// Processar formulário
if ($_POST) {
    $step = $_POST['step'] ?? 1;
    
    if ($step == 1) {
        // Validar dados do banco
        $db_host = $_POST['db_host'] ?? 'localhost';
        $db_port = $_POST['db_port'] ?? '3306';
        $db_name = $_POST['db_name'] ?? 'comida_sm';
        $db_user = $_POST['db_user'] ?? 'root';
        $db_pass = $_POST['db_pass'] ?? '';
        
        // Testar conexão
        try {
            $pdo = new PDO("mysql:host=$db_host;port=$db_port;charset=utf8mb4", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Criar banco se não existir
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `$db_name`");
            
            $_SESSION['db_config'] = [
                'host' => $db_host,
                'port' => $db_port,
                'name' => $db_name,
                'user' => $db_user,
                'pass' => $db_pass
            ];
            
            $step = 2;
        } catch (PDOException $e) {
            $error = "Erro na conexão com o banco: " . $e->getMessage();
        }
    } elseif ($step == 2) {
        // Criar arquivo .env
        $db_config = $_SESSION['db_config'];
        $app_name = $_POST['app_name'] ?? 'Comida SM';
        $app_url = $_POST['app_url'] ?? 'http://localhost';
        
        $env_content = "# Comida SM - Environment Configuration\n\n";
        $env_content .= "# Application\n";
        $env_content .= "APP_NAME=\"$app_name\"\n";
        $env_content .= "APP_URL=\"$app_url\"\n";
        $env_content .= "APP_ENV=development\n";
        $env_content .= "APP_DEBUG=true\n";
        $env_content .= "APP_TIMEZONE=\"America/Sao_Paulo\"\n\n";
        $env_content .= "# Database - MySQL Configuration\n";
        $env_content .= "DB_DRIVER=mysql\n";
        $env_content .= "DB_HOST={$db_config['host']}\n";
        $env_content .= "DB_PORT={$db_config['port']}\n";
        $env_content .= "DB_DATABASE={$db_config['name']}\n";
        $env_content .= "DB_USERNAME={$db_config['user']}\n";
        $env_content .= "DB_PASSWORD={$db_config['pass']}\n";
        $env_content .= "DB_PREFIX=\n\n";
        $env_content .= "# Upload\n";
        $env_content .= "UPLOAD_PATH=storage/\n";
        $env_content .= "UPLOAD_MAX_SIZE=5242880\n";
        $env_content .= "UPLOAD_ALLOWED_TYPES=jpg,jpeg,png,gif,webp\n\n";
        $env_content .= "# Security\n";
        $env_content .= "APP_KEY=" . bin2hex(random_bytes(32)) . "\n";
        
        file_put_contents('.env', $env_content);
        
        // Criar tabelas diretamente
        try {
            // Conectar ao banco
            $pdo = new PDO(
                "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['name']};charset=utf8mb4",
                $db_config['user'],
                $db_config['pass']
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Executar schema SQL
            $sql = getMySQLSchema();
            $pdo->exec($sql);
            
            // Criar usuário admin
            $admin_name = $_POST['admin_name'] ?? 'Administrador';
            $admin_email = $_POST['admin_email'] ?? 'admin@gmail.com';
            $admin_password = password_hash($_POST['admin_password'] ?? 'admin123', PASSWORD_DEFAULT);
            
            // Criar estabelecimento padrão
            $stmt = $pdo->prepare("INSERT INTO establishments (subdomain, name, description, is_active) VALUES (?, ?, ?, ?)");
            $stmt->execute(['demo', 'Estabelecimento Demo', 'Estabelecimento de demonstração', 1]);
            $establishment_id = $pdo->lastInsertId();
            
            // Criar usuário admin
            $stmt = $pdo->prepare("INSERT INTO users (establishment_id, name, email, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$establishment_id, $admin_name, $admin_email, $admin_password, 'admin']);
            
            // Criar flag de instalação
            if (!is_dir('database')) {
                mkdir('database', 0755, true);
            }
            file_put_contents('database/installed.flag', date('Y-m-d H:i:s'));
            
            // Auto-remover o instalador após instalação bem-sucedida
            $installer_removed = false;
            if (file_exists(__FILE__)) {
                $installer_removed = unlink(__FILE__);
            }
            
            $step = 3;
        } catch (Exception $e) {
            $error = "Erro ao criar tabelas: " . $e->getMessage();
        }
    }
} else {
    $step = 1;
}

function getMySQLSchema() {
    return "
    -- Estabelecimentos (primeira tabela - sem dependências)
    CREATE TABLE IF NOT EXISTS establishments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subdomain VARCHAR(50) UNIQUE NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        logo VARCHAR(255),
        photo VARCHAR(255),
        address TEXT,
        phone VARCHAR(20),
        whatsapp VARCHAR(20),
        is_whatsapp BOOLEAN DEFAULT 0,
        instagram VARCHAR(100),
        facebook VARCHAR(100),
        delivery_time INT DEFAULT 30,
        delivery_fee DECIMAL(10,2) DEFAULT 0,
        min_order_value DECIMAL(10,2) DEFAULT 0,
        latitude DECIMAL(10,8),
        longitude DECIMAL(11,8),
        primary_color VARCHAR(7) DEFAULT '#3B82F6',
        secondary_color VARCHAR(7) DEFAULT '#1E40AF',
        background_color VARCHAR(7) DEFAULT '#F8FAFC',
        text_color VARCHAR(7) DEFAULT '#1F2937',
        cep VARCHAR(10),
        street_address VARCHAR(255),
        number VARCHAR(20),
        complement VARCHAR(255),
        neighborhood VARCHAR(255),
        city VARCHAR(255),
        state VARCHAR(2),
        category VARCHAR(100),
        operating_hours TEXT,
        special_hours_note TEXT,
        is_24_hours BOOLEAN DEFAULT 0,
        different_holiday_hours BOOLEAN DEFAULT 0,
        show_rating BOOLEAN DEFAULT 1,
        show_delivery_time BOOLEAN DEFAULT 1,
        show_phone BOOLEAN DEFAULT 1,
        accept_reviews BOOLEAN DEFAULT 1,
        featured_message VARCHAR(255),
        is_featured BOOLEAN DEFAULT 0,
        accepts_delivery BOOLEAN DEFAULT 1,
        accepts_pickup BOOLEAN DEFAULT 1,
        free_delivery_above BOOLEAN DEFAULT 0,
        free_delivery_value DECIMAL(10,2),
        payment_methods TEXT,
        max_delivery_distance DECIMAL(5,2),
        is_active BOOLEAN DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- Usuários admin (depende de establishments)
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        establishment_id INT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(20) DEFAULT 'admin',
        is_active BOOLEAN DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (establishment_id) REFERENCES establishments(id) ON DELETE CASCADE
    );

    -- Clientes (depende de establishments)
    CREATE TABLE IF NOT EXISTS customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        establishment_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        phone VARCHAR(20) NOT NULL,
        cep VARCHAR(10),
        address TEXT,
        number VARCHAR(20),
        complement VARCHAR(255),
        neighborhood VARCHAR(255),
        city VARCHAR(255),
        state VARCHAR(2),
        latitude DECIMAL(10,8),
        longitude DECIMAL(11,8),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (establishment_id) REFERENCES establishments(id) ON DELETE CASCADE
    );

    -- Formas de pagamento (depende de establishments)
    CREATE TABLE IF NOT EXISTS payment_methods (
        id INT AUTO_INCREMENT PRIMARY KEY,
        establishment_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        type VARCHAR(50) NOT NULL,
        sort_order INT DEFAULT 0,
        is_active BOOLEAN DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (establishment_id) REFERENCES establishments(id) ON DELETE CASCADE
    );

    -- Horários de funcionamento (depende de establishments)
    CREATE TABLE IF NOT EXISTS business_hours (
        id INT AUTO_INCREMENT PRIMARY KEY,
        establishment_id INT NOT NULL,
        day_of_week INT NOT NULL,
        open_time TIME,
        close_time TIME,
        is_closed BOOLEAN DEFAULT 0,
        FOREIGN KEY (establishment_id) REFERENCES establishments(id) ON DELETE CASCADE
    );

    -- Categorias (depende de establishments)
    CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        establishment_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        image VARCHAR(255),
        sort_order INT DEFAULT 0,
        is_active BOOLEAN DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (establishment_id) REFERENCES establishments(id) ON DELETE CASCADE
    );

    -- Produtos (depende de establishments e categories)
    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        establishment_id INT NOT NULL,
        category_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image VARCHAR(255),
        is_available BOOLEAN DEFAULT 1,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (establishment_id) REFERENCES establishments(id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    );

    -- Grupos de opções de produtos (depende de products)
    CREATE TABLE IF NOT EXISTS product_option_groups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        is_required BOOLEAN DEFAULT 0,
        min_selections INT DEFAULT 1,
        max_selections INT DEFAULT 1,
        sort_order INT DEFAULT 0,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    );

    -- Opções de produtos (depende de product_option_groups)
    CREATE TABLE IF NOT EXISTS product_options (
        id INT AUTO_INCREMENT PRIMARY KEY,
        group_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        price_adjustment DECIMAL(10,2) DEFAULT 0,
        is_available BOOLEAN DEFAULT 1,
        sort_order INT DEFAULT 0,
        FOREIGN KEY (group_id) REFERENCES product_option_groups(id) ON DELETE CASCADE
    );

    -- Zonas de entrega (depende de establishments)
    CREATE TABLE IF NOT EXISTS delivery_zones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        establishment_id INT NOT NULL,
        name VARCHAR(255) NOT NULL,
        radius_km DECIMAL(5,2) NOT NULL,
        delivery_fee DECIMAL(10,2) NOT NULL,
        min_order_value DECIMAL(10,2) DEFAULT 0,
        is_active BOOLEAN DEFAULT 1,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (establishment_id) REFERENCES establishments(id) ON DELETE CASCADE
    );

    -- Pedidos (depende de establishments, customers, payment_methods)
    CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        public_id VARCHAR(32) UNIQUE NOT NULL,
        establishment_id INT NOT NULL,
        customer_id INT,
        customer_name VARCHAR(255) NOT NULL,
        customer_phone VARCHAR(20) NOT NULL,
        customer_address TEXT,
        delivery_type VARCHAR(20) DEFAULT 'delivery',
        payment_method_id INT,
        total_amount DECIMAL(10,2) NOT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        notes TEXT,
        notified BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (establishment_id) REFERENCES establishments(id) ON DELETE CASCADE,
        FOREIGN KEY (customer_id) REFERENCES customers(id),
        FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id)
    );

    -- Itens do pedido (depende de orders e products)
    CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        product_name VARCHAR(255) NOT NULL,
        product_price DECIMAL(10,2) NOT NULL,
        quantity INT NOT NULL,
        options TEXT,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id)
    );

    -- Notificações push (depende de establishments)
    CREATE TABLE IF NOT EXISTS push_subscriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        establishment_id INT NOT NULL,
        customer_phone VARCHAR(20),
        endpoint TEXT NOT NULL,
        p256dh TEXT NOT NULL,
        auth TEXT NOT NULL,
        is_active BOOLEAN DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (establishment_id) REFERENCES establishments(id) ON DELETE CASCADE
    );
    ";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - Comida SM</title>
    <link href="/css/output.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-primary-100">
                <i class="fas fa-utensils text-primary-600 text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Instalação do Comida SM
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Configure sua aplicação de delivery
            </p>
        </div>

        <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
        <!-- Etapa 1: Configuração do Banco -->
        <form class="mt-8 space-y-6" method="POST">
            <input type="hidden" name="step" value="1">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Host do Banco</label>
                    <input type="text" name="db_host" value="localhost" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Porta</label>
                    <input type="number" name="db_port" value="3306" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome do Banco</label>
                    <input type="text" name="db_name" value="comida_sm" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Usuário</label>
                    <input type="text" name="db_user" value="root" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Senha</label>
                    <input type="password" name="db_pass"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
            </div>
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Testar Conexão e Continuar
            </button>
        </form>

        <?php elseif ($step == 2): ?>
        <!-- Etapa 2: Configuração da Aplicação -->
        <form class="mt-8 space-y-6" method="POST">
            <input type="hidden" name="step" value="2">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome da Aplicação</label>
                    <input type="text" name="app_name" value="Comida SM" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">URL da Aplicação</label>
                    <input type="url" name="app_url" value="http://localhost" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div class="border-t pt-4">
                    <h3 class="text-lg font-medium text-gray-900">Usuário Administrador</h3>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome Completo</label>
                    <input type="text" name="admin_name" value="Administrador" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="admin_email" value="admin@gmail.com" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Senha</label>
                    <input type="password" name="admin_password" value="admin123" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
            </div>
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Instalar Aplicação
            </button>
        </form>

        <?php elseif ($step == 3): ?>
        <!-- Etapa 3: Instalação Concluída -->
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
            <h3 class="mt-2 text-lg font-medium text-gray-900">Instalação Concluída!</h3>
            <p class="mt-1 text-sm text-gray-500">
                Sua aplicação foi instalada com sucesso.
            </p>
            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-shield-alt mr-2"></i>
                    <strong>Segurança:</strong> O instalador foi removido automaticamente por segurança.
                </p>
            </div>
            <div class="mt-6">
                <a href="/" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Acessar Aplicação
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
