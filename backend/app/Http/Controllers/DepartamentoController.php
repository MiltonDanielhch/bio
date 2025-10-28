<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use App\Models\Sucursal;
use App\Models\Empleado;
use App\Http\Requests\StoreDepartamentoRequest;
use App\Http\Requests\UpdateDepartamentoRequest;

class DepartamentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewAny', Departamento::class);
        return view('admin.departamentos.browse');
    }

    public function list(\Illuminate\Http\Request $request)
    {
        $this->authorize('viewAny', Departamento::class);
        $search   = $request->get('search', '');
        $paginate = $request->get('paginate', 10);

        $departamentos = Departamento::with(['sucursal.empresa', 'jefe'])
            ->when($search, fn($q) => $q->where('nombre_departamento', 'like', "%$search%"))
            ->orderBy('id', 'desc')
            ->paginate($paginate);

        return view('admin.departamentos.list', compact('departamentos'));
    }

    public function show(Departamento $departamento)
    {
        $this->authorize('view', $departamento);
        return view('admin.departamentos.read', compact('departamento'));
    }

    public function create()
    {
        $this->authorize('create', Departamento::class);
        $sucursales = Sucursal::with('empresa')->where('estado', 'activo')->get();
        $empleados  = Empleado::where('estado', 'activo')->get();
        $departamento = null;
        return view('admin.departamentos.edit-add', compact('sucursales', 'empleados', 'departamento'));
    }

    public function store(StoreDepartamentoRequest $request)
    {
        $data = $request->validated();
        $data['creado_por'] = auth()->id();
        Departamento::create($data);

        return redirect()->route('admin.departamentos.index')
            ->with(['message' => 'Departamento creado.', 'alert-type' => 'success']);
    }

    public function edit(Departamento $departamento)
    {
        $this->authorize('update', $departamento);
        $sucursales = Sucursal::with('empresa')->where('estado', 'activo')->get();
        $empleados  = Empleado::where('estado', 'activo')->get();
        return view('admin.departamentos.edit-add', compact('departamento', 'sucursales', 'empleados'));
    }

    public function update(UpdateDepartamentoRequest $request, Departamento $departamento)
    {
        $departamento->update($request->validated());
        return redirect()->route('admin.departamentos.index')
            ->with(['message' => 'Departamento actualizado.', 'alert-type' => 'success']);
    }

    public function destroy(Departamento $departamento)
    {
        $this->authorize('delete', $departamento);
        $departamento->delete();
        return redirect()->route('admin.departamentos.index')
            ->with(['message' => 'Departamento eliminado.', 'alert-type' => 'success']);
    }
}
