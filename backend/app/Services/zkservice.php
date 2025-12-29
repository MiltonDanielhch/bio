<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZkService
{
    protected $baseUrl;
    protected $apiKey;

    /**
     * Constructor para inicializar el servicio con la URL base y la API Key.
     */
    public function __construct()
    {
        // Obtiene la configuración desde config/services.php
        $this->baseUrl = config('services.zkservice.base_url');
        $this->apiKey = config('services.zkservice.api_key');

        if (!$this->baseUrl || !$this->apiKey) {
            Log::error('ZkService: La URL del servicio o la API Key no están configuradas en config/services.php');
            throw new \Exception('La configuración del servicio biométrico (zkservice) no está completa.');
        }
    }

    /**
     * Verifica la salud del microservicio ZK.
     *
     * @return bool
     */
    public function checkHealth(): bool
    {
        try {
            $response = Http::withHeaders(['x-api-key' => $this->apiKey])
                ->timeout(5)
                ->get("{$this->baseUrl}/health");

            return $response->successful() && ($response->json()['status'] ?? '') === 'ok';
        } catch (\Exception $e) {
            Log::warning("ZkService: El microservicio no está respondiendo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene la información de un dispositivo.
     *
     * @param string $ip
     * @param int $port
     * @param int|null $password
     * @return array|null
     */
    public function getDeviceInfo(string $ip, int $port, ?int $password = 0): ?array
    {
        $endpoint = "{$this->baseUrl}/devices/{$ip}/info";
        $params = ['port' => $port, 'password' => $password];

        return $this->makeRequest('get', $endpoint, $params);
    }

    /**
     * Obtiene los registros de asistencia de un dispositivo.
     *
     * @param string $ip
     * @param int $port
     * @param int|null $password
     * @return array|null
     */
    public function getAttendance(string $ip, int $port, ?int $password = 0): ?array
    {
        $endpoint = "{$this->baseUrl}/devices/{$ip}/attendance";
        $params = ['port' => $port, 'password' => $password];

        $response = $this->makeRequest('get', $endpoint, $params);

        // El job espera un array de registros, no el objeto completo de respuesta.
        return $response['records'] ?? null;
    }

    /**
     * Limpia los registros de asistencia de un dispositivo.
     *
     * @param string $ip
     * @param int $port
     * @param int|null $password
     * @return array|null
     */
    public function clearAttendance(string $ip, int $port, ?int $password = 0): ?array
    {
        $endpoint = "{$this->baseUrl}/devices/{$ip}/attendance";
        $params = ['port' => $port, 'password' => $password];

        return $this->makeRequest('delete', $endpoint, $params);
    }

    /**
     * Método privado para realizar las peticiones HTTP.
     */
    private function makeRequest(string $method, string $endpoint, array $params = []): ?array
    {
        try {
            return retry(3, function () use ($method, $endpoint, $params) {
                $response = Http::withHeaders(['x-api-key' => $this->apiKey])
                    ->timeout(15) // Timeout para cada intento
                    ->{$method}($endpoint, $params);

                return $response->throw()->json();
            }, 500); // 500ms de espera entre reintentos
        } catch (\Exception $e) {
            Log::error("Error en la petición a ZkService ({$endpoint}): " . $e->getMessage());
            // Relanzar la excepción para que el controlador la capture y muestre un mensaje de error.
            throw $e;
        }
    }
}
