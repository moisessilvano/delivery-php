-- Comida SM - MySQL Schema
-- Execute este script no seu banco MySQL
-- IMPORTANTE: Execute as tabelas na ordem correta para evitar erros de chave estrangeira

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