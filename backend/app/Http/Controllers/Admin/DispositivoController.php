<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispositivo;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use App\Jobs\SyncUsersToDeviceJob;
use App\Jobs\SyncAttendanceJob;
use Illuminate\Support\Facades\Log;
use App\Services\ZkService;
use App\Http\Requests\StoreDispositivoRequest;
use App\Http\Requests\UpdateDispositivoRequest;
use App\Traits\ManagesCrud;
use Illuminate\Database\Eloquent\Builder;

class DispositivoController extends Controller
{
    use ManagesCrud;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra la vista principal para listar dispositivos.
     */
    protected $model = Dispositivo::class;
    protected $browseView = 'admin.dispositivos.browse';
    protected $listView = 'admin.dispositivos.list';
    protected $with = ['sucursal', 'creador'];

    /**
     * Devuelve la lista paginada y con capacidad de búsqueda de dispositivos.
     */
    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query->when($search, fn($q) => $q->where('nombre_dispositivo', 'like', "%$search%")
            ->orWhere('direccion_ip', 'like', "%$search%")
            ->orWhere('numero_serie', 'like', "%$search%"));
    }

    /**
     * Muestra los detalles de un dispositivo específico.
     */
    public function show(Dispositivo $dispositivo)
    {
        $this->authorize('view', $dispositivo);
        return view('admin.dispositivos.read', compact('dispositivo'));
    }

    /**
     * Muestra el formulario para crear un nuevo dispositivo.
     */
    public function create()
    {
        $this->authorize('create', Dispositivo::class);
        $sucursales = Sucursal::where('estado', 'activo')->orderBy('nombre_sucursal')->get();
        return view('admin.dispositivos.edit-add', [
            'dispositivo' => new Dispositivo(),
            'sucursales' => $sucursales
        ]);
    }

    /**
     * Almacena un nuevo dispositivo en la base de datos.
     */
    public function store(StoreDispositivoRequest $request)
    {
        $this->authorize('create', Dispositivo::class);
        $data = $request->validated();
        $data['creado_por'] = auth()->id();
        Dispositivo::create($data);

        return redirect()->route('admin.dispositivos.index')
            ->with(['message' => 'Dispositivo creado exitosamente.', 'alert-type' => 'success']);
    }

    /**
     * Muestra el formulario para editar un dispositivo existente.
     */
    public function edit(Dispositivo $dispositivo)
    {
        $this->authorize('update', $dispositivo);
        $sucursales = Sucursal::where('estado', 'activo')->orderBy('nombre_sucursal')->get();
        return view('admin.dispositivos.edit-add', compact('dispositivo', 'sucursales'));
    }

    /**
     * Actualiza un dispositivo en la base de datos.
     */
    public function update(UpdateDispositivoRequest $request, Dispositivo $dispositivo)
    {
        $this->authorize('update', $dispositivo);
        $dispositivo->update($request->validated());

        return redirect()->route('admin.dispositivos.index')
            ->with(['message' => 'Dispositivo actualizado exitosamente.', 'alert-type' => 'success']);
    }

    /**
     * Elimina un dispositivo de la base de datos.
     */
    public function destroy(Dispositivo $dispositivo)
    {
        $this->authorize('delete', $dispositivo);
        try {
            $dispositivo->delete();
            return redirect()->route('admin.dispositivos.index')
                ->with(['message' => 'Dispositivo eliminado exitosamente.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error("Error al eliminar dispositivo {$dispositivo->id}: " . $e->getMessage());
            return redirect()->route('admin.dispositivos.index')
                ->with(['message' => 'Error al eliminar el dispositivo.', 'alert-type' => 'error']);
        }
    }

    /**
     * Prueba la conexión con un dispositivo ZKTeco.
     */
    public function testConnection(Dispositivo $dispositivo, ZkService $zkService) {
        try {
            $info = $zkService->getDeviceInfo($dispositivo->direccion_ip, $dispositivo->puerto, $dispositivo->password);
            if ($info && isset($info['serial_number'])) {
                return back()->with(['message' => "Conexión exitosa con {$dispositivo->nombre_dispositivo} (SN: {$info['serial_number']})", 'alert-type' => 'success']);
            }
            return back()->with(['message' => 'El dispositivo respondió, pero la información no es válida.', 'alert-type' => 'warning']);
        } catch (\Exception $e) {
            Log::error("Error al probar conexión de dispositivo {$dispositivo->id}: {$e->getMessage()}");
            return back()->with(['message' => 'Error al probar conexión: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }

    /**
     * Despacha un job para sincronizar la asistencia de un dispositivo específico.
     */
    public function syncNow(Dispositivo $dispositivo) {
        try {
            SyncAttendanceJob::dispatch($dispositivo, false); // 'false' para no limpiar por defecto
            return back()->with(['message' => 'Sincronización iniciada para: ' . $dispositivo->nombre_dispositivo, 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error("Error al despachar job de sincronización para dispositivo {$dispositivo->id}: {$e->getMessage()}");
            return back()->with(['message' => 'Error al iniciar sincronización: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }

    /**
     * Encola un trabajo para sincronizar (subir) la lista de usuarios
     * desde el sistema hacia el dispositivo físico.
     *
     * @param  \App\Models\Dispositivo  $dispositivo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function syncUsers(Dispositivo $dispositivo)
    {
        try {
            // Despacha el Job a la cola
            SyncUsersToDeviceJob::dispatch($dispositivo);

            // Devuelve al usuario inmediatamente con un mensaje de éxito
            return back()->with([
                'message'    => 'La sincronización de usuarios ha sido encolada. Los cambios se reflejarán en el dispositivo en breve.',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            Log::error("Error al despachar job de sincronización de usuarios para dispositivo {$dispositivo->id}: {$e->getMessage()}");
            return back()->with(['message' => 'Error al encolar la tarea de sincronización: ' . $e->getMessage(), 'alert-type' => 'error']);
        }
    }

    /**
     * Muestra la vista para asignar empleados a un dispositivo.
     */
    public function assignEmployees(Dispositivo $dispositivo)
    {
        $this->authorize('update', $dispositivo);
        
        // Obtener todos los empleados activos
        $empleados = \App\Models\Empleado::where('estado', 'activo')
            ->orderBy('nombres')
            ->get();
            
        // Obtener IDs de empleados ya asignados
        $assignedIds = $dispositivo->empleados()->pluck('empleados.id')->toArray();
        
        return view('admin.dispositivos.assign_employees', compact('dispositivo', 'empleados', 'assignedIds'));
    }

    /**
     * Guarda la asignación de empleados a un dispositivo.
     */
    public function storeEmployees(Request $request, Dispositivo $dispositivo)
    {
        $this->authorize('update', $dispositivo);
        
        $request->validate([
            'empleados' => 'array',
            'empleados.*' => 'exists:empleados,id',
        ]);

        $empleadosIds = $request->input('empleados', []);
        
        // Sincronizar (esto agrega los nuevos y elimina los que no están en la lista)
        // Pero necesitamos mantener zk_user_id si ya existe para no romper sincronización
        
        // Estrategia: 
        // 1. Obtener asignaciones actuales
        $currentAssignments = $dispositivo->empleados()->get();
        
        // 2. Preparar array para sync
        $syncData = [];
        
        // Obtener el último zk_user_id usado en este dispositivo para asignar nuevos
        $maxZkId = \Illuminate\Support\Facades\DB::table('dispositivo_empleado')
            ->where('dispositivo_id', $dispositivo->id)
            ->max('zk_user_id') ?? 0;
            
        foreach ($empleadosIds as $empId) {
            // Verificar si ya estaba asignado
            $existing = $currentAssignments->firstWhere('id', $empId);
            
            if ($existing) {
                // Mantener datos existentes
                $syncData[$empId] = [
                    'zk_user_id' => $existing->pivot->zk_user_id,
                    'privilegio' => $existing->pivot->privilegio,
                    'estado' => 'activo'
                ];
            } else {
                // Nuevo empleado, asignar nuevo zk_user_id
                $maxZkId++;
                $syncData[$empId] = [
                    'zk_user_id' => $maxZkId,
                    'privilegio' => 'usuario', // Por defecto
                    'estado' => 'activo',
                    'estado_sincronizacion' => 'pendiente' // Para que se sincronice luego
                ];
            }
        }
        
        $dispositivo->empleados()->sync($syncData);

        return redirect()->route('admin.dispositivos.index')
            ->with(['message' => 'Empleados asignados exitosamente.', 'alert-type' => 'success']);
    }
}
