<?php

namespace App\Console\Commands;

use App\Models\Dispositivo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestUserSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:user-sync {dispositivo_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba manual de sincronización de usuarios con el microservicio zkservice';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dispositivoId = $this->argument('dispositivo_id');
        $dispositivo = Dispositivo::find($dispositivoId);

        if (!$dispositivo) {
            $this->error("❌ No se encontró el dispositivo con ID: {$dispositivoId}");
            return 1;
        }

        $this->info("═══════════════════════════════════════════════════════");
        $this->info("   PRUEBA MANUAL DE SINCRONIZACIÓN DE USUARIOS");
        $this->info("═══════════════════════════════════════════════════════");
        $this->newLine();

        // 1. Información del dispositivo
        $this->info("📱 Dispositivo: {$dispositivo->nombre_dispositivo}");
        $this->info("🌐 IP: {$dispositivo->direccion_ip}:{$dispositivo->puerto}");
        $this->newLine();

        // 2. Obtener empleados
        $empleados = $dispositivo->empleados;
        
        if ($empleados->isEmpty()) {
            $this->warn("⚠ No hay empleados asignados a este dispositivo");
            return 1;
        }

        $this->info("👥 Empleados a sincronizar: {$empleados->count()}");
        $this->newLine();

        // 3. Formatear payload (igual que en el Job)
        $usersPayload = $empleados->map(function ($empleado) {
            return [
                'uid'       => $empleado->pivot->zk_user_id,
                'user_id'   => (string) $empleado->id,
                'name'      => $empleado->nombres . ' ' . $empleado->apellidos,
                'privilege' => $empleado->pivot->privilegio ?? 'User',
                'password'  => '',
            ];
        });

        $this->info("📦 Payload que se enviará:");
        $this->line(json_encode(['users' => $usersPayload->toArray()], JSON_PRETTY_PRINT));
        $this->newLine();

        // 4. Construir URL y headers
        $baseUrl = config('services.zkservice.base_url', 'http://localhost:8001');
        $apiKey = config('services.zkservice.api_key');
        $url = "{$baseUrl}/devices/{$dispositivo->direccion_ip}/sync-users?port={$dispositivo->puerto}&password={$dispositivo->password}";

        $this->info("🌐 URL del microservicio:");
        $this->line($url);
        $this->newLine();

        $this->info("🔑 Headers:");
        $this->line("  x-api-key: " . ($apiKey ? substr($apiKey, 0, 20) . '...' : 'NO CONFIGURADA'));
        $this->newLine();

        if (!$apiKey) {
            $this->error("❌ ZKSERVICE_API_KEY no está configurada en el .env");
            $this->warn("💡 Agrega: ZKSERVICE_API_KEY=310dbfcdbe0a2234e73a07078bce4e2d1291ec026edfce78cd8a6c4679b10b99");
            return 1;
        }

        // 5. Confirmar antes de enviar
        if (!$this->confirm('¿Deseas proceder con el envío al microservicio?', true)) {
            $this->warn('Operación cancelada por el usuario');
            return 0;
        }

        $this->newLine();
        $this->info("📤 Enviando petición al zkservice...");

        // 6. Realizar la petición HTTP
        try {
            $response = Http::withHeaders(['x-api-key' => $apiKey])
                ->timeout(60)
                ->post($url, [
                    'users' => $usersPayload->toArray()
                ]);

            $this->newLine();
            $this->info("📥 Respuesta recibida:");
            $this->line("  Status Code: {$response->status()}");
            $this->line("  Body: " . ($response->body() ?: '(vacío)'));
            $this->newLine();

            if ($response->successful()) {
                $this->info("✅ Sincronización completada exitosamente");
                return 0;
            } else {
                $this->error("❌ El microservicio devolvió un error");
                $this->warn("Detalles del error:");
                $this->line($response->body());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Fallo crítico al comunicarse con el microservicio:");
            $this->error($e->getMessage());
            $this->newLine();
            $this->warn("💡 Verifica que el zkservice esté ejecutándose en: {$baseUrl}");
            return 1;
        }
    }
}
