<?php

namespace App\Controllers;

class CategoryController extends BaseController
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
            SELECT c.*, COUNT(p.id) as product_count 
            FROM categories c 
            LEFT JOIN products p ON c.id = p.category_id 
            WHERE c.establishment_id = ? 
            GROUP BY c.id 
            ORDER BY c.sort_order, c.name
        ");
        $stmt->execute([$establishment['id']]);
        $categories = $stmt->fetchAll();

        $this->view('categories/index', [
            'categories' => $categories,
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

        $this->view('categories/create', ['establishment' => $establishment]);
    }

    public function edit(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        $categoryId = $this->params[0] ?? null;
        if (!$categoryId) {
            $this->redirect('/categories');
            return;
        }

        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM categories 
            WHERE id = ? AND establishment_id = ?
        ");
        $stmt->execute([$categoryId, $establishment['id']]);
        $category = $stmt->fetch();

        if (!$category) {
            $this->redirect('/categories');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleUpdate($categoryId, $establishment['id']);
            return;
        }

        $this->view('categories/edit', [
            'category' => $category,
            'establishment' => $establishment
        ]);
    }

    private function handleCreate(int $establishmentId): void
    {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        if (empty($name)) {
            $this->view('categories/create', [
                'establishment' => $this->getCurrentEstablishment(),
                'error' => 'Nome da categoria é obrigatório'
            ]);
            return;
        }

        try {
            // Handle image upload
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = $this->uploadFile($_FILES['image'], 'categories');
            }

            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO categories (establishment_id, name, description, image, sort_order) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$establishmentId, $name, $description, $image, $sortOrder]);

            $this->redirect('/categories?success=Categoria criada com sucesso');

        } catch (\Exception $e) {
            $this->view('categories/create', [
                'establishment' => $this->getCurrentEstablishment(),
                'error' => 'Erro ao criar categoria: ' . $e->getMessage()
            ]);
        }
    }

    private function handleUpdate(int $categoryId, int $establishmentId): void
    {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        if (empty($name)) {
            $this->view('categories/edit', [
                'category' => $this->getCategory($categoryId, $establishmentId),
                'establishment' => $this->getCurrentEstablishment(),
                'error' => 'Nome da categoria é obrigatório'
            ]);
            return;
        }

        try {
            $sql = "UPDATE categories SET name = ?, description = ?, sort_order = ?";
            $params = [$name, $description, $sortOrder];

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = $this->uploadFile($_FILES['image'], 'categories');
                if ($image) {
                    $sql .= ", image = ?";
                    $params[] = $image;
                }
            }

            $sql .= " WHERE id = ? AND establishment_id = ?";
            $params[] = $categoryId;
            $params[] = $establishmentId;

            $stmt = $this->db->getPdo()->prepare($sql);
            $stmt->execute($params);

            $this->redirect('/categories?success=Categoria atualizada com sucesso');

        } catch (\Exception $e) {
            $this->view('categories/edit', [
                'category' => $this->getCategory($categoryId, $establishmentId),
                'establishment' => $this->getCurrentEstablishment(),
                'error' => 'Erro ao atualizar categoria: ' . $e->getMessage()
            ]);
        }
    }

    public function updateOrder(): void
    {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return;
        }

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            http_response_code(403);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $order = $input['order'] ?? [];

        try {
            foreach ($order as $item) {
                $stmt = $this->db->getPdo()->prepare("
                    UPDATE categories 
                    SET sort_order = ? 
                    WHERE id = ? AND establishment_id = ?
                ");
                $stmt->execute([$item['sort_order'], $item['id'], $establishment['id']]);
            }
            
            http_response_code(200);
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function getCategory(int $categoryId, int $establishmentId): ?array
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM categories 
            WHERE id = ? AND establishment_id = ?
        ");
        $stmt->execute([$categoryId, $establishmentId]);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }
}

