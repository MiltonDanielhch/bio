<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Log;

class ZkService
{
    protected PendingRequest $client;

    public function __construct()
    {
        $baseUrl = config('services.zkservice.url');
        $apiKey = config('services.zkservice.key');

        if (!$baseUrl || !$apiKey) {
            throw new \Exception('ZkService URL or API Key is not configured.');
        }

        $this->client = Http::withHeaders([
            'X-API-Key' => $apiKey,
            'Accept' => 'application/json',
        ])->baseUrl($baseUrl);
    }

    /**
     * Obtiene los registros de asistencia de un dispositivo.
     */
    public function getAttendance(string $ip, ?string $password = null): ?array
    {
        $queryParams = [];
        if ($password) {
            $queryParams['password'] = $password;
        }

        $response = $this->client->get("/devices/{$ip}/attendance", $queryParams);

        if ($response->successful()) {
            return $response->json('records');
        }

        Log::error("Failed to get attendance from {$ip}: " . $response->body(), ['status' => $response->status()]);
        return null;
    }

    /**
     * Borra los registros de asistencia de un dispositivo.
     */
    public function clearAttendance(string $ip, ?string $password = null): bool
    {
        $queryParams = [];
        if ($password) {
            $queryParams['password'] = $password;
        }

        $response = $this->client->delete("/devices/{$ip}/attendance", $queryParams);
        return $response->successful();
    }
}
