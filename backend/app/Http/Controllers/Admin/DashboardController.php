<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispositivo;
use App\Models\Empleado;
use App\Models\RegistroAsistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Autorización para ver el panel de administración
        $this->authorize('browse_admin', null);

        $stats = [
            'total_empleados' => Empleado::where('estado', 'activo')->count(),
            'total_dispositivos' => Dispositivo::activos()->count(),
            'asistencias_hoy' => RegistroAsistencia::whereDate('fecha_hora', today())->count(),
            'empleados_presentes_hoy' => RegistroAsistencia::whereDate('fecha_hora', today())->distinct('empleado_id')->count(),
        ];

        // Para los dispositivos online/offline, necesitamos una lógica más avanzada.
        // Por ahora, simularemos el dato, pero la implementación ideal requiere un job en segundo plano.
        // Asumiremos que los dispositivos con IP que termina en número par están "online".
        $dispositivos = Dispositivo::activos()->get();
        $onlineCount = $dispositivos->filter(function ($dispositivo) {
            $lastIpSegment = (int) substr($dispositivo->direccion_ip, -1);
            return $lastIpSegment % 2 === 0;
        })->count();

        $stats['dispositivos_online'] = $onlineCount;
        $stats['dispositivos_offline'] = $stats['total_dispositivos'] - $onlineCount;

        // Obtener los últimos 5 registros de asistencia
        $ultimas_asistencias = RegistroAsistencia::with(['empleado', 'dispositivo'])
            ->latest('fecha_hora')
            ->take(5)
            ->get();

        return view('vendor.voyager.index', [
            'stats' => $stats,
            'ultimas_asistencias' => $ultimas_asistencias
        ]);
    }
}
