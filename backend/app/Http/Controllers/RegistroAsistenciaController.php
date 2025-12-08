<?php

namespace App\Http\Controllers;
use App\Models\Empleado;
use App\Models\Dispositivo;
use App\Traits\ManagesCrud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use App\Http\Requests\StoreRegistroAsistenciaRequest;
use App\Http\Requests\UpdateRegistroAsistenciaRequest;
use App\Models\RegistroAsistencia;

class RegistroAsistenciaController extends Controller
{
    use ManagesCrud;

    protected $model = RegistroAsistencia::class;
    protected $readView = 'admin.registros_asistencia.read';
    protected $browseView = 'admin.registros_asistencia.browse';
    protected $listView = 'admin.registros_asistencia.list';
    protected $with = ['empleado', 'dispositivo'];
    protected $orderBy = ['fecha_hora', 'desc'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query->when($search, function ($query) use ($search) {
            $query->whereHas('empleado', function ($q) use ($search) {
                $q->where('nombres', 'like', "%$search%")
                    ->orWhere('apellidos', 'like', "%$search%")
                    ->orWhere('codigo_empleado', 'like', "%$search%");
            })
                ->orWhereHas('dispositivo', function ($q) use ($search) {
                    $q->where('nombre_dispositivo', 'like', "%$search%");
                });
        });
    }

    public function create()
    {
        $this->authorize('create', RegistroAsistencia::class);
        $empleados = Empleado::where('estado', 'activo')->orderBy('nombres')->get();
        $dispositivos = Dispositivo::where('estado', 'activo')->orderBy('nombre_dispositivo')->get();
        return view('admin.registros_asistencia.edit-add', [
            'registro' => new RegistroAsistencia(),
            'empleados' => $empleados,
            'dispositivos' => $dispositivos,
        ]);
    }

    public function store(StoreRegistroAsistenciaRequest $request)
    {
        $data = $request->getValidatedData();
        RegistroAsistencia::create($data);

        return redirect()->route('admin.registros-asistencia.index')
            ->with(['message' => 'Registro de asistencia creado exitosamente.', 'alert-type' => 'success']);
    }
    public function edit(RegistroAsistencia $registro)
    {
        $this->authorize('update', $registro);
        $empleados = Empleado::where('estado', 'activo')->orderBy('nombres')->get();
        $dispositivos = Dispositivo::where('estado', 'activo')->orderBy('nombre_dispositivo')->get();

        return view('admin.registros_asistencia.edit-add', [
            'registro' => $registro,
            'empleados' => $empleados,
            'dispositivos' => $dispositivos,
        ]);
    }

    public function update(UpdateRegistroAsistenciaRequest $request, RegistroAsistencia $registro)
    {
        $data = $request->getValidatedData();

        $registro->update($data);

        return redirect()->route('admin.registros-asistencia.index')
            ->with(['message' => 'Registro de asistencia actualizado exitosamente.', 'alert-type' => 'success']);
    }

    public function destroy(RegistroAsistencia $registro)
    {
        $this->authorize('delete', $registro);
        try {
            $registro->delete();
            return redirect()->route('admin.registros-asistencia.index')
                ->with(['message' => 'Registro de asistencia eliminado exitosamente.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error("Error al eliminar registro de asistencia {$registro->id}: " . $e->getMessage());
            return redirect()->route('admin.registros-asistencia.index')
                ->with(['message' => 'Error al eliminar el registro de asistencia.', 'alert-type' => 'error']);
        }
    }
}
