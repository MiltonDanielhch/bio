<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Departamento;
use Illuminate\Http\Request;
use App\Traits\ManagesCrud;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EmpleadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    use ManagesCrud;

    protected $model = Empleado::class;
    protected $browseView = 'admin.empleados.browse';
    protected $listView = 'admin.empleados.list';
    protected $with = ['empresa', 'departamento'];

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query->where('nombres', 'like', "%$search%")
            ->orWhere('apellidos', 'like', "%$search%")
            ->orWhere('dni', 'like', "%$search%")
            ->orWhere('codigo_empleado', 'like', "%$search%");
    }

    public function create()
    {
        $this->authorize('create', Empleado::class);
        $empresas = Empresa::where('estado', 'activo')->get();
        $departamentos = Departamento::where('estado', 'activo')->get();
        return view('admin.empleados.edit-add', [
            'empleado' => new Empleado(),
            'empresas' => $empresas,
            'departamentos' => $departamentos,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Empleado::class);
        $data = $this->validateRequest($request);

        if ($request->hasFile('foto_perfil')) {
            $data['foto_perfil'] = $request->file('foto_perfil')->store('empleados/fotos', 'public');
        }

        $data['creado_por'] = auth()->id();
        Empleado::create($data);

        return redirect()->route('admin.empleados.index')
            ->with(['message' => 'Empleado creado exitosamente.', 'alert-type' => 'success']);
    }

    public function edit(Empleado $empleado)
    {
        $this->authorize('update', $empleado);
        $empresas = Empresa::where('estado', 'activo')->get();
        $departamentos = Departamento::where('estado', 'activo')->get();
        return view('admin.empleados.edit-add', compact('empleado', 'empresas', 'departamentos'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $this->authorize('update', $empleado);
        $data = $this->validateRequest($request, $empleado->id);

        if ($request->hasFile('foto_perfil')) {
            if ($empleado->foto_perfil) {
                Storage::disk('public')->delete($empleado->foto_perfil);
            }
            $data['foto_perfil'] = $request->file('foto_perfil')->store('empleados/fotos', 'public');
        }

        $empleado->update($data);

        return redirect()->route('admin.empleados.index')
            ->with(['message' => 'Empleado actualizado exitosamente.', 'alert-type' => 'success']);
    }

    public function destroy(Empleado $empleado)
    {
        $this->authorize('delete', $empleado);
        try {
            if ($empleado->foto_perfil) {
                Storage::disk('public')->delete($empleado->foto_perfil);
            }
            $empleado->delete();
            return redirect()->route('admin.empleados.index')
                ->with(['message' => 'Empleado eliminado exitosamente.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error("Error al eliminar empleado {$empleado->id}: " . $e->getMessage());
            return redirect()->route('admin.empleados.index')
                ->with(['message' => 'Error al eliminar el empleado.', 'alert-type' => 'error']);
        }
    }

    private function validateRequest(Request $request, $empleadoId = null): array
    {
        return $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'codigo_empleado' => ['required', 'string', 'max:50', Rule::unique('empleados')->ignore($empleadoId)],
            'dni' => ['required', 'string', 'max:20', Rule::unique('empleados')->ignore($empleadoId)],
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => 'nullable|string',
            'email' => ['nullable', 'email', Rule::unique('empleados')->ignore($empleadoId)],
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'fecha_contratacion' => 'required|date',
            'tipo_contrato' => 'required|string',
            'estado' => 'required|string',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    }
}
