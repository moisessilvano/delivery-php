# 🍕 Comida SM - Plataforma de Cardápios

Plataforma similar ao Appzei para gestão de cardápios e pedidos online.

## 🚀 Início Rápido

### Desenvolvimento Local

```bash
# 1. Instalar dependências
composer install

# 2. Iniciar servidor de desenvolvimento
composer dev
# ou
php server.php

# 3. Acessar a aplicação
# Admin: http://localhost:8000
# Cardápio: http://teste.localhost:8000
```

### Comandos Disponíveis

```bash
# Servidor de desenvolvimento
composer dev

# Migrar para PostgreSQL
composer migrate

# Backup do banco
composer backup

# Servidor PHP built-in
composer serve
```

## 📁 Estrutura do Projeto

```
comida-sm/
├── index.php              # Front controller (raiz) - para Apache
├── .htaccess              # Configuração Apache
├── server.php             # Servidor de desenvolvimento
├── composer.json          # Dependências PHP
├── public/                # Document root
│   ├── index.php          # Front controller (public)
│   ├── css/               # Arquivos CSS
│   ├── js/                # Arquivos JavaScript
│   ├── audio/             # Sons e notificações
│   └── icons/             # Ícones e imagens
├── src/                   # Código da aplicação
│   ├── Controllers/
│   ├── Core/
│   └── Services/
├── templates/             # Templates PHP
├── storage/               # Uploads e cache
├── database/              # Banco SQLite
└── vendor/                # Dependências
```

## 🗄️ Banco de Dados

### SQLite (Desenvolvimento)
- Arquivo: `database/app.sqlite`
- Configuração automática

### PostgreSQL (Produção)
```bash
# Migrar dados
php migrate_to_postgresql.php
```

## 🌐 URLs

### Admin Panel
- **Login**: `http://localhost:8000/`
- **Dashboard**: `http://localhost:8000/dashboard`
- **Pedidos**: `http://localhost:8000/orders/kanban`

### Cardápio Público
- **Menu**: `http://teste.localhost:8000/`
- **Carrinho**: `http://teste.localhost:8000/cart`
- **Checkout**: `http://teste.localhost:8000/checkout-step1`

## 🔧 Configuração

### Variáveis de Ambiente (.env)
```env
# Database
DB_DRIVER=sqlite
DB_DATABASE=database/app.sqlite

# App
APP_URL=http://localhost:8000
APP_DEBUG=true
```

### Apache (.htaccess)
- Rewrite rules para URLs limpas
- Headers de segurança
- Compressão Gzip
- Cache de arquivos estáticos

## 📱 Funcionalidades

### Admin
- ✅ Gestão de estabelecimentos
- ✅ Categorias e produtos
- ✅ Pedidos em tempo real (Kanban)
- ✅ Clientes e formas de pagamento
- ✅ Customizações avançadas de produtos

### Público
- ✅ Cardápio responsivo
- ✅ Carrinho de compras
- ✅ Checkout em etapas
- ✅ Login automático por telefone
- ✅ PWA (Progressive Web App)

## 🚀 Deploy

### Desenvolvimento
```bash
composer dev
```

### Produção
```bash
# 1. Configurar .env
# 2. Instalar dependências
composer install --no-dev

# 3. Configurar Apache/Nginx
# 4. Migrar para PostgreSQL
composer migrate
```

## 🛠️ Tecnologias

- **Backend**: PHP 8.0+, PDO
- **Frontend**: HTML5, CSS3, JavaScript ES6+
- **Database**: SQLite (dev) / PostgreSQL (prod)
- **Styling**: Tailwind CSS
- **Icons**: Font Awesome
- **PWA**: Service Worker

## 📄 Licença

MIT License - veja arquivo LICENSE para detalhes.