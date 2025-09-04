# 🚀 Deploy em Produção - Comida SM

## 📋 Pré-requisitos

- **Hospedagem com PHP 8.2+**
- **MySQL 5.7+ ou MariaDB 10.3+**
- **mod_rewrite habilitado**
- **Acesso SSH ou cPanel**

## 🔧 Passos para Deploy

### 1. Upload dos Arquivos

```bash
# Via SSH ou cPanel File Manager
# Upload todos os arquivos para a pasta public_html
```

### 2. Configurar Permissões

```bash
# Definir permissões corretas
chmod 755 public/
chmod 755 storage/
chmod 644 .htaccess
chmod 644 .env
```

### 3. Instalar Dependências (Opcional)

```bash
# Se tiver acesso SSH e Composer
composer install --no-dev --optimize-autoloader

# OU usar o autoloader simples (já incluído)
# Não precisa fazer nada - o sistema detecta automaticamente
```

### 4. Configurar Banco de Dados

1. **Criar banco MySQL:**
   ```sql
   CREATE DATABASE comida_sm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Criar usuário (se necessário):**
   ```sql
   CREATE USER 'comida_user'@'localhost' IDENTIFIED BY 'senha_forte';
   GRANT ALL PRIVILEGES ON comida_sm.* TO 'comida_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

### 5. Configurar .env

```env
# Application
APP_NAME="Comida SM"
APP_URL="https://seudominio.com"
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE="America/Sao_Paulo"

# Database - MySQL Configuration
DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=comida_sm
DB_USERNAME=comida_user
DB_PASSWORD=senha_forte
DB_PREFIX=

# Upload
UPLOAD_PATH=storage/
UPLOAD_MAX_SIZE=5242880
UPLOAD_ALLOWED_TYPES=jpg,jpeg,png,gif,webp

# Security
APP_KEY=sua-chave-secreta-aqui-32-caracteres
```

### 6. Executar Instalação

1. **Acesse:** `https://seudominio.com/install.php`
2. **Configure:** Banco de dados e usuário admin
3. **Instale:** O sistema criará as tabelas automaticamente

### 7. Verificar Instalação

- ✅ Acesse `https://seudominio.com/`
- ✅ Faça login com as credenciais criadas
- ✅ Verifique se o instalador foi removido automaticamente

## 🛡️ Segurança em Produção

### Arquivos a Remover/Proteger

```bash
# Remover arquivos de desenvolvimento
rm install.php
rm autoload.php
rm DEPLOY_PRODUCAO.md
rm *.md
rm database/schema.sql
```

### Configurações de Segurança

1. **Alterar senhas padrão**
2. **Configurar HTTPS**
3. **Definir permissões corretas**
4. **Configurar backup automático**

## 🔍 Solução de Problemas

### Erro: "Class not found"
- ✅ **Solução:** O autoloader simples já está incluído
- ✅ **Verificar:** Se todos os arquivos foram enviados

### Erro: "Database connection failed"
- ✅ **Verificar:** Configurações do .env
- ✅ **Testar:** Conexão com banco via phpMyAdmin

### CSS não carrega
- ✅ **Verificar:** Se mod_rewrite está habilitado
- ✅ **Testar:** Acessar CSS diretamente

### Página em branco
- ✅ **Verificar:** Logs de erro do PHP
- ✅ **Testar:** Com APP_DEBUG=true temporariamente

## 📞 Suporte

Se encontrar problemas:
1. Verifique os logs de erro
2. Teste com APP_DEBUG=true
3. Confirme todas as configurações

---

**Comida SM** - Sistema de Delivery para Estabelecimentos
