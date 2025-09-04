<?php

namespace App\Services;

class GeocodingService
{
    private const NOMINATIM_API_URL = 'https://nominatim.openstreetmap.org/search';
    
    /**
     * Geocode an address to get latitude and longitude
     * 
     * @param string $address The address to geocode
     * @return array|null Returns ['lat' => float, 'lng' => float] or null if not found
     */
    public function geocodeAddress(string $address): ?array
    {
        if (empty(trim($address))) {
            return null;
        }

        // Clean and format address for better results
        $address = trim($address);
        $address = preg_replace('/\s+/', ' ', $address); // Remove extra spaces
        
        $query = http_build_query([
            'q' => $address,
            'format' => 'json',
            'addressdetails' => 1,
            'limit' => 1,
            'countrycodes' => 'br', // Restrict to Brazil
            'accept-language' => 'pt-BR,pt,en'
        ]);

        $url = self::NOMINATIM_API_URL . '?' . $query;

        // Set up cURL with headers
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'ComidaSM/1.0 (Food Delivery Platform)');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Accept-Language: pt-BR,pt,en'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            // Try fallback with simplified address
            return $this->geocodeAddressFallback($address);
        }

        $data = json_decode($response, true);
        
        if (!$data || empty($data)) {
            // Try fallback with simplified address
            return $this->geocodeAddressFallback($address);
        }

        $result = $data[0];
        
        return [
            'lat' => (float)$result['lat'],
            'lng' => (float)$result['lon'],
            'display_name' => $result['display_name'] ?? '',
            'formatted_address' => $this->formatAddress($result)
        ];
    }

    /**
     * Fallback geocoding with simplified address
     * 
     * @param string $address The address to geocode
     * @return array|null Returns ['lat' => float, 'lng' => float] or null if not found
     */
    private function geocodeAddressFallback(string $address): ?array
    {
        // Extract city and state from address for fallback
        $parts = explode(',', $address);
        if (count($parts) >= 2) {
            $city = trim($parts[count($parts) - 2]);
            $state = trim($parts[count($parts) - 1]);
            $fallbackAddress = "{$city}, {$state}, Brasil";
            
            $query = http_build_query([
                'q' => $fallbackAddress,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 1,
                'countrycodes' => 'br'
            ]);

            $url = self::NOMINATIM_API_URL . '?' . $query;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_USERAGENT, 'ComidaSM/1.0 (Food Delivery Platform)');

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if ($data && !empty($data)) {
                    $result = $data[0];
                    return [
                        'lat' => (float)$result['lat'],
                        'lng' => (float)$result['lon'],
                        'display_name' => $result['display_name'] ?? '',
                        'formatted_address' => $this->formatAddress($result)
                    ];
                }
            }
        }
        
        return null;
    }

    /**
     * Reverse geocode coordinates to get address
     * 
     * @param float $lat Latitude
     * @param float $lng Longitude
     * @return array|null Returns address information or null if not found
     */
    public function reverseGeocode(float $lat, float $lng): ?array
    {
        $query = http_build_query([
            'lat' => $lat,
            'lon' => $lng,
            'format' => 'json',
            'addressdetails' => 1,
            'zoom' => 18
        ]);

        $url = 'https://nominatim.openstreetmap.org/reverse?' . $query;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, 'ComidaSM/1.0 (Food Delivery Platform)');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return null;
        }

        $data = json_decode($response, true);
        
        if (!$data) {
            return null;
        }

        return [
            'display_name' => $data['display_name'] ?? '',
            'formatted_address' => $this->formatAddress($data)
        ];
    }

    /**
     * Calculate distance between two points using Haversine formula
     * 
     * @param float $lat1 Latitude of first point
     * @param float $lng1 Longitude of first point
     * @param float $lat2 Latitude of second point
     * @param float $lng2 Longitude of second point
     * @return float Distance in kilometers
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Format address from Nominatim response
     * 
     * @param array $data Nominatim response data
     * @return string Formatted address
     */
    private function formatAddress(array $data): string
    {
        $address = $data['address'] ?? [];
        
        $parts = [];
        
        // Street number and name
        if (!empty($address['house_number']) && !empty($address['road'])) {
            $parts[] = $address['road'] . ', ' . $address['house_number'];
        } elseif (!empty($address['road'])) {
            $parts[] = $address['road'];
        }
        
        // Neighborhood
        if (!empty($address['neighbourhood'])) {
            $parts[] = $address['neighbourhood'];
        } elseif (!empty($address['suburb'])) {
            $parts[] = $address['suburb'];
        }
        
        // City
        if (!empty($address['city'])) {
            $parts[] = $address['city'];
        } elseif (!empty($address['town'])) {
            $parts[] = $address['town'];
        } elseif (!empty($address['municipality'])) {
            $parts[] = $address['municipality'];
        }
        
        // State
        if (!empty($address['state'])) {
            $parts[] = $address['state'];
        }
        
        // Country
        if (!empty($address['country'])) {
            $parts[] = $address['country'];
        }
        
        return implode(', ', array_filter($parts));
    }
}