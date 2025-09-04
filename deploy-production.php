<?php
/**
 * Script de Deploy para Produção
 * Remove arquivos desnecessários e otimiza para produção
 */

echo "🚀 Iniciando deploy para produção...\n\n";

// Arquivos e pastas para remover em produção
$items_to_remove = [
    'install.php' => 'Instalador (já executado)',
    'autoload.php' => 'Autoloader simples (não necessário com Composer)',
    'DEPLOY_PRODUCAO.md' => 'Instruções de deploy',
    'INSTALACAO.md' => 'Instruções de instalação',
    'database/schema.sql' => 'Schema SQL (já executado)',
    'database/installed.flag' => 'Flag de instalação',
    'composer.json' => 'Configuração do Composer (opcional)',
    'composer.lock' => 'Lock do Composer (opcional)',
    'package.json' => 'Configuração do NPM (opcional)',
    'package-lock.json' => 'Lock do NPM (opcional)',
    'tailwind.config.js' => 'Configuração do Tailwind (opcional)',
    'server.php' => 'Servidor de desenvolvimento'
];

$removed_count = 0;
$protected_count = 0;

foreach ($items_to_remove as $item => $description) {
    if (file_exists($item)) {
        if (is_dir($item)) {
            if (rmdir($item)) {
                echo "✅ Removido diretório: $item ($description)\n";
                $removed_count++;
            } else {
                echo "❌ Erro ao remover diretório: $item\n";
            }
        } else {
            if (unlink($item)) {
                echo "✅ Removido arquivo: $item ($description)\n";
                $removed_count++;
            } else {
                echo "❌ Erro ao remover arquivo: $item\n";
            }
        }
    } else {
        echo "ℹ️  Não encontrado: $item\n";
    }
}

// Otimizar .htaccess para produção
if (file_exists('.htaccess')) {
    $htaccess_content = file_get_contents('.htaccess');
    
    // Adicionar regras de segurança se não existirem
    $security_rules = "\n# Security rules for production\n";
    $security_rules .= "<FilesMatch \"\\.(env|log|sql|md|txt|php)$\">\n";
    $security_rules .= "    Require all denied\n";
    $security_rules .= "</FilesMatch>\n";
    $security_rules .= "\n# Block access to sensitive directories\n";
    $security_rules .= "RewriteRule ^(vendor|src|storage|database|templates)/ - [F,L]\n";
    
    if (strpos($htaccess_content, 'Security rules for production') === false) {
        file_put_contents('.htaccess', $htaccess_content . $security_rules);
        echo "✅ Regras de segurança adicionadas ao .htaccess\n";
        $protected_count++;
    } else {
        echo "ℹ️  .htaccess já possui regras de segurança\n";
    }
}

// Verificar se .env está configurado para produção
if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    
    if (strpos($env_content, 'APP_ENV=production') === false) {
        echo "⚠️  AVISO: Configure APP_ENV=production no arquivo .env\n";
    }
    
    if (strpos($env_content, 'APP_DEBUG=false') === false) {
        echo "⚠️  AVISO: Configure APP_DEBUG=false no arquivo .env\n";
    }
    
    if (strpos($env_content, 'APP_KEY=your-secret-key') !== false) {
        echo "⚠️  AVISO: Altere a APP_KEY no arquivo .env para uma chave única\n";
    }
}

echo "\n📊 Resumo do deploy:\n";
echo "   - Itens removidos: $removed_count\n";
echo "   - Itens protegidos: $protected_count\n";

if ($removed_count > 0) {
    echo "\n✅ Deploy para produção concluído!\n";
    echo "⚠️  IMPORTANTE: Remova este arquivo (deploy-production.php) também!\n";
    echo "\n🔒 Sua aplicação está otimizada para produção!\n";
} else {
    echo "\nℹ️  Nenhum item encontrado para remover.\n";
}

echo "\n📋 Próximos passos:\n";
echo "   1. Configure APP_ENV=production no .env\n";
echo "   2. Configure APP_DEBUG=false no .env\n";
echo "   3. Altere a APP_KEY para uma chave única\n";
echo "   4. Configure HTTPS se disponível\n";
echo "   5. Configure backup automático\n";
?>
