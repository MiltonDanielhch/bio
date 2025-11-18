<?php

namespace App\Http\Controllers;

use App\Models\AsignacionHorario;
use App\Models\Empleado;
use App\Models\Horario;
use App\Http\Requests\StoreAsignacionHorarioRequest;
use App\Http\Requests\UpdateAsignacionHorarioRequest;
use App\Traits\ManagesCrud;
use Illuminate\Database\Eloquent\Builder;

class AsignacionHorarioController extends Controller
{
    use ManagesCrud;

    protected $model = AsignacionHorario::class;
    protected $browseView = 'admin.asignacion-horarios.browse';
    protected $listView = 'admin.asignacion-horarios.list';
    protected $with = ['empleado.empresa', 'horario'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query->whereHas('empleado', function ($q) use ($search) {
            $q->where('nombres', 'like', "%$search%")
              ->orWhere('apellidos', 'like', "%$search%")
              ->orWhere('codigo_empleado', 'like', "%$search%");
        });
    }

    public function show(AsignacionHorario $asignacionHorario)
    {
        $this->authorize('view', $asignacionHorario);
        return view('admin.asignacion-horarios.read', ['asignacion' => $asignacionHorario]);
    }

    public function create()
    {
        $this->authorize('create', AsignacionHorario::class);
        $empleados = Empleado::where('estado', 'activo')->orderBy('apellidos')->get();
        $horarios = Horario::where('estado', 'activo')->orderBy('nombre_horario')->get();
        return view('admin.asignacion-horarios.edit-add', [
            'asignacion' => new AsignacionHorario(['activo' => true]),
            'empleados' => $empleados,
            'horarios' => $horarios,
        ]);
    }

    public function store(StoreAsignacionHorarioRequest $request)
    {
        $this->authorize('create', AsignacionHorario::class);
        $data = $request->validated();
        $data['creado_por'] = auth()->id();

        AsignacionHorario::create($data);

        return redirect()->route('admin.asignacion-horarios.index')
            ->with(['message' => 'Asignación creada.', 'alert-type' => 'success']);
    }

    public function edit(AsignacionHorario $asignacionHorario)
    {
        $this->authorize('update', $asignacionHorario);
        $empleados = Empleado::where('estado', 'activo')->orderBy('apellidos')->get();
        $horarios = Horario::where('estado', 'activo')->orderBy('nombre_horario')->get();
        return view('admin.asignacion-horarios.edit-add', [
            'asignacion' => $asignacionHorario,
            'empleados' => $empleados,
            'horarios' => $horarios,
        ]);
    }

    public function update(UpdateAsignacionHorarioRequest $request, AsignacionHorario $asignacionHorario)
    {
        $this->authorize('update', $asignacionHorario);
        $asignacionHorario->update($request->validated());

        return redirect()->route('admin.asignacion-horarios.index')
            ->with(['message' => 'Asignación actualizada.', 'alert-type' => 'success']);
    }

    public function destroy(AsignacionHorario $asignacionHorario)
    {
        $this->authorize('delete', $asignacionHorario);
        $asignacionHorario->delete();

        return redirect()->route('admin.asignacion-horarios.index')
            ->with(['message' => 'Asignación eliminada.', 'alert-type' => 'success']);
    }
}
