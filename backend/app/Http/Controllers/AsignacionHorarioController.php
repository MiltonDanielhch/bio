<?php

namespace App\Http\Controllers;

use App\Models\AsignacionHorario;
use App\Models\Empleado;
use App\Models\Horario;
use App\Http\Requests\StoreAsignacionHorarioRequest;
use App\Http\Requests\UpdateAsignacionHorarioRequest;

class AsignacionHorarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewAny', AsignacionHorario::class);
        return view('admin.asignacion-horarios.browse');
    }

    public function list(\Illuminate\Http\Request $request)
    {
        $this->authorize('viewAny', AsignacionHorario::class);
        $search   = $request->get('search', '');
        $paginate = $request->get('paginate', 10);

        $asignaciones = AsignacionHorario::with(['empleado', 'horario.empresa'])
            ->when($search, fn($q) => $q->whereHas('empleado', fn($q) => $q->where('nombres', 'like', "%$search%")
                ->orWhere('apellidos', 'like', "%$search%")
                ->orWhere('codigo_empleado', 'like', "%$search%")))
            ->orderBy('id', 'desc')
            ->paginate($paginate);

        return view('admin.asignacion-horarios.list', compact('asignaciones'));
    }

    public function show(AsignacionHorario $asignacionHorario)
    {
        $this->authorize('view', $asignacionHorario);
        return view('admin.asignacion-horarios.read', compact('asignacionHorario'));
    }

    public function create()
    {
        $this->authorize('create', AsignacionHorario::class);
        $empleados = Empleado::where('estado', 'activo')->orderBy('apellidos')->orderBy('nombres')->get();
        $horarios  = Horario::with('empresa')->where('estado', 'activo')->orderBy('nombre_horario')->get();
        $asignacion = null; // <-- clave: evita "Undefined variable"

        return view('admin.asignacion-horarios.edit-add', compact('empleados', 'horarios', 'asignacion'));
    }

    public function store(StoreAsignacionHorarioRequest $request)
    {
        $data = $request->validated();
        $data['creado_por'] = auth()->id();
        AsignacionHorario::create($data);

        return redirect()->route('admin.asignacion-horarios.index')
            ->with(['message' => 'Asignación creada.', 'alert-type' => 'success']);
    }

    public function edit(AsignacionHorario $asignacionHorario)
    {
        $this->authorize('update', $asignacionHorario);
        $empleados = Empleado::where('estado', 'activo')->orderBy('apellidos')->orderBy('nombres')->get();
        $horarios  = Horario::with('empresa')->where('estado', 'activo')->orderBy('nombre_horario')->get();

        // Enviamos como $asignacion para que la vista funcione tanto en create como en edit
        return view('admin.asignacion-horarios.edit-add', [
            'asignacion' => $asignacionHorario,
            'empleados'  => $empleados,
            'horarios'   => $horarios,
        ]);
    }

    public function update(UpdateAsignacionHorarioRequest $request, AsignacionHorario $asignacionHorario)
    {
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
