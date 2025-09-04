<?php

namespace App\Controllers;

class CustomerController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM customers 
            WHERE establishment_id = ? 
            ORDER BY name
        ");
        $stmt->execute([$establishment['id']]);
        $customers = $stmt->fetchAll();

        $this->view('customers/index', [
            'customers' => $customers,
            'establishment' => $establishment
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreate($establishment['id']);
            return;
        }

        $this->view('customers/create', ['establishment' => $establishment]);
    }

    public function edit(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        $customerId = $this->params[0] ?? null;
        if (!$customerId) {
            $this->redirect('/customers');
            return;
        }

        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM customers 
            WHERE id = ? AND establishment_id = ?
        ");
        $stmt->execute([$customerId, $establishment['id']]);
        $customer = $stmt->fetch();

        if (!$customer) {
            $this->redirect('/customers');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleUpdate($customerId, $establishment['id']);
            return;
        }

        $this->view('customers/edit', [
            'customer' => $customer,
            'establishment' => $establishment
        ]);
    }

    private function handleCreate(int $establishmentId): void
    {
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';
        $cep = $_POST['cep'] ?? '';
        $address = $_POST['address'] ?? '';
        $number = $_POST['number'] ?? '';
        $complement = $_POST['complement'] ?? '';
        $neighborhood = $_POST['neighborhood'] ?? '';
        $city = $_POST['city'] ?? '';
        $state = $_POST['state'] ?? '';
        $notes = $_POST['notes'] ?? '';

        if (empty($name) || empty($phone)) {
            $this->view('customers/create', [
                'establishment' => $this->getCurrentEstablishment(),
                'error' => 'Nome e telefone são obrigatórios'
            ]);
            return;
        }

        try {
            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO customers (establishment_id, name, phone, email, cep, address, number, complement, neighborhood, city, state, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$establishmentId, $name, $phone, $email, $cep, $address, $number, $complement, $neighborhood, $city, $state, $notes]);

            $this->redirect('/customers?success=Cliente criado com sucesso');

        } catch (\Exception $e) {
            $this->view('customers/create', [
                'establishment' => $this->getCurrentEstablishment(),
                'error' => 'Erro ao criar cliente: ' . $e->getMessage()
            ]);
        }
    }

    private function handleUpdate(int $customerId, int $establishmentId): void
    {
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';
        $cep = $_POST['cep'] ?? '';
        $address = $_POST['address'] ?? '';
        $number = $_POST['number'] ?? '';
        $complement = $_POST['complement'] ?? '';
        $neighborhood = $_POST['neighborhood'] ?? '';
        $city = $_POST['city'] ?? '';
        $state = $_POST['state'] ?? '';
        $notes = $_POST['notes'] ?? '';

        if (empty($name) || empty($phone)) {
            $this->view('customers/edit', [
                'customer' => $this->getCustomer($customerId, $establishmentId),
                'establishment' => $this->getCurrentEstablishment(),
                'error' => 'Nome e telefone são obrigatórios'
            ]);
            return;
        }

        try {
            $stmt = $this->db->getPdo()->prepare("
                UPDATE customers 
                SET name = ?, phone = ?, email = ?, cep = ?, address = ?, number = ?, complement = ?, neighborhood = ?, city = ?, state = ?, notes = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND establishment_id = ?
            ");
            $stmt->execute([$name, $phone, $email, $cep, $address, $number, $complement, $neighborhood, $city, $state, $notes, $customerId, $establishmentId]);

            $this->redirect('/customers?success=Cliente atualizado com sucesso');

        } catch (\Exception $e) {
            $this->view('customers/edit', [
                'customer' => $this->getCustomer($customerId, $establishmentId),
                'establishment' => $this->getCurrentEstablishment(),
                'error' => 'Erro ao atualizar cliente: ' . $e->getMessage()
            ]);
        }
    }

    private function getCustomer(int $customerId, int $establishmentId): ?array
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM customers 
            WHERE id = ? AND establishment_id = ?
        ");
        $stmt->execute([$customerId, $establishmentId]);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }
}
