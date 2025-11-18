<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispositivo;
use App\Models\Sucursal;
use Illuminate\Http\Request;
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
}
