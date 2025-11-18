<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Sucursal;
use App\Models\Empleado;
use App\Http\Requests\StoreDepartamentoRequest;
use App\Http\Requests\UpdateDepartamentoRequest;
use App\Traits\ManagesCrud;
use Illuminate\Database\Eloquent\Builder;

class DepartamentoController extends Controller
{
    use ManagesCrud;

    protected $model = Departamento::class;
    protected $browseView = 'admin.departamentos.browse';
    protected $listView = 'admin.departamentos.list';
    protected $with = ['sucursal.empresa', 'jefe'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query->when($search, fn($q) => $q->where('nombre_departamento', 'like', "%$search%"));
    }

    public function show(Departamento $departamento)
    {
        $this->authorize('view', $departamento); // Descomentado
        return view('admin.departamentos.read', compact('departamento'));
    }

    public function create()
    {
        $this->authorize('create', Departamento::class); // Descomentado
        $sucursales = Sucursal::with('empresa')->where('estado', 'activo')->get();
        $empleados  = Empleado::where('estado', 'activo')->get();
        $departamento = null;
        return view('admin.departamentos.edit-add', compact('sucursales', 'empleados', 'departamento'));
    }

    public function store(StoreDepartamentoRequest $request)
    {
        $this->authorize('create', Departamento::class); // Añadido
        $data = $request->validated();
        $data['creado_por'] = auth()->id();
        Departamento::create($data);

        return redirect()->route('admin.departamentos.index')
            ->with(['message' => 'Departamento creado.', 'alert-type' => 'success']);
    }

    public function edit(Departamento $departamento)
    {
        $this->authorize('update', $departamento); // Descomentado
        $sucursales = Sucursal::with('empresa')->where('estado', 'activo')->get();
        $empleados  = Empleado::where('estado', 'activo')->get();
        return view('admin.departamentos.edit-add', compact('departamento', 'sucursales', 'empleados'));
    }

    public function update(UpdateDepartamentoRequest $request, Departamento $departamento)
    {
        $this->authorize('update', $departamento); // Añadido
        $departamento->update($request->validated());
        return redirect()->route('admin.departamentos.index')
            ->with(['message' => 'Departamento actualizado.', 'alert-type' => 'success']);
    }

    public function destroy(Departamento $departamento)
    {
        $this->authorize('delete', $departamento); // Descomentado
        $departamento->delete();
        return redirect()->route('admin.departamentos.index')
            ->with(['message' => 'Departamento eliminado.', 'alert-type' => 'success']);
    }
}
