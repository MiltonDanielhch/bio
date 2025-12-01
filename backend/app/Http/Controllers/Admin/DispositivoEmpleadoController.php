<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ManagesCrud;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Dispositivo;
use App\Models\DispositivoEmpleado;
use App\Models\Empleado; // Usar el modelo Empleado
use Illuminate\Http\Request;
use App\Http\Requests\StoreDispositivoEmpleadoRequest;
use App\Http\Requests\UpdateDispositivoEmpleadoRequest;

class DispositivoEmpleadoController extends Controller
{
    use ManagesCrud;

    protected $model = DispositivoEmpleado::class;
    protected $browseView = 'admin.dispositivo_empleado.browse';
    protected $listView = 'admin.dispositivo_empleado.list';
    protected $with = ['empleado', 'dispositivo'];

    public function __construct()
    {
        $this->middleware('auth');
        // La autorización se maneja dentro de cada método o por el Trait ManagesCrud, no en el constructor.
    }

    public function index()
    {
        // Obtenemos los dispositivos activos para poblar el filtro dropdown.
        $dispositivos = Dispositivo::activos()->orderBy('nombre_dispositivo')->get();

        return view($this->browseView, [
            'dispositivos' => $dispositivos,
        ]);
    }

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query->where('zk_user_id', 'like', "%$search%")
            ->orWhereHas('empleado', fn($q) => $q->where('nombres', 'like', "%$search%")->orWhere('apellidos', 'like', "%$search%"))
            ->orWhereHas('dispositivo', fn($q) => $q->where('nombre_dispositivo', 'like', "%$search%"));
    }

    /**
     * Aplica los filtros avanzados a la consulta.
     * Este método es llamado por el método `list` del Trait `ManagesCrud`.
     *
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    protected function applyFilters(Builder $query, Request $request): Builder
    {
        if ($request->has('dispositivo_id') && $request->dispositivo_id != '') {
            $query->where('dispositivo_id', $request->dispositivo_id);
        }
        return $query;
    }

    public function create()
    {
        // La autorización se verifica usando la clase del modelo.
        $this->authorize('create', DispositivoEmpleado::class);
        $empleados = Empleado::where('estado', 'activo')->orderBy('nombres')->get();
        $dispositivos = Dispositivo::activos()->orderBy('nombre_dispositivo')->get();
        return view('admin.dispositivo_empleado.edit-add', [
            'map' => new DispositivoEmpleado(),
            'empleados' => $empleados,
            'dispositivos' => $dispositivos
        ]);
    }

    public function store(StoreDispositivoEmpleadoRequest $request)
    {
        $this->authorize('create', DispositivoEmpleado::class);
        $validated = $request->validated();
        $validated['estado_sincronizacion'] = 'sincronizado'; // Valor por defecto
        $this->model::create($validated);

        return redirect()->route('admin.dispositivo-empleado.index')
            ->with(['message' => 'Mapeo creado exitosamente.', 'alert-type' => 'success']);
    }

    public function edit(DispositivoEmpleado $map)
    {
        // La autorización se verifica sobre la instancia específica que se va a editar.
        $this->authorize('update', $map);
        $empleados = Empleado::where('estado', 'activo')->orderBy('nombres')->get();
        $dispositivos = Dispositivo::activos()->orderBy('nombre_dispositivo')->get();
        return view('admin.dispositivo_empleado.edit-add', compact('map', 'empleados', 'dispositivos'));
    }

    public function update(UpdateDispositivoEmpleadoRequest $request, DispositivoEmpleado $map)
    {
        // Se verifica la autorización antes de realizar la actualización.
        $this->authorize('update', $map);
        $validated = $request->validated();
        $map->update($validated);

        return redirect()->route('admin.dispositivo-empleado.index')
            ->with(['message' => 'Mapeo actualizado exitosamente.', 'alert-type' => 'success']);
    }

    public function destroy(DispositivoEmpleado $map)
    {
        // Se verifica la autorización antes de eliminar.
        $this->authorize('delete', $map);
        $map->delete();

        return redirect()->route('admin.dispositivo-empleado.index')
            ->with(['message' => 'Mapeo eliminado exitosamente.', 'alert-type' => 'success']);
    }
}
