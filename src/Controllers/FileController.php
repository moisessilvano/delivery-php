<?php

namespace App\Controllers;

class FileController extends BaseController
{
    public function serve(): void
    {
        $path = $this->params[0] ?? '';
        
        if (empty($path)) {
            http_response_code(404);
            exit;
        }
        
        // Sanitize path to prevent directory traversal
        $path = str_replace(['../', '..\\', './'], '', $path);
        
        // Build full file path
        $storagePath = __DIR__ . '/../../storage/';
        $filePath = $storagePath . $path;
        
        // Check if file exists and is within storage directory
        if (!file_exists($filePath) || !is_file($filePath)) {
            http_response_code(404);
            exit;
        }
        
        // Verify file is within storage directory (additional security)
        $realPath = realpath($filePath);
        $realStoragePath = realpath($storagePath);
        
        if (!$realPath || !$realStoragePath || strpos($realPath, $realStoragePath) !== 0) {
            http_response_code(403);
            exit;
        }
        
        // Get file info
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        
        if (!in_array($extension, $allowedExtensions)) {
            http_response_code(403);
            exit;
        }
        
        // Set appropriate content type
        $contentTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml'
        ];
        
        $contentType = $contentTypes[$extension] ?? 'application/octet-stream';
        
        // Set headers
        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: public, max-age=31536000'); // 1 year cache
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        
        // Add security headers
        header('X-Content-Type-Options: nosniff');
        
        // Output file
        readfile($filePath);
        exit;
    }
}
?>