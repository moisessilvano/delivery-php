<?php
/**
 * Servidor de desenvolvimento para Comida SM
 * Use: php server.php
 */

$host = 'localhost';
$port = 8000;

echo "🚀 Iniciando servidor de desenvolvimento...\n";
echo "📱 Acesse: http://{$host}:{$port}\n";
echo "🏪 Teste com subdomínio: http://teste.{$host}:{$port}\n";
echo "⏹️  Para parar: Ctrl+C\n\n";

// Start the built-in PHP server
passthru("php -S {$host}:{$port} -t public");
