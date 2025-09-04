#!/bin/bash
# Deploy para cPanel - Comida SM

echo "🚀 Iniciando deploy para cPanel..."

# Configurações
FTP_HOST="seu-hospedeiro.com"
FTP_USER="seu_usuario"
FTP_PASS="sua_senha"
REMOTE_DIR="public_html"

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para log
log() {
    echo -e "${GREEN}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[ERRO]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[AVISO]${NC} $1"
}

# Verificar se o lftp está instalado
if ! command -v lftp &> /dev/null; then
    error "lftp não está instalado. Instale com: apt install lftp"
    exit 1
fi

# Verificar se os arquivos existem
if [ ! -f "public/index.php" ]; then
    error "Arquivo public/index.php não encontrado!"
    exit 1
fi

if [ ! -f "composer.json" ]; then
    error "Arquivo composer.json não encontrado!"
    exit 1
fi

log "Preparando arquivos para upload..."

# Criar arquivo .env de exemplo se não existir
if [ ! -f ".env" ]; then
    log "Criando arquivo .env de exemplo..."
    cat > .env << EOF
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
EOF
fi

# Instalar dependências se necessário
if [ ! -d "vendor" ]; then
    log "Instalando dependências PHP..."
    composer install --no-dev --optimize-autoloader
fi

# Criar pasta database se não existir
if [ ! -d "database" ]; then
    log "Criando pasta database..."
    mkdir -p database
fi

# Criar pasta storage se não existir
if [ ! -d "storage" ]; then
    log "Criando pasta storage..."
    mkdir -p storage
fi

log "Fazendo upload dos arquivos..."

# Upload via FTP
lftp -u "$FTP_USER,$FTP_PASS" "$FTP_HOST" << EOF
set ftp:ssl-allow no
set ftp:ssl-protect-data no
set ftp:ssl-protect-list no
cd $REMOTE_DIR

# Upload arquivos principais
put .htaccess
put .env
put composer.json
put composer.lock

# Upload pasta public
mirror -R public/ public/

# Upload pasta src
mirror -R src/ src/

# Upload pasta templates
mirror -R templates/ templates/

# Upload pasta storage
mirror -R storage/ storage/

# Upload pasta database
mirror -R database/ database/

# Upload pasta vendor
mirror -R vendor/ vendor/

# Upload README
put README.md

quit
EOF

if [ $? -eq 0 ]; then
    log "✅ Upload concluído com sucesso!"
    
    # Configurar permissões via SSH (se disponível)
    log "Configurando permissões..."
    ssh "$FTP_USER@$FTP_HOST" << 'EOF'
cd public_html
chmod 755 storage/
chmod 755 database/
chmod 644 .htaccess
chmod 644 .env
EOF
    
    log "🎉 Deploy concluído!"
    log "📱 Acesse: https://seudominio.com"
    log "🏪 Cardápio: https://teste.seudominio.com"
    
else
    error "❌ Erro no upload!"
    exit 1
fi
