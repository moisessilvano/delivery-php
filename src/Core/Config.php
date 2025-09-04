<?php

namespace App\Core;

class Config
{
    private array $config;

    public function __construct()
    {
        // Load environment variables from .env file if it exists
        $this->loadEnv();
        
        $this->config = [
            'database' => [
                'driver' => $this->env('DB_DRIVER', 'sqlite'),
                'host' => $this->env('DB_HOST', 'localhost'),
                'port' => $this->env('DB_PORT', '5432'),
                'database' => $this->getDatabaseName(),
                'username' => $this->env('DB_USERNAME', ''),
                'password' => $this->env('DB_PASSWORD', ''),
                'prefix' => $this->env('DB_PREFIX', ''),
            ],
            'app' => [
                'name' => $this->env('APP_NAME', 'Comida SM'),
                'url' => $this->env('APP_URL', 'http://localhost:8000'),
                'env' => $this->env('APP_ENV', 'development'),
                'debug' => $this->env('APP_DEBUG', 'true') === 'true',
                'timezone' => $this->env('APP_TIMEZONE', 'America/Sao_Paulo'),
            ],
            'upload' => [
                'path' => $this->env('UPLOAD_PATH', __DIR__ . '/../../storage/'),
                'max_size' => (int)$this->env('UPLOAD_MAX_SIZE', 5 * 1024 * 1024), // 5MB
                'allowed_types' => explode(',', $this->env('UPLOAD_ALLOWED_TYPES', 'jpg,jpeg,png,gif,webp')),
            ]
        ];
    }

    private function loadEnv(): void
    {
        $envFile = __DIR__ . '/../../.env';
        
        if (!file_exists($envFile)) {
            return;
        }
        
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue; // Skip comments
            }
            
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
                
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
    
    private function env(string $key, $default = null)
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }

    private function getDatabaseName(): string
    {
        $driver = $this->env('DB_DRIVER', 'sqlite');
        $database = $this->env('DB_DATABASE', '');
        
        if ($driver === 'sqlite') {
            return $this->resolveDatabasePath($database ?: __DIR__ . '/../../database/app.sqlite');
        }
        
        // For MySQL and other databases, return the database name as-is
        return $database ?: 'comida_sm';
    }

    private function resolveDatabasePath(string $path): string
    {
        // If it's already an absolute path, return as-is
        if (strpos($path, '/') === 0 || preg_match('/^[A-Za-z]:/', $path)) {
            return $path;
        }
        
        // If it's a relative path, resolve it relative to the project root
        return __DIR__ . '/../../' . ltrim($path, './');
    }

    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public function set(string $key, $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }
}

