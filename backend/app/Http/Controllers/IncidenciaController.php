<?php

namespace App\Http\Controllers;

use App\Models\Incidencia;
use App\Models\Empleado;
use App\Models\TipoIncidencia;
use App\Traits\ManagesCrud;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class IncidenciaController extends Controller
{
    use ManagesCrud;

    protected $model = Incidencia::class;
    protected $browseView = 'admin.incidencias.browse';
    protected $listView = 'admin.incidencias.list';
    protected $with = ['empleado', 'tipoIncidencia', 'aprobador'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query->whereHas('empleado', function ($q) use ($search) {
            $q->where('nombres', 'like', "%$search%")
              ->orWhere('apellidos', 'like', "%$search%");
        })->orWhere('motivo', 'like', "%$search%");
    }

    public function create()
    {
        $this->authorize('create', Incidencia::class);
        $empleados = Empleado::where('estado', 'activo')->orderBy('apellidos')->get();
        $tipos = TipoIncidencia::orderBy('nombre')->get();
        return view('admin.incidencias.edit-add', [
            'incidencia' => new Incidencia(),
            'empleados' => $empleados,
            'tipos' => $tipos,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Incidencia::class);
        $data = $this->validateRequest($request);
        $data['creado_por'] = auth()->id();
        $data['estado'] = 'pendiente'; // Siempre se crea como pendiente

        Incidencia::create($data);

        return redirect()->route('admin.incidencias.index')
            ->with(['message' => 'Incidencia registrada exitosamente.', 'alert-type' => 'success']);
    }

    public function edit(Incidencia $incidencia)
    {
        $this->authorize('update', $incidencia);
        $empleados = Empleado::where('estado', 'activo')->orderBy('apellidos')->get();
        $tipos = TipoIncidencia::orderBy('nombre')->get();
        return view('admin.incidencias.edit-add', compact('incidencia', 'empleados', 'tipos'));
    }

    public function update(Request $request, Incidencia $incidencia)
    {
        $this->authorize('update', $incidencia);
        $data = $this->validateRequest($request, $incidencia->id);

        // Lógica para aprobación/rechazo
        if (in_array($data['estado'], ['aprobado', 'rechazado']) && $incidencia->estado === 'pendiente') {
            $data['aprobado_por'] = auth()->id();
            $data['aprobado_en'] = now();
        }

        $incidencia->update($data);

        return redirect()->route('admin.incidencias.index')
            ->with(['message' => 'Incidencia actualizada exitosamente.', 'alert-type' => 'success']);
    }

    public function destroy(Incidencia $incidencia)
    {
        $this->authorize('delete', $incidencia);
        $incidencia->delete();
        return redirect()->route('admin.incidencias.index')
            ->with(['message' => 'Incidencia eliminada exitosamente.', 'alert-type' => 'success']);
    }

    private function validateRequest(Request $request, $incidenciaId = null): array
    {
        return $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'tipo_incidencia_id' => 'required|exists:tipos_incidencia,id',
            'fecha_incidencia' => 'required|date',
            'hora_incidencia' => 'nullable|date_format:H:i',
            'motivo' => 'required|string|max:65535',
            'observaciones' => 'nullable|string|max:65535',
            'evidencia' => 'nullable|string|max:191', // Asumiendo que es una ruta o nombre de archivo
            'estado' => ['required', Rule::in(['pendiente', 'aprobado', 'rechazado'])],
        ]);
    }
}
