<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * service untuk integrasi dengan BPS API
 * 
 * CORRECT ENDPOINT: /domain dengan parameter type
 * - type=prov : untuk provinces
 * - type=kabbyprov&prov=XX : untuk regencies by province
 * 
 * path: app/Services/BpsApiService.php
 */
class BpsApiService
{
    private $apiKey;
    private $baseUrl;
    private $timeout;

    public function __construct()
    {
        $this->apiKey = 'f475900cc09fb4013e90d5531c13313f';
        $this->baseUrl = 'https://webapi.bps.go.id/v1/api';
        $this->timeout = 30;
    }

    /**
     * get HTTP client dengan SSL verification disabled
     * 
     * @return \Illuminate\Http\Client\PendingRequest
     */
    private function getHttpClient()
    {
        return Http::withOptions([
            'verify' => false,
            'timeout' => $this->timeout,
        ]);
    }

    /**
     * ambil semua provinces dari BPS
     * endpoint: /domain?type=prov&key=xxx
     * 
     * Response format:
     * {
     *   "status": "OK",
     *   "data-availability": "available",
     *   "data": [
     *     {"page": 1, "pages": 1, "total": 34},  // metadata
     *     [                                        // actual data at index [1]
     *       {"domain_id": "11", "domain_name": "Aceh", ...},
     *       {"domain_id": "12", "domain_name": "Sumatera Utara", ...}
     *     ]
     *   ]
     * }
     * 
     * @return array
     */
    public function getProvinces(): array
    {
        try {
            return Cache::remember('bps_provinces', 86400, function () {
                $url = "{$this->baseUrl}/domain";
                
                $response = $this->getHttpClient()
                    ->get($url, [
                        'type' => 'prov',
                        'key' => $this->apiKey,
                    ]);

                if (!$response->successful()) {
                    throw new \Exception('BPS API response unsuccessful: ' . $response->status());
                }

                $data = $response->json();
                
                Log::debug('BPS API - Full provinces response', [
                    'response' => $data,
                ]);
                
                // cek jika ada error dari BPS
                if (isset($data['status']) && $data['status'] === 'Error') {
                    throw new \Exception('BPS API Error: ' . ($data['message'] ?? 'Unknown error'));
                }
                
                // extract data dari struktur BPS: data[1] adalah array actual data
                $provinces = [];
                
                if (isset($data['data']) && is_array($data['data']) && isset($data['data'][1])) {
                    // data[1] contains the actual provinces array
                    $provincesData = $data['data'][1];
                    
                    if (is_array($provincesData)) {
                        $provinces = $this->formatProvinces($provincesData);
                    }
                }
                
                Log::info('BPS API - Provinces formatted', [
                    'count' => count($provinces),
                    'sample' => array_slice($provinces, 0, 3),
                ]);
                
                if (empty($provinces)) {
                    Log::error('BPS API - No provinces after formatting', [
                        'raw_response' => $data,
                    ]);
                    throw new \Exception('No provinces data received from BPS API');
                }
                
                return $provinces;
            });

        } catch (\Exception $e) {
            Log::error('BPS API - Failed to get provinces', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * ambil regencies berdasarkan province code
     * endpoint: /domain?type=kabbyprov&prov=XX&key=xxx
     * 
     * Response format sama dengan provinces
     * 
     * @param string $provinceCode
     * @return array
     */
    public function getRegencies(string $provinceCode): array
    {
        try {
            $cacheKey = "bps_regencies_{$provinceCode}";
            
            return Cache::remember($cacheKey, 86400, function () use ($provinceCode) {
                $url = "{$this->baseUrl}/domain";
                
                // BPS expects prov param as 4 digits (e.g., "3100" for DKI Jakarta code "31")
                $provParam = str_pad($provinceCode, 4, '0', STR_PAD_RIGHT);
                
                $response = $this->getHttpClient()
                    ->get($url, [
                        'type' => 'kabbyprov',
                        'prov' => $provParam,
                        'key' => $this->apiKey,
                    ]);

                if (!$response->successful()) {
                    throw new \Exception('BPS API response unsuccessful: ' . $response->status());
                }

                $data = $response->json();
                
                // cek jika ada error dari BPS
                if (isset($data['status']) && $data['status'] === 'Error') {
                    throw new \Exception('BPS API Error: ' . ($data['message'] ?? 'Unknown error'));
                }
                
                // extract data dari struktur BPS: data[1] adalah array actual data
                $regencies = [];
                
                if (isset($data['data']) && is_array($data['data']) && isset($data['data'][1])) {
                    // data[1] contains the actual regencies array
                    $regenciesData = $data['data'][1];
                    
                    if (is_array($regenciesData)) {
                        $regencies = $this->formatRegencies($regenciesData, $provinceCode);
                    }
                }
                
                Log::info('BPS API - Regencies retrieved', [
                    'province_code' => $provinceCode,
                    'count' => count($regencies),
                ]);
                
                return $regencies;
            });

        } catch (\Exception $e) {
            Log::error('BPS API - Failed to get regencies', [
                'province_code' => $provinceCode,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * format data provinces dari BPS API
     * 
     * BPS domain API structure:
     * {"domain_id": "11", "domain_name": "Aceh", "domain_url": "..."}
     * 
     * @param array $rawData
     * @return array
     */
    private function formatProvinces(array $rawData): array
    {
        $provinces = [];

        foreach ($rawData as $item) {
            if (!is_array($item)) {
                continue;
            }
            
            // BPS menggunakan key "domain_id" dan "domain_name"
            $code = $item['domain_id'] ?? null;
            $name = $item['domain_name'] ?? null;

            if (!$code || !$name) {
                continue;
            }
            
            // extract 2 digit code dari domain_id
            // domain_id bisa "11", "1100", dll
            $codeStr = (string)$code;
            $shortCode = substr($codeStr, 0, 2);
            
            // validasi code harus numerik dan 2 digit
            if (!is_numeric($shortCode) || strlen($shortCode) != 2) {
                continue;
            }
            
            $provinces[] = [
                'id' => (int) $shortCode,
                'code' => $shortCode,
                'name' => $this->cleanName($name),
            ];
        }

        return $provinces;
    }

    /**
     * format data regencies dari BPS API
     * 
     * BPS domain API structure:
     * {"domain_id": "3171", "domain_name": "Jakarta Selatan", "domain_url": "..."}
     * 
     * @param array $rawData
     * @param string $provinceCode
     * @return array
     */
    private function formatRegencies(array $rawData, string $provinceCode): array
    {
        $regencies = [];

        foreach ($rawData as $item) {
            if (!is_array($item)) {
                continue;
            }
            
            // BPS menggunakan key "domain_id" dan "domain_name"
            $code = $item['domain_id'] ?? null;
            $name = $item['domain_name'] ?? null;

            if (!$code || !$name) {
                continue;
            }
            
            // extract 4 digit code dari domain_id
            $codeStr = (string)$code;
            $shortCode = substr($codeStr, 0, 4);
            
            // validasi code harus numerik dan 4 digit
            if (!is_numeric($shortCode) || strlen($shortCode) != 4) {
                continue;
            }
            
            $regencies[] = [
                'id' => (int) $shortCode,
                'province_id' => (int) $provinceCode,
                'code' => $shortCode,
                'name' => $this->cleanName($name),
            ];
        }

        return $regencies;
    }

    /**
     * bersihkan nama wilayah dari format BPS
     * 
     * @param string $name
     * @return string
     */
    private function cleanName(string $name): string
    {
        // BPS kadang pakai format "Prov. Jawa Barat", "Kab. Bandung", dll
        $name = preg_replace('/^(Prov\.|Provinsi|Kab\.|Kabupaten|Kota)\s+/i', '', $name);
        return trim($name);
    }

    /**
     * clear cache BPS data
     * 
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget('bps_provinces');
        
        for ($i = 11; $i <= 99; $i++) {
            Cache::forget("bps_regencies_{$i}");
        }

        Log::info('BPS API cache cleared');
    }

    /**
     * test koneksi ke BPS API
     * 
     * @return array
     */
    public function testConnection(): array
    {
        try {
            $startTime = microtime(true);
            
            $url = "{$this->baseUrl}/domain";
            $response = $this->getHttpClient()
                ->get($url, [
                    'type' => 'prov',
                    'key' => $this->apiKey,
                ]);

            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);

            $data = $response->json();

            $hasError = isset($data['status']) && $data['status'] === 'Error';
            $hasData = (isset($data['data']) && is_array($data['data'])) || 
                       (is_array($data) && !isset($data['status']));

            return [
                'success' => $response->successful() && !$hasError && $hasData,
                'status_code' => $response->status(),
                'response_time' => $duration . ' ms',
                'has_data' => $hasData,
                'error' => $hasError ? ($data['message'] ?? 'Unknown error') : null,
                'data_sample' => $hasData ? (isset($data['data']) ? array_slice($data['data'], 0, 2) : array_slice($data, 0, 2)) : null,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}