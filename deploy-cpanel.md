# 🚀 Deploy no cPanel - Comida SM

## 📋 Pré-requisitos

### cPanel
- Acesso ao cPanel do seu provedor
- PHP 8.0+ habilitado
- Extensões PHP: PDO, PDO_SQLite, PDO_PGSQL, JSON, MBString, CURL, ZIP
- Acesso ao File Manager
- Acesso ao phpMyAdmin (se usar MySQL/PostgreSQL)

## 📁 Estrutura para cPanel

### 1. Upload dos Arquivos

```
public_html/
├── index.php              # Front controller
├── .htaccess              # Configuração Apache
├── composer.json          # Dependências
├── composer.lock          # Lock das dependências
├── vendor/                # Dependências PHP (via Composer)
├── src/                   # Código da aplicação
├── templates/             # Templates
├── storage/               # Uploads e cache
├── database/              # Banco SQLite
├── public/                # Assets estáticos
│   ├── css/
│   ├── js/
│   ├── audio/
│   └── icons/
└── .env                   # Configurações (criar no cPanel)
```

## 🔧 Configuração no cPanel

### 1. Upload via File Manager

1. **Acesse o File Manager** no cPanel
2. **Navegue para public_html**
3. **Faça upload** de todos os arquivos do projeto
4. **Extraia** se estiver em ZIP

### 2. Configurar Permissões

```bash
# Via File Manager ou Terminal
chmod 755 public_html/
chmod 755 public_html/storage/
chmod 755 public_html/database/
chmod 644 public_html/.env
chmod 644 public_html/.htaccess
```

### 3. Criar Arquivo .env

Crie um arquivo `.env` na raiz do `public_html`:

```env
# Database Configuration
DB_DRIVER=sqlite
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=database/app.sqlite
DB_USERNAME=
DB_PASSWORD=

# App Configuration
APP_NAME=Comida SM
APP_URL=https://seudominio.com
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=America/Sao_Paulo

# Upload Configuration
UPLOAD_PATH=storage/
UPLOAD_MAX_SIZE=5242880
UPLOAD_ALLOWED_TYPES=jpg,jpeg,png,gif,webp

# Session Configuration
SESSION_DRIVER=files
SESSION_LIFETIME=120
```

### 4. Instalar Dependências via Terminal

Se o cPanel tiver acesso ao Terminal:

```bash
cd public_html
composer install --no-dev --optimize-autoloader
```

**OU** faça upload da pasta `vendor/` completa.

## 🌐 Configuração de Domínio

### 1. Subdomínios

Para usar subdomínios (ex: `teste.seudominio.com`):

1. **Crie subdomínios** no cPanel
2. **Aponte** para a mesma pasta `public_html`
3. **Configure** o `.htaccess` para detectar subdomínios

### 2. Domínio Principal

- **Admin**: `https://seudominio.com/`
- **Cardápio**: `https://teste.seudominio.com/`

## 🔒 Configuração de Segurança

### 1. .htaccess Otimizado

```apache
# Comida SM - cPanel Configuration
RewriteEngine On

# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security headers
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-XSS-Protection "1; mode=block"
Header always set X-Content-Type-Options "nosniff"
Header always set Referrer-Policy "no-referrer-when-downgrade"

# Handle subdomains
RewriteCond %{HTTP_HOST} ^([^.]+)\.([^.]+)$
RewriteRule ^(.*)$ index.php?subdomain=%1&path=%{REQUEST_URI} [QSA,L]

# Serve static files directly
RewriteCond %{REQUEST_FILENAME} -f
RewriteCond %{REQUEST_URI} \.(css|js|png|jpg|jpeg|gif|webp|ico|svg|woff|woff2|ttf|eot)$
RewriteRule ^(.*)$ public/$1 [L]

# Redirect all requests to index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Prevent access to sensitive files
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

<FilesMatch "\.(env|log|sql|md|txt)$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Block access to directories
<DirectoryMatch "(vendor|src|storage|database|templates)">
    Order allow,deny
    Deny from all
</DirectoryMatch>

# Gzip compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Cache static files
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
    ExpiresByType font/ttf "access plus 1 year"
</IfModule>
```

## 🗄️ Configuração do Banco de Dados

### Opção 1: SQLite (Mais Simples)

1. **Crie** a pasta `database/` em `public_html/`
2. **Configure** permissões: `chmod 755 database/`
3. **O banco** será criado automaticamente

### Opção 2: MySQL (Recomendado para Produção)

1. **Crie** um banco MySQL no cPanel
2. **Configure** o `.env`:

```env
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=seu_banco_mysql
DB_USERNAME=seu_usuario_mysql
DB_PASSWORD=sua_senha_mysql
```

3. **Migre** os dados do SQLite para MySQL

## 📱 Configuração PWA

### 1. Service Worker

O arquivo `public/sw.js` já está configurado.

### 2. Manifest

O arquivo `public/manifest.json` já está configurado.

### 3. Ícones

Faça upload dos ícones na pasta `public/icons/`

## 🔧 Troubleshooting

### Problemas Comuns

1. **Erro 500**
   - Verifique permissões das pastas
   - Verifique se o PHP 8.0+ está ativo
   - Verifique logs de erro no cPanel

2. **CSS/JS não carregam**
   - Verifique se o `.htaccess` está correto
   - Verifique se os arquivos estão em `public/`

3. **Banco de dados não funciona**
   - Verifique permissões da pasta `database/`
   - Verifique configuração do `.env`

4. **Subdomínios não funcionam**
   - Verifique se os subdomínios estão criados
   - Verifique configuração do `.htaccess`

### Logs de Debug

Ative logs no `.env`:

```env
APP_DEBUG=true
```

## 📊 Monitoramento

### 1. Logs de Acesso

- **cPanel > Logs > Raw Access Logs**
- **cPanel > Logs > Error Logs**

### 2. Estatísticas

- **cPanel > Estatísticas**
- **cPanel > Uso de Disco**

## 🚀 Deploy Automatizado

### 1. Script de Deploy

```bash
#!/bin/bash
# deploy-cpanel.sh

echo "🚀 Deploy para cPanel..."

# Upload via FTP/SFTP
rsync -avz --exclude 'node_modules' --exclude '.git' \
  ./ user@seudominio.com:public_html/

# Configurar permissões
ssh user@seudominio.com "chmod 755 public_html/storage public_html/database"

echo "✅ Deploy concluído!"
```

### 2. GitHub Actions (Opcional)

```yaml
name: Deploy to cPanel

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Deploy to cPanel
      uses: SamKirkland/FTP-Deploy-Action@4.0.0
      with:
        server: ${{ secrets.FTP_SERVER }}
        username: ${{ secrets.FTP_USERNAME }}
        password: ${{ secrets.FTP_PASSWORD }}
        local-dir: ./
        server-dir: public_html/
```

## ✅ Checklist de Deploy

- [ ] Arquivos enviados para `public_html/`
- [ ] Permissões configuradas
- [ ] Arquivo `.env` criado
- [ ] `.htaccess` configurado
- [ ] Dependências instaladas
- [ ] Banco de dados configurado
- [ ] Subdomínios criados
- [ ] SSL configurado
- [ ] Teste de funcionamento

## 🎯 URLs Finais

- **Admin**: `https://seudominio.com/`
- **Cardápio**: `https://teste.seudominio.com/`
- **API**: `https://seudominio.com/api/`
