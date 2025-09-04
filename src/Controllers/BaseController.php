<?php

namespace App\Controllers;

use App\Core\Database;
use App\Core\Config;

abstract class BaseController
{
    protected Database $db;
    protected Config $config;
    protected ?string $subdomain = null;
    protected array $params = [];

    public function __construct()
    {
        $this->config = new Config();
        $this->db = new Database($this->config->get('database'));
    }

    public function setSubdomain(?string $subdomain): void
    {
        $this->subdomain = $subdomain;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    protected function view(string $template, array $data = []): void
    {
        // Extract data to variables
        extract($data);

        // Start output buffering
        ob_start();

        // Include the template
        $templatePath = __DIR__ . '/../../templates/' . $template . '.php';
        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            throw new \Exception("Template not found: {$template}");
        }

        // Get the content
        $content = ob_get_clean();

        // Include layout
        $layoutPath = __DIR__ . '/../../templates/layout.php';
        if (file_exists($layoutPath)) {
            include $layoutPath;
        } else {
            echo $content;
        }
    }

    protected function redirect(string $url): void
    {
        // Ensure session is written before redirect
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        
        // Use absolute URL for redirect
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
        $fullUrl = $protocol . '://' . $host . $url;
        
        error_log("Redirecting to: " . $fullUrl);
        header("Location: {$fullUrl}");
        exit;
    }

    protected function requireAuth(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        error_log("requireAuth - Session data: " . print_r($_SESSION, true));
        
        if (!isset($_SESSION['user_id'])) {
            error_log("requireAuth - User not authenticated, redirecting to login");
            $this->redirect('/login');
        }
        
        error_log("requireAuth - User is authenticated");
    }

    protected function getCurrentEstablishment(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        error_log("getCurrentEstablishment - Session data: " . print_r($_SESSION, true));
        
        if (!isset($_SESSION['establishment_id'])) {
            error_log("getCurrentEstablishment - No establishment_id in session");
            return null;
        }

        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM establishments 
            WHERE id = ? AND is_active = 1
        ");
        $stmt->execute([$_SESSION['establishment_id']]);
        $result = $stmt->fetch();
        
        error_log("getCurrentEstablishment - Result: " . print_r($result, true));
        return $result ?: null;
    }

    protected function uploadFile(array $file, string $folder): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Validate file size
        $maxSize = $this->config->get('upload.max_size', 5 * 1024 * 1024);
        if ($file['size'] > $maxSize) {
            throw new \Exception('Arquivo muito grande. Tamanho máximo: ' . ($maxSize / 1024 / 1024) . 'MB');
        }

        // Validate file type
        $allowedTypes = $this->config->get('upload.allowed_types', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedTypes)) {
            throw new \Exception('Tipo de arquivo não permitido. Tipos aceitos: ' . implode(', ', $allowedTypes));
        }

        // Create upload directory if it doesn't exist
        $uploadPath = $this->config->get('upload.path') . $folder . '/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate unique filename
        $filename = uniqid() . '.' . $extension;
        $filepath = $uploadPath . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return 'storage/' . $folder . '/' . $filename;
        }

        throw new \Exception('Erro ao fazer upload do arquivo');
    }

    protected function getCurrentUser(): ?array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        error_log("getCurrentUser - Session data: " . print_r($_SESSION, true));
        
        if (!isset($_SESSION['user_id'])) {
            error_log("getCurrentUser - No user_id in session");
            return null;
        }

        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM users 
            WHERE id = ? AND is_active = 1
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch();
        
        error_log("getCurrentUser - Result: " . print_r($result, true));
        return $result;
    }

    protected function isAdmin(): bool
    {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === 'admin';
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function validateCsrf(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        error_log("validateCsrf - Session data: " . print_r($_SESSION, true));
        
        $token = $_POST['csrf_token'] ?? '';
        $result = isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
        
        error_log("validateCsrf - Result: " . ($result ? 'true' : 'false'));
        return $result;
    }

    protected function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        error_log("generateCsrfToken - Session data: " . print_r($_SESSION, true));
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        
        error_log("generateCsrfToken - Generated token: " . $token);
        return $token;
    }

    protected function getEstablishmentBySubdomain(string $subdomain): ?array
    {
        error_log("getEstablishmentBySubdomain - Subdomain: " . $subdomain);
        
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM establishments 
            WHERE subdomain = ? AND is_active = 1
        ");
        $stmt->execute([$subdomain]);
        $result = $stmt->fetch();
        
        error_log("getEstablishmentBySubdomain - Result: " . print_r($result, true));
        return $result;
    }
}
