<?php

namespace App\Jobs;

use App\Models\Dispositivo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncUsersToDeviceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * El número de veces que puede reintentarse el trabajo.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * El número de segundos que el trabajo puede ejecutarse antes de expirar.
     *
     * @var int
     */
    public $timeout = 120;

    protected Dispositivo $dispositivo;

    /**
     * Create a new job instance.
     */
    public function __construct(Dispositivo $dispositivo)
    {
        $this->dispositivo = $dispositivo;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Iniciando Job de sincronización de usuarios para el dispositivo: {$this->dispositivo->nombre_dispositivo} ({$this->dispositivo->direccion_ip})");

        // 1. Obtener los empleados mapeados a este dispositivo
        $empleados = $this->dispositivo->empleados;

        // 2. Formatear los datos para la API del zkservice
        $usersPayload = $empleados->map(function ($empleado) {
            return [
                'uid'       => $empleado->pivot->zk_user_id,
                'user_id'   => (string) $empleado->id, // El ID de usuario en el reloj será el ID del empleado en nuestro sistema
                'name'      => $empleado->nombres . ' ' . $empleado->apellidos,
                'privilege' => $empleado->pivot->privilegio ?? 'User',
                'password'  => '', // Puedes añadir lógica para contraseñas si es necesario
            ];
        });

        // 3. Construir la URL y la data para la petición
        $baseUrl = config('services.zkservice.base_url', 'http://zkservice:8001');
        $url = "{$baseUrl}/devices/{$this->dispositivo->direccion_ip}/sync-users?port={$this->dispositivo->puerto}&password={$this->dispositivo->password}";

        // 4. Realizar la llamada HTTP al microservicio
        try {
            $response = Http::withHeaders(['x-api-key' => config('services.zkservice.api_key')])
                            ->timeout(60) // Timeout para la petición HTTP
                            ->post($url, [
                                'users' => $usersPayload->toArray()
                            ]);

            if ($response->successful()) {
                Log::info("Sincronización de usuarios completada con éxito para el dispositivo: {$this->dispositivo->nombre_dispositivo}. Respuesta: " . $response->body());
            } else {
                Log::error("Error al sincronizar usuarios para {$this->dispositivo->nombre_dispositivo}. Estado: {$response->status()}. Respuesta: " . $response->body());
                $this->fail(new \Exception("El microservicio devolvió un error: " . $response->body()));
            }
        } catch (\Exception $e) {
            Log::critical("Fallo crítico en el Job SyncUsersToDeviceJob para {$this->dispositivo->nombre_dispositivo}: " . $e->getMessage());
            $this->fail($e);
        }
    }
}
