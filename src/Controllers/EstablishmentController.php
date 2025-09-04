<?php

namespace App\Controllers;

use App\Services\GeocodingService;

class EstablishmentController extends BaseController
{
    public function profile(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        // Get business hours
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM business_hours 
            WHERE establishment_id = ? 
            ORDER BY day_of_week
        ");
        $stmt->execute([$establishment['id']]);
        $businessHours = $stmt->fetchAll();

        $this->view('establishment/profile', [
            'establishment' => $establishment,
            'business_hours' => $businessHours
        ]);
    }

    public function edit(): void
    {
        // Redirect to the first page of the multi-page form
        $this->redirect('/profile/edit/info');
    }

    public function editInfo(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleInfoUpdate($establishment['id']);
            return;
        }

        $this->view('establishment/edit/info', [
            'establishment' => $establishment
        ]);
    }

    public function editContact(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleContactUpdate($establishment['id']);
            return;
        }

        $this->view('establishment/edit/contact', [
            'establishment' => $establishment
        ]);
    }

    public function editDelivery(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleDeliveryUpdate($establishment['id']);
            return;
        }

        // Get delivery zones
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM delivery_zones 
            WHERE establishment_id = ? 
            ORDER BY sort_order, radius_km
        ");
        $stmt->execute([$establishment['id']]);
        $deliveryZones = $stmt->fetchAll();

        $this->view('establishment/edit/delivery', [
            'establishment' => $establishment,
            'delivery_zones' => $deliveryZones
        ]);
    }

    public function editHours(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleHoursUpdate($establishment['id']);
            return;
        }

        // Get business hours
        $stmt = $this->db->getPdo()->prepare("
            SELECT * FROM business_hours 
            WHERE establishment_id = ? 
            ORDER BY day_of_week
        ");
        $stmt->execute([$establishment['id']]);
        $businessHours = $stmt->fetchAll();

        $this->view('establishment/edit/hours', [
            'establishment' => $establishment,
            'business_hours' => $businessHours
        ]);
    }

    public function editDesign(): void
    {
        $this->requireAuth();

        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->redirect('/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleDesignUpdate($establishment['id']);
            return;
        }

        $this->view('establishment/edit/design', [
            'establishment' => $establishment
        ]);
    }

    private function handleUpdate(int $establishmentId): void
    {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $isWhatsapp = isset($_POST['is_whatsapp']) ? 1 : 0;
        $category = $_POST['category'] ?? '';
        $instagram = $_POST['instagram'] ?? '';
        $facebook = $_POST['facebook'] ?? '';
        $deliveryTime = (int)($_POST['delivery_time'] ?? 30);
        $deliveryFee = (float)($_POST['delivery_fee'] ?? 0);
        $minOrderValue = (float)($_POST['min_order_value'] ?? 0);
        
        // New detailed address fields
        $cep = $_POST['cep'] ?? '';
        $streetAddress = $_POST['street_address'] ?? '';
        $number = $_POST['number'] ?? '';
        $complement = $_POST['complement'] ?? '';
        $neighborhood = $_POST['neighborhood'] ?? '';
        $city = $_POST['city'] ?? '';
        $state = $_POST['state'] ?? '';
        
        // Build full address for geocoding (backward compatibility)
        $fullAddress = '';
        if (!empty($streetAddress)) {
            $fullAddress = $streetAddress;
            if (!empty($number)) {
                $fullAddress .= ', ' . $number;
            }
            if (!empty($neighborhood)) {
                $fullAddress .= ', ' . $neighborhood;
            }
            if (!empty($city)) {
                $fullAddress .= ', ' . $city;
            }
            if (!empty($state)) {
                $fullAddress .= ', ' . $state;
            }
        }

        // Get current establishment data
        $stmt = $this->db->getPdo()->prepare("SELECT * FROM establishments WHERE id = ?");
        $stmt->execute([$establishmentId]);
        $establishment = $stmt->fetch();

        if (!$establishment) {
            $this->redirect('/profile?error=Estabelecimento não encontrado');
            return;
        }

        try {
            $this->db->getPdo()->beginTransaction();

            // Geocode address if it changed
            $latitude = $establishment['latitude'];
            $longitude = $establishment['longitude'];
            
            // Compare the full address with the old address for geocoding
            $oldFullAddress = $establishment['address'] ?? '';
            if ($fullAddress !== $oldFullAddress && !empty($fullAddress)) {
                $geocodingService = new GeocodingService();
                $geocodeResult = $geocodingService->geocodeAddress($fullAddress);
                
                if ($geocodeResult) {
                    $latitude = $geocodeResult['lat'];
                    $longitude = $geocodeResult['lng'];
                }
            }

            // Handle logo upload
            $logo = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $logo = $this->uploadFile($_FILES['logo'], 'logos');
            }

            // Handle photo upload
            $photo = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $photo = $this->uploadFile($_FILES['photo'], 'general');
            }

            // Update establishment
            // Get color values
            $primaryColor = $_POST['primary_color'] ?? '#3B82F6';
            $secondaryColor = $_POST['secondary_color'] ?? '#1E40AF';
            $backgroundColor = $_POST['background_color'] ?? '#F8FAFC';
            $textColor = $_POST['text_color'] ?? '#1F2937';

            $sql = "UPDATE establishments SET 
                    name = ?, description = ?, address = ?, phone = ?, is_whatsapp = ?, 
                    category = ?, instagram = ?, facebook = ?, delivery_time = ?, delivery_fee = ?, 
                    min_order_value = ?, primary_color = ?, secondary_color = ?, 
                    background_color = ?, text_color = ?, latitude = ?, longitude = ?,
                    cep = ?, street_address = ?, number = ?, complement = ?, 
                    neighborhood = ?, city = ?, state = ?, 
                    updated_at = CURRENT_TIMESTAMP";
            
            $params = [$name, $description, $fullAddress, $phone, $isWhatsapp, $category, 
                      $instagram, $facebook, $deliveryTime, $deliveryFee, $minOrderValue, 
                      $primaryColor, $secondaryColor, $backgroundColor, $textColor, $latitude, $longitude,
                      $cep, $streetAddress, $number, $complement, $neighborhood, $city, $state];
            
            if ($logo) {
                $sql .= ", logo = ?";
                $params[] = $logo;
            }
            
            if ($photo) {
                $sql .= ", photo = ?";
                $params[] = $photo;
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $establishmentId;

            $stmt = $this->db->getPdo()->prepare($sql);
            $stmt->execute($params);

            // Update business hours
            $days = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
            for ($i = 0; $i < 7; $i++) {
                $openTime = $_POST["open_time_{$i}"] ?? null;
                $closeTime = $_POST["close_time_{$i}"] ?? null;
                $isClosed = isset($_POST["closed_{$i}"]) ? 1 : 0;

                $stmt = $this->db->getPdo()->prepare("
                    UPDATE business_hours 
                    SET open_time = ?, close_time = ?, is_closed = ? 
                    WHERE establishment_id = ? AND day_of_week = ?
                ");
                $stmt->execute([$openTime, $closeTime, $isClosed, $establishmentId, $i]);
            }

            $this->db->getPdo()->commit();
            $this->redirect('/profile?success=Perfil atualizado com sucesso');

        } catch (\Exception $e) {
            $this->db->getPdo()->rollBack();
            $this->view('establishment/edit', [
                'establishment' => $establishment,
                'error' => 'Erro ao atualizar perfil: ' . $e->getMessage()
            ]);
        }
    }

    public function geocodeAddress(): void
    {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Método não permitido'], 405);
            return;
        }
        
        $address = $_POST['address'] ?? '';
        
        if (empty($address)) {
            $this->json(['error' => 'Endereço é obrigatório'], 400);
            return;
        }
        
        try {
            $geocodingService = new GeocodingService();
            $result = $geocodingService->geocodeAddress($address);
            
            if (!$result) {
                $this->json(['error' => 'Endereço não encontrado. Verifique se o endereço está correto.'], 404);
                return;
            }
            
            $this->json([
                'success' => true,
                'latitude' => $result['lat'],
                'longitude' => $result['lng'],
                'formatted_address' => $result['formatted_address'] ?? $result['display_name']
            ]);
            
        } catch (\Exception $e) {
            error_log('Geocoding error: ' . $e->getMessage());
            $this->json(['error' => 'Erro ao geocodificar endereço. Tente novamente.'], 500);
        }
    }

    public function viaCep(): void
    {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->json(['error' => 'Método não permitido'], 405);
            return;
        }
        
        $cep = $_GET['cep'] ?? '';
        $cep = preg_replace('/\D/', '', $cep); // Remove non-digits
        
        if (strlen($cep) !== 8) {
            $this->json(['error' => 'CEP deve ter 8 dígitos'], 400);
            return;
        }
        
        try {
            // Call ViaCEP API
            $url = "https://viacep.com.br/ws/{$cep}/json/";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'ComidaSM/1.0 (Food Delivery Platform)');
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode !== 200 || !$response) {
                $this->json(['error' => 'Erro ao consultar CEP'], 500);
                return;
            }
            
            $data = json_decode($response, true);
            
            if (!$data || isset($data['erro'])) {
                $this->json(['error' => 'CEP não encontrado'], 404);
                return;
            }
            
            $this->json([
                'success' => true,
                'street_address' => $data['logradouro'] ?? '',
                'neighborhood' => $data['bairro'] ?? '',
                'city' => $data['localidade'] ?? '',
                'state' => $data['uf'] ?? ''
            ]);
            
        } catch (\Exception $e) {
            error_log('ViaCEP error: ' . $e->getMessage());
            $this->json(['error' => 'Erro ao consultar CEP. Tente novamente.'], 500);
        }
    }

    public function deliveryZones(): void
    {
        $this->requireAuth();
        
        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->json(['error' => 'Estabelecimento não encontrado'], 404);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $stmt = $this->db->getPdo()->prepare("
                SELECT * FROM delivery_zones 
                WHERE establishment_id = ? 
                ORDER BY sort_order, radius_km
            ");
            $stmt->execute([$establishment['id']]);
            $zones = $stmt->fetchAll();
            
            $this->json(['zones' => $zones]);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->createDeliveryZone($establishment['id']);
            return;
        }
        
        $this->json(['error' => 'Método não permitido'], 405);
    }

    public function updateDeliveryZone(): void
    {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            $this->json(['error' => 'Método não permitido'], 405);
            return;
        }
        
        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->json(['error' => 'Estabelecimento não encontrado'], 404);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $zoneId = $input['id'] ?? 0;
        $name = $input['name'] ?? '';
        $radiusKm = (float)($input['radius_km'] ?? 0);
        $deliveryFee = (float)($input['delivery_fee'] ?? 0);
        $minOrderValue = (float)($input['min_order_value'] ?? 0);
        $sortOrder = (int)($input['sort_order'] ?? 0);
        
        if (empty($name) || $radiusKm <= 0) {
            $this->json(['error' => 'Nome e raio são obrigatórios'], 400);
            return;
        }
        
        try {
            $stmt = $this->db->getPdo()->prepare("
                UPDATE delivery_zones 
                SET name = ?, radius_km = ?, delivery_fee = ?, min_order_value = ?, 
                    sort_order = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ? AND establishment_id = ?
            ");
            $stmt->execute([$name, $radiusKm, $deliveryFee, $minOrderValue, $sortOrder, $zoneId, $establishment['id']]);
            
            if ($stmt->rowCount() === 0) {
                $this->json(['error' => 'Zona de entrega não encontrada'], 404);
                return;
            }
            
            $this->json(['success' => true, 'message' => 'Zona de entrega atualizada']);
            
        } catch (\Exception $e) {
            $this->json(['error' => 'Erro ao atualizar zona de entrega'], 500);
        }
    }

    public function deleteDeliveryZone(): void
    {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            $this->json(['error' => 'Método não permitido'], 405);
            return;
        }
        
        $establishment = $this->getCurrentEstablishment();
        if (!$establishment) {
            $this->json(['error' => 'Estabelecimento não encontrado'], 404);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $zoneId = $input['id'] ?? 0;
        
        try {
            $stmt = $this->db->getPdo()->prepare("
                DELETE FROM delivery_zones 
                WHERE id = ? AND establishment_id = ?
            ");
            $stmt->execute([$zoneId, $establishment['id']]);
            
            if ($stmt->rowCount() === 0) {
                $this->json(['error' => 'Zona de entrega não encontrada'], 404);
                return;
            }
            
            $this->json(['success' => true, 'message' => 'Zona de entrega removida']);
            
        } catch (\Exception $e) {
            $this->json(['error' => 'Erro ao remover zona de entrega'], 500);
        }
    }

    private function createDeliveryZone(int $establishmentId): void
    {
        $name = $_POST['name'] ?? '';
        $radiusKm = (float)($_POST['radius_km'] ?? 0);
        $deliveryFee = (float)($_POST['delivery_fee'] ?? 0);
        $minOrderValue = (float)($_POST['min_order_value'] ?? 0);
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        
        if (empty($name) || $radiusKm <= 0) {
            $this->json(['error' => 'Nome e raio são obrigatórios'], 400);
            return;
        }
        
        try {
            $stmt = $this->db->getPdo()->prepare("
                INSERT INTO delivery_zones (establishment_id, name, radius_km, delivery_fee, min_order_value, sort_order) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$establishmentId, $name, $radiusKm, $deliveryFee, $minOrderValue, $sortOrder]);
            
            $zoneId = $this->db->getPdo()->lastInsertId();
            
            $this->json(['success' => true, 'message' => 'Zona de entrega criada', 'id' => $zoneId]);
            
        } catch (\Exception $e) {
            $this->json(['error' => 'Erro ao criar zona de entrega'], 500);
        }
    }

    private function handleInfoUpdate(int $establishmentId): void
    {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        // New detailed address fields
        $cep = $_POST['cep'] ?? '';
        $streetAddress = $_POST['street_address'] ?? '';
        $number = $_POST['number'] ?? '';
        $complement = $_POST['complement'] ?? '';
        $neighborhood = $_POST['neighborhood'] ?? '';
        $city = $_POST['city'] ?? '';
        $state = $_POST['state'] ?? '';
        
        // Build full address for geocoding
        $fullAddress = '';
        if (!empty($streetAddress)) {
            $fullAddress = $streetAddress;
            if (!empty($number)) $fullAddress .= ', ' . $number;
            if (!empty($neighborhood)) $fullAddress .= ', ' . $neighborhood;
            if (!empty($city)) $fullAddress .= ', ' . $city;
            if (!empty($state)) $fullAddress .= ', ' . $state;
        }

        try {
            $this->db->getPdo()->beginTransaction();

            // Geocode address if provided
            $latitude = null;
            $longitude = null;
            if (!empty($fullAddress)) {
                $geocodingService = new GeocodingService();
                $geocodeResult = $geocodingService->geocodeAddress($fullAddress);
                if ($geocodeResult) {
                    $latitude = $geocodeResult['lat'];
                    $longitude = $geocodeResult['lng'];
                }
            }

            $sql = "UPDATE establishments SET 
                    name = ?, description = ?, phone = ?, address = ?,
                    cep = ?, street_address = ?, number = ?, complement = ?, 
                    neighborhood = ?, city = ?, state = ?";
            
            $params = [$name, $description, $phone, $fullAddress,
                      $cep, $streetAddress, $number, $complement, $neighborhood, $city, $state];
            
            if ($latitude && $longitude) {
                $sql .= ", latitude = ?, longitude = ?";
                $params[] = $latitude;
                $params[] = $longitude;
            }
            
            $sql .= ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $params[] = $establishmentId;

            $stmt = $this->db->getPdo()->prepare($sql);
            $stmt->execute($params);

            $this->db->getPdo()->commit();
            $this->redirect('/profile/edit/info?success=Informações básicas atualizadas com sucesso');

        } catch (\Exception $e) {
            $this->db->getPdo()->rollBack();
            $this->redirect('/profile/edit/info?error=Erro ao atualizar informações: ' . $e->getMessage());
        }
    }

    private function handleContactUpdate(int $establishmentId): void
    {
        $whatsapp = $_POST['whatsapp'] ?? '';
        $instagram = $_POST['instagram'] ?? '';
        $facebook = $_POST['facebook'] ?? '';

        try {
            // Handle logo upload
            $logo = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $logo = $this->uploadFile($_FILES['logo'], 'logos');
            }

            // Handle photo upload
            $photo = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $photo = $this->uploadFile($_FILES['photo'], 'general');
            }

            $sql = "UPDATE establishments SET whatsapp = ?, instagram = ?, facebook = ?";
            $params = [$whatsapp, $instagram, $facebook];
            
            if ($logo) {
                $sql .= ", logo = ?";
                $params[] = $logo;
            }
            
            if ($photo) {
                $sql .= ", photo = ?";
                $params[] = $photo;
            }
            
            $sql .= ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $params[] = $establishmentId;

            $stmt = $this->db->getPdo()->prepare($sql);
            $stmt->execute($params);

            $this->redirect('/profile/edit/contact?success=Informações de contato atualizadas com sucesso');

        } catch (\Exception $e) {
            $this->redirect('/profile/edit/contact?error=Erro ao atualizar informações: ' . $e->getMessage());
        }
    }

    private function handleDeliveryUpdate(int $establishmentId): void
    {
        try {
            // Get form data
            $deliveryFee = (float)($_POST['delivery_fee'] ?? 0);
            $deliveryTime = (int)($_POST['delivery_time'] ?? 30);
            $minOrderValue = (float)($_POST['min_order_value'] ?? 0);
            $maxDeliveryDistance = (float)($_POST['max_delivery_distance'] ?? 5);
            $acceptsDelivery = isset($_POST['accepts_delivery']) ? 1 : 0;
            $acceptsPickup = isset($_POST['accepts_pickup']) ? 1 : 0;
            $freeDeliveryAbove = isset($_POST['free_delivery_above']) ? 1 : 0;
            $freeDeliveryValue = (float)($_POST['free_delivery_value'] ?? 0);
            
            // Handle payment methods
            $paymentMethods = $_POST['payment_methods'] ?? [];
            $paymentMethodsString = implode(',', $paymentMethods);

            $sql = "UPDATE establishments SET 
                    delivery_fee = ?, delivery_time = ?, min_order_value = ?, max_delivery_distance = ?,
                    accepts_delivery = ?, accepts_pickup = ?, free_delivery_above = ?, free_delivery_value = ?,
                    payment_methods = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            
            $params = [
                $deliveryFee, $deliveryTime, $minOrderValue, $maxDeliveryDistance,
                $acceptsDelivery, $acceptsPickup, $freeDeliveryAbove, $freeDeliveryValue,
                $paymentMethodsString, $establishmentId
            ];

            $stmt = $this->db->getPdo()->prepare($sql);
            $stmt->execute($params);

            $this->redirect('/profile/edit/delivery?success=Configurações de entrega atualizadas com sucesso');

        } catch (\Exception $e) {
            $this->redirect('/profile/edit/delivery?error=Erro ao atualizar configurações: ' . $e->getMessage());
        }
    }

    private function handleHoursUpdate(int $establishmentId): void
    {
        try {
            $this->db->getPdo()->beginTransaction();

            // Define all days to ensure we process every day
            $allDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            $operatingHours = [];
            
            // Process each day individually
            foreach ($allDays as $day) {
                $dayData = $_POST['operating_hours'][$day] ?? [];
                $isOpen = isset($dayData['is_open']) ? true : false;
                $openTime = $dayData['open_time'] ?? '08:00';
                $closeTime = $dayData['close_time'] ?? '18:00';
                
                $operatingHours[$day] = [
                    'is_open' => $isOpen,
                    'open_time' => $openTime,
                    'close_time' => $closeTime
                ];
            }
            
            $operatingHoursJson = json_encode($operatingHours);
            
            // Handle special configuration fields
            $specialHoursNote = $_POST['special_hours_note'] ?? '';
            $is24Hours = isset($_POST['is_24_hours']) ? 1 : 0;
            $differentHolidayHours = isset($_POST['different_holiday_hours']) ? 1 : 0;

            // Update establishments table with operating hours and special settings
            $stmt = $this->db->getPdo()->prepare("
                UPDATE establishments 
                SET operating_hours = ?, special_hours_note = ?, is_24_hours = ?, different_holiday_hours = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            $stmt->execute([$operatingHoursJson, $specialHoursNote, $is24Hours, $differentHolidayHours, $establishmentId]);

            // Also update the business_hours table for backward compatibility
            $dayMapping = [
                'sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3,
                'thursday' => 4, 'friday' => 5, 'saturday' => 6
            ];
            
            foreach ($operatingHours as $day => $dayData) {
                if (isset($dayMapping[$day])) {
                    $dayOfWeek = $dayMapping[$day];
                    $isOpen = $dayData['is_open'];
                    $openTime = $dayData['open_time'];
                    $closeTime = $dayData['close_time'];
                    $isClosed = !$isOpen;
                    
                    // Check if record exists
                    $stmt = $this->db->getPdo()->prepare("
                        SELECT id FROM business_hours 
                        WHERE establishment_id = ? AND day_of_week = ?
                    ");
                    $stmt->execute([$establishmentId, $dayOfWeek]);
                    
                    if ($stmt->fetch()) {
                        // Update existing record
                        $stmt = $this->db->getPdo()->prepare("
                            UPDATE business_hours 
                            SET open_time = ?, close_time = ?, is_closed = ? 
                            WHERE establishment_id = ? AND day_of_week = ?
                        ");
                        $stmt->execute([$openTime, $closeTime, $isClosed, $establishmentId, $dayOfWeek]);
                    } else {
                        // Insert new record
                        $stmt = $this->db->getPdo()->prepare("
                            INSERT INTO business_hours (establishment_id, day_of_week, open_time, close_time, is_closed)
                            VALUES (?, ?, ?, ?, ?)
                        ");
                        $stmt->execute([$establishmentId, $dayOfWeek, $openTime, $closeTime, $isClosed]);
                    }
                }
            }

            $this->db->getPdo()->commit();
            $this->redirect('/profile/edit/hours?success=Horários de funcionamento atualizados com sucesso');

        } catch (\Exception $e) {
            $this->db->getPdo()->rollBack();
            $this->redirect('/profile/edit/hours?error=Erro ao atualizar horários: ' . $e->getMessage());
        }
    }

    private function handleDesignUpdate(int $establishmentId): void
    {
        try {
            // Get form data
            $primaryColor = $_POST['primary_color'] ?? '#3B82F6';
            $secondaryColor = $_POST['secondary_color'] ?? '#1E40AF';

            $sql = "UPDATE establishments SET 
                    primary_color = ?, secondary_color = ?, 
                    updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            
            $params = [
                $primaryColor, $secondaryColor,
                $establishmentId
            ];

            $stmt = $this->db->getPdo()->prepare($sql);
            $stmt->execute($params);

            $this->redirect('/profile/edit/design?success=Personalização atualizada com sucesso');

        } catch (\Exception $e) {
            $this->redirect('/profile/edit/design?error=Erro ao atualizar personalização: ' . $e->getMessage());
        }
    }
}

