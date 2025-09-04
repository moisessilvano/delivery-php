<?php

namespace App\Controllers;

class PaymentMethodController extends BaseController
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
            SELECT * FROM payment_methods 
            WHERE establishment_id = ? 
            ORDER BY sort_order, name
        ");
        $stmt->execute([$establishment['id']]);
        $paymentMethods = $stmt->fetchAll();

        $this->view('payment_methods/index', [
            'payment_methods' => $paymentMethods,
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

        $this->view('payment_methods/create', [
            'establishment' => $establishment,
            'payment_types' => $this->getPaymentTypes()
        ]);
    }

    public function edit(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        $paymentMethodId = $this->params[0] ?? null;
        if (!$paymentMethodId) {
            $this->redirect('/payment-methods');
            return;
        }

        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM payment_methods 
            WHERE id = ? AND establishment_id = ?
        ");
        $stmt->execute([$paymentMethodId, $establishment['id']]);
        $paymentMethod = $stmt->fetch();

        if (!$paymentMethod) {
            $this->redirect('/payment-methods');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleUpdate($paymentMethodId, $establishment['id']);
            return;
        }

        $this->view('payment_methods/edit', [
            'payment_method' => $paymentMethod,
            'establishment' => $establishment,
            'payment_types' => $this->getPaymentTypes()
        ]);
    }

    public function setupDefault(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        try {
            $defaultMethods = [
                ['name' => 'Dinheiro', 'type' => 'dinheiro', 'sort_order' => 1],
                ['name' => 'Cartão de Crédito', 'type' => 'cartao_credito', 'sort_order' => 2],
                ['name' => 'Cartão de Débito', 'type' => 'cartao_debito', 'sort_order' => 3],
                ['name' => 'PIX', 'type' => 'pix', 'sort_order' => 4],
                ['name' => 'Vale Refeição Alelo', 'type' => 'vale_refeicao', 'sort_order' => 5],
                ['name' => 'Vale Refeição Sodexo', 'type' => 'vale_refeicao', 'sort_order' => 6],
                ['name' => 'Vale Refeição Ticket', 'type' => 'vale_refeicao', 'sort_order' => 7],
            ];

            foreach ($defaultMethods as $method) {
                $stmt = $this->db->getPdo()->prepare("
                    INSERT INTO payment_methods (establishment_id, name, type, sort_order) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $establishment['id'], 
                    $method['name'], 
                    $method['type'], 
                    $method['sort_order']
                ]);
            }

            $this->redirect('/payment-methods?success=Formas de pagamento padrão criadas com sucesso');

        } catch (\Exception $e) {
            $this->redirect('/payment-methods?error=Erro ao criar formas de pagamento: ' . $e->getMessage());
        }
    }

    private function handleCreate(int $establishmentId): void
    {
        $name = $_POST['name'] ?? '';
        $type = $_POST['type'] ?? '';
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        if (empty($name) || empty($type)) {
            $this->view('payment_methods/create', [
                'establishment' => $this->getCurrentEstablishment(),
                'payment_types' => $this->getPaymentTypes(),
                'error' => 'Nome e tipo são obrigatórios'
            ]);
            return;
        }

        try {
            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO payment_methods (establishment_id, name, type, sort_order) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$establishmentId, $name, $type, $sortOrder]);

            $this->redirect('/payment-methods?success=Forma de pagamento criada com sucesso');

        } catch (\Exception $e) {
            $this->view('payment_methods/create', [
                'establishment' => $this->getCurrentEstablishment(),
                'payment_types' => $this->getPaymentTypes(),
                'error' => 'Erro ao criar forma de pagamento: ' . $e->getMessage()
            ]);
        }
    }

    private function handleUpdate(int $paymentMethodId, int $establishmentId): void
    {
        $name = $_POST['name'] ?? '';
        $type = $_POST['type'] ?? '';
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        if (empty($name) || empty($type)) {
            $this->view('payment_methods/edit', [
                'payment_method' => $this->getPaymentMethod($paymentMethodId, $establishmentId),
                'establishment' => $this->getCurrentEstablishment(),
                'payment_types' => $this->getPaymentTypes(),
                'error' => 'Nome e tipo são obrigatórios'
            ]);
            return;
        }

        try {
            $stmt = $this->db->getPdo()->prepare("
                UPDATE payment_methods 
                SET name = ?, type = ?, sort_order = ?, is_active = ?
                WHERE id = ? AND establishment_id = ?
            ");
            $stmt->execute([$name, $type, $sortOrder, $isActive, $paymentMethodId, $establishmentId]);

            $this->redirect('/payment-methods?success=Forma de pagamento atualizada com sucesso');

        } catch (\Exception $e) {
            $this->view('payment_methods/edit', [
                'payment_method' => $this->getPaymentMethod($paymentMethodId, $establishmentId),
                'establishment' => $this->getCurrentEstablishment(),
                'payment_types' => $this->getPaymentTypes(),
                'error' => 'Erro ao atualizar forma de pagamento: ' . $e->getMessage()
            ]);
        }
    }

    private function getPaymentMethod(int $paymentMethodId, int $establishmentId): ?array
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM payment_methods 
            WHERE id = ? AND establishment_id = ?
        ");
        $stmt->execute([$paymentMethodId, $establishmentId]);
        return $stmt->fetch();
    }

    private function getPaymentTypes(): array
    {
        return [
            'dinheiro' => 'Dinheiro',
            'cartao_credito' => 'Cartão de Crédito',
            'cartao_debito' => 'Cartão de Débito',
            'pix' => 'PIX',
            'vale_refeicao' => 'Vale Refeição',
            'vale_alimentacao' => 'Vale Alimentação'
        ];
    }

    public function delete(): void
    {
        $this->requireAuth();
        
        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        $paymentMethodId = (int)($this->params[0] ?? 0);
        if (!$paymentMethodId) {
            $this->redirect('/payment-methods?error=Forma de pagamento não encontrada');
            return;
        }

        // Check if payment method exists and belongs to establishment
        $paymentMethod = $this->getPaymentMethod($paymentMethodId, $establishment['id']);
        if (!$paymentMethod) {
            $this->redirect('/payment-methods?error=Forma de pagamento não encontrada');
            return;
        }

        try {
            $stmt = $this->db->getPdo()->prepare("
                DELETE FROM payment_methods 
                WHERE id = ? AND establishment_id = ?
            ");
            $stmt->execute([$paymentMethodId, $establishment['id']]);

            $this->redirect('/payment-methods?success=Forma de pagamento excluída com sucesso');

        } catch (\Exception $e) {
            $this->redirect('/payment-methods?error=Erro ao excluir forma de pagamento');
        }
    }
}
