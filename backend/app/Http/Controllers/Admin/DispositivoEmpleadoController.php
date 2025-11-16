<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispositivo;
use App\Models\DispositivoEmpleado;
use App\Models\Empleado; // Usar el modelo Empleado
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DispositivoEmpleadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.dispositivo_empleado.browse');
    }

    public function list(Request $request)
    {
        $search = $request->get('search', '');
        $paginate = $request->get('paginate', 10);

        $mapeos = DispositivoEmpleado::with(['empleado', 'dispositivo'])
            ->when($search, function ($query) use ($search) {
                $query->where('zk_user_id', 'like', "%$search%")
                    ->orWhereHas('empleado', fn($q) => $q->where('nombres', 'like', "%$search%")->orWhere('apellidos', 'like', "%$search%"))
                    ->orWhereHas('dispositivo', fn($q) => $q->where('nombre_dispositivo', 'like', "%$search%"));
            })
            ->orderBy('id', 'desc')
            ->paginate($paginate);

        return view('admin.dispositivo_empleado.list', compact('mapeos'));
    }

    public function create()
    {
        $empleados = Empleado::where('estado', 'activo')->orderBy('nombres')->get();
        $dispositivos = Dispositivo::activos()->orderBy('nombre_dispositivo')->get();
        return view('admin.dispositivo_empleado.edit-add', [
            'map' => new DispositivoEmpleado(),
            'empleados' => $empleados,
            'dispositivos' => $dispositivos
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'dispositivo_id' => 'required|exists:dispositivos,id',
            'zk_user_id' => [
                'required',
                'integer',
                Rule::unique('dispositivo_empleado')->where(function ($query) use ($request) {
                    return $query->where('dispositivo_id', $request->dispositivo_id);
                }),
            ],
        ], [
            'zk_user_id.unique' => 'El ID de usuario ya está en uso en este dispositivo.'
        ]);

        $validated['estado_sincronizacion'] = 'sincronizado'; // Valor por defecto
        DispositivoEmpleado::create($validated);

        return redirect()->route('admin.dispositivo-empleado.index')
            ->with(['message' => 'Mapeo creado exitosamente.', 'alert-type' => 'success']);
    }

    public function edit(DispositivoEmpleado $map)
    {
        $empleados = Empleado::where('estado', 'activo')->orderBy('nombres')->get();
        $dispositivos = Dispositivo::activos()->orderBy('nombre_dispositivo')->get();
        return view('admin.dispositivo_empleado.edit-add', compact('map', 'empleados', 'dispositivos'));
    }

    public function update(Request $request, DispositivoEmpleado $map)
    {
        $validated = $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'dispositivo_id' => 'required|exists:dispositivos,id',
            'zk_user_id' => [
                'required',
                'integer',
                Rule::unique('dispositivo_empleado')->where(function ($query) use ($request) {
                    return $query->where('dispositivo_id', $request->dispositivo_id);
                })->ignore($map->id),
            ],
        ], [
            'zk_user_id.unique' => 'El ID de usuario ya está en uso en este dispositivo.'
        ]);

        $map->update($validated);

        return redirect()->route('admin.dispositivo-empleado.index')
            ->with(['message' => 'Mapeo actualizado exitosamente.', 'alert-type' => 'success']);
    }

    public function destroy(DispositivoEmpleado $map)
    {
        try {
            $map->delete();
            return redirect()->route('admin.dispositivo-empleado.index')
                ->with(['message' => 'Mapeo eliminado exitosamente.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error("Error al eliminar mapeo {$map->id}: " . $e->getMessage());
            return redirect()->route('admin.dispositivo-empleado.index')
                ->with(['message' => 'Error al eliminar el mapeo.', 'alert-type' => 'error']);
        }
    }
}
