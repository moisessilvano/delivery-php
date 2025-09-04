<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Debug session data
        error_log("Session data in index: " . print_r($_SESSION, true));

        // Check if user is already logged in
        if (isset($_SESSION['user_id'])) {
            error_log("User is logged in, redirecting to dashboard");
            $this->redirect('/dashboard');
            return;
        }

        error_log("User not logged in, showing login page");
        // If not logged in, show login page
        $this->view('auth/login');
    }

    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
            return;
        }

        $this->view('auth/login');
    }

    private function handleLogin(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->view('auth/login', ['error' => 'Email e senha são obrigatórios']);
            return;
        }

        $stmt = $this->db->getPdo()->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $this->view('auth/login', ['error' => 'Credenciais inválidas']);
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['establishment_id'] = $user['establishment_id'];

        // Debug session data
        error_log("Session data after login: " . print_r($_SESSION, true));

        // Force session write
        session_write_close();
        
        error_log("Redirecting to dashboard");
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_destroy();
        $this->redirect('/login');
    }

    public function register(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
            return;
        }

        $this->view('auth/register');
    }

    private function handleRegister(): void
    {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $subdomain = $_POST['subdomain'] ?? '';
        $establishment_name = $_POST['establishment_name'] ?? '';

        if (empty($name) || empty($email) || empty($password) || empty($subdomain) || empty($establishment_name)) {
            $this->view('auth/register', ['error' => 'Todos os campos são obrigatórios']);
            return;
        }

        // Check if subdomain is already taken
        $stmt = $this->db->getPdo()->prepare("SELECT id FROM establishments WHERE subdomain = ?");
        $stmt->execute([$subdomain]);
        if ($stmt->fetch()) {
            $this->view('auth/register', ['error' => 'Este subdomínio já está em uso']);
            return;
        }

        // Check if email is already taken
        $stmt = $this->db->getPdo()->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $this->view('auth/register', ['error' => 'Este email já está em uso']);
            return;
        }

        try {
            $this->db->getPdo()->beginTransaction();

            // Create establishment
            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO establishments (subdomain, name, is_active) 
                VALUES (?, ?, 1)
            ");
            $stmt->execute([$subdomain, $establishment_name]);
            $establishmentId = $this->db->getPdo()->lastInsertId();

            // Create default business hours
            $days = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
            for ($i = 0; $i < 7; $i++) {
                $stmt = $this->db->getPdo()->prepare("
                    INSERT INTO business_hours (establishment_id, day_of_week, open_time, close_time, is_closed) 
                    VALUES (?, ?, '08:00', '18:00', 0)
                ");
                $stmt->execute([$establishmentId, $i]);
            }

            // Create user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO users (establishment_id, name, email, password, role) 
                VALUES (?, ?, ?, ?, 'admin')
            ");
            $stmt->execute([$establishmentId, $name, $email, $hashedPassword]);

            // Create default payment methods
            $defaultPaymentMethods = [
                ['name' => 'Dinheiro', 'type' => 'dinheiro', 'sort_order' => 1],
                ['name' => 'Cartão de Crédito', 'type' => 'cartao_credito', 'sort_order' => 2],
                ['name' => 'Cartão de Débito', 'type' => 'cartao_debito', 'sort_order' => 3],
                ['name' => 'PIX', 'type' => 'pix', 'sort_order' => 4],
            ];

            foreach ($defaultPaymentMethods as $method) {
                $stmt = $this->db->getPdo()->prepare("
                    INSERT INTO payment_methods (establishment_id, name, type, sort_order) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $establishmentId, 
                    $method['name'], 
                    $method['type'], 
                    $method['sort_order']
                ]);
            }

            $this->db->getPdo()->commit();

            // Auto login
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['user_id'] = $this->db->getPdo()->lastInsertId();
            $_SESSION['user_name'] = $name;
            $_SESSION['establishment_id'] = $establishmentId;

            // Force session write
            session_write_close();
            
            $this->redirect('/dashboard');

        } catch (\Exception $e) {
            $this->db->getPdo()->rollBack();
            $this->view('auth/register', ['error' => 'Erro ao criar conta: ' . $e->getMessage()]);
        }
    }
}

