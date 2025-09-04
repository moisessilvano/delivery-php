# 🚀 Deploy Rápido no cPanel

## 📋 Passos Simples

### 1. Preparar Arquivos
```bash
# No seu computador
composer install --no-dev --optimize-autoloader
```

### 2. Upload para cPanel
1. **Acesse** o File Manager no cPanel
2. **Navegue** para `public_html`
3. **Faça upload** de todos os arquivos:
   - `public/` (pasta completa)
   - `src/` (pasta completa)
   - `templates/` (pasta completa)
   - `storage/` (pasta completa)
   - `database/` (pasta completa)
   - `vendor/` (pasta completa)
   - `.htaccess`
   - `composer.json`
   - `composer.lock`

### 3. Configurar Permissões
```bash
# Via File Manager
storage/ → 755
database/ → 755
.htaccess → 644
```

### 4. Criar Arquivo .env
Crie um arquivo `.env` na raiz do `public_html`:

```env
DB_DRIVER=sqlite
DB_DATABASE=database/app.sqlite
APP_URL=https://seudominio.com
APP_ENV=production
APP_DEBUG=false
```

### 5. Configurar Subdomínios
1. **Crie subdomínios** no cPanel
2. **Aponte** para a mesma pasta `public_html`

## ✅ URLs Finais
- **Admin**: `https://seudominio.com/`
- **Cardápio**: `https://teste.seudominio.com/`

## 🔧 Troubleshooting
- **Erro 500**: Verifique permissões
- **CSS não carrega**: Verifique se `.htaccess` está correto
- **Banco não funciona**: Verifique permissões da pasta `database/`
