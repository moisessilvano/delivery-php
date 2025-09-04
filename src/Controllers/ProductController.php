<?php

namespace App\Controllers;

class ProductController extends BaseController
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
            SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.establishment_id = ? 
            ORDER BY c.sort_order, c.name, p.sort_order, p.name
        ");
        $stmt->execute([$establishment['id']]);
        $products = $stmt->fetchAll();

        $this->view('products/index', [
            'products' => $products,
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

        // Get categories for dropdown
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM categories 
            WHERE establishment_id = ? AND is_active = 1 
            ORDER BY sort_order, name
        ");
        $stmt->execute([$establishment['id']]);
        $categories = $stmt->fetchAll();

        $this->view('products/create', [
            'categories' => $categories,
            'establishment' => $establishment
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

        $productId = $this->params[0] ?? null;
        if (!$productId) {
            $this->redirect('/products');
            return;
        }

        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM products 
            WHERE id = ? AND establishment_id = ?
        ");
        $stmt->execute([$productId, $establishment['id']]);
        $product = $stmt->fetch();

        if (!$product) {
            $this->redirect('/products');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleUpdate($productId, $establishment['id']);
            return;
        }

        // Get categories for dropdown
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM categories 
            WHERE establishment_id = ? AND is_active = 1 
            ORDER BY sort_order, name
        ");
        $stmt->execute([$establishment['id']]);
        $categories = $stmt->fetchAll();

        // Get product option groups
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM product_option_groups 
            WHERE product_id = ? 
            ORDER BY sort_order, name
        ");
        $stmt->execute([$productId]);
        $optionGroups = $stmt->fetchAll();

        // Get options for each group
        foreach ($optionGroups as &$group) {
            $stmt = $this->db->getPdo()->prepare("
                SELECT * FROM product_options 
                WHERE option_group_id = ? AND is_available = 1
                ORDER BY sort_order, name
            ");
            $stmt->execute([$group['id']]);
            $group['options'] = $stmt->fetchAll();
        }

        $this->view('products/edit', [
            'product' => $product,
            'categories' => $categories,
            'optionGroups' => $optionGroups,
            'establishment' => $establishment
        ]);
    }

    private function handleCreate(int $establishmentId): void
    {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $priceRaw = $_POST['price'] ?? '';
        // Remove máscara monetária e converte para float
        $price = (float)str_replace(['R$', ' ', '.', ','], ['', '', '', '.'], $priceRaw);
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isAvailable = isset($_POST['is_available']) ? 1 : 0;

        if (empty($name) || $price <= 0 || $categoryId <= 0) {
            $this->view('products/create', [
                'categories' => $this->getCategories($establishmentId),
                'establishment' => $this->getCurrentEstablishment(),
                'error' => 'Nome, preço e categoria são obrigatórios'
            ]);
            return;
        }

        try {
            $this->db->getPdo()->beginTransaction();

            // Handle image upload
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = $this->uploadFile($_FILES['image'], 'products');
            }

            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO products (establishment_id, category_id, name, description, price, image, is_available, sort_order) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$establishmentId, $categoryId, $name, $description, $price, $image, $isAvailable, $sortOrder]);
            $productId = $this->db->getPdo()->lastInsertId();

            // Handle product options
            $this->handleProductOptions($productId);

            $this->db->getPdo()->commit();
            $this->redirect('/products?success=Produto criado com sucesso');

        } catch (\Exception $e) {
            $this->db->getPdo()->rollBack();
            $this->view('products/create', [
                'categories' => $this->getCategories($establishmentId),
                'establishment' => $this->getCurrentEstablishment(),
                'error' => 'Erro ao criar produto: ' . $e->getMessage()
            ]);
        }
    }

    private function handleUpdate(int $productId, int $establishmentId): void
    {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $priceRaw = $_POST['price'] ?? '';
        // Remove máscara monetária e converte para float
        $price = (float)str_replace(['R$', ' ', '.', ','], ['', '', '', '.'], $priceRaw);
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isAvailable = isset($_POST['is_available']) ? 1 : 0;

        if (empty($name) || $price <= 0 || $categoryId <= 0) {
            $this->view('products/edit', [
                'product' => $this->getProduct($productId, $establishmentId),
                'categories' => $this->getCategories($establishmentId),
                'establishment' => $this->getCurrentEstablishment(),
                'error' => 'Nome, preço e categoria são obrigatórios'
            ]);
            return;
        }

        try {
            $this->db->getPdo()->beginTransaction();

            $sql = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, is_available = ?, sort_order = ?";
            $params = [$name, $description, $price, $categoryId, $isAvailable, $sortOrder];

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = $this->uploadFile($_FILES['image'], 'products');
                if ($image) {
                    $sql .= ", image = ?";
                    $params[] = $image;
                }
            }

            $sql .= " WHERE id = ? AND establishment_id = ?";
            $params[] = $productId;
            $params[] = $establishmentId;

            $stmt = $this->db->getPdo()->prepare($sql);
            $stmt->execute($params);

            // Handle product options
            $this->handleProductOptions($productId);

            $this->db->getPdo()->commit();
            $this->redirect('/products?success=Produto atualizado com sucesso');

        } catch (\Exception $e) {
            $this->db->getPdo()->rollBack();
            $this->view('products/edit', [
                'product' => $this->getProduct($productId, $establishmentId),
                'categories' => $this->getCategories($establishmentId),
                'establishment' => $this->getCurrentEstablishment(),
                'error' => 'Erro ao atualizar produto: ' . $e->getMessage()
            ]);
        }
    }

    private function handleProductOptions(int $productId): void
    {
        // Delete existing option groups and options (cascade will handle options)
        $stmt = $this->db->getPdo()->prepare("DELETE FROM product_option_groups WHERE product_id = ?");
        $stmt->execute([$productId]);

        // Add new customization groups
        if (isset($_POST['customization_groups']) && is_array($_POST['customization_groups'])) {
            foreach ($_POST['customization_groups'] as $groupIndex => $groupData) {
                if (!empty($groupData['name'])) {
                    // Insert group
                    $stmt = $this->db->getPdo()->prepare("
                        INSERT INTO product_option_groups (product_id, name, is_required, allow_multiple, sort_order) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $productId,
                        $groupData['name'],
                        isset($groupData['is_required']) ? 1 : 0,
                        isset($groupData['allow_multiple']) ? 1 : 0,
                        (int)($groupData['sort_order'] ?? 0)
                    ]);
                    $groupId = $this->db->getPdo()->lastInsertId();

                    // Insert options for this group
                    if (isset($groupData['options']) && is_array($groupData['options'])) {
                        foreach ($groupData['options'] as $optionIndex => $option) {
                            if (!empty($option['name'])) {
                                $optionImage = null;
                                
                                // Handle option image upload
                                if (isset($_FILES['customization_groups']['tmp_name']['options'][$groupIndex][$optionIndex]['image']) && 
                                    $_FILES['customization_groups']['error']['options'][$groupIndex][$optionIndex]['image'] === UPLOAD_ERR_OK) {
                                    
                                    $fileInfo = [
                                        'name' => $_FILES['customization_groups']['name']['options'][$groupIndex][$optionIndex]['image'],
                                        'type' => $_FILES['customization_groups']['type']['options'][$groupIndex][$optionIndex]['image'],
                                        'tmp_name' => $_FILES['customization_groups']['tmp_name']['options'][$groupIndex][$optionIndex]['image'],
                                        'error' => $_FILES['customization_groups']['error']['options'][$groupIndex][$optionIndex]['image'],
                                        'size' => $_FILES['customization_groups']['size']['options'][$groupIndex][$optionIndex]['image']
                                    ];
                                    $optionImage = $this->uploadFile($fileInfo, 'options');
                                }

                                $stmt = $this->db->getPdo()->prepare("
                                    INSERT INTO product_options (option_group_id, name, price, image, sort_order) 
                                    VALUES (?, ?, ?, ?, ?)
                                ");
                                $stmt->execute([
                                    $groupId,
                                    $option['name'],
                                    (float)($option['price'] ?? 0),
                                    $optionImage,
                                    (int)($option['sort_order'] ?? $optionIndex)
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }

    private function getCategories(int $establishmentId): array
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM categories 
            WHERE establishment_id = ? AND is_active = 1 
            ORDER BY sort_order, name
        ");
        $stmt->execute([$establishmentId]);
        return $stmt->fetchAll();
    }

    private function getProduct(int $productId, int $establishmentId): ?array
    {
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM products 
            WHERE id = ? AND establishment_id = ?
        ");
        $stmt->execute([$productId, $establishmentId]);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }
}

