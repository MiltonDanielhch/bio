<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Http\Requests\StoreEmpresaRequest;
use App\Http\Requests\UpdateEmpresaRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class EmpresaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewAny', Empresa::class);
        return view('admin.empresas.browse');
    }

    public function list(\Illuminate\Http\Request $request)
    {
        $this->authorize('viewAny', Empresa::class);
        $search   = $request->get('search', '');
        $paginate = $request->get('paginate', 10);

        $empresas = Empresa::with('creador')
            ->when($search, fn($q) => $q->where('nombre_empresa', 'like', "%$search%")->orWhere('ruc', 'like', "%$search%"))
            ->orderBy('id', 'desc')
            ->paginate($paginate);

        return view('admin.empresas.list', compact('empresas'));
    }

    public function show(Empresa $empresa)
    {
        $this->authorize('view', $empresa);
        return view('admin.empresas.read', compact('empresa'));
    }

    public function create()
    {
        $this->authorize('create', Empresa::class);
        return view('admin.empresas.edit-add', ['empresa' => new Empresa()]);
    }

    public function store(StoreEmpresaRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }
        $data['creado_por'] = auth()->id();
        Empresa::create($data);

        return redirect()->route('admin.empresas.index')
            ->with(['message' => 'Empresa creada.', 'alert-type' => 'success']);
    }

    public function edit(Empresa $empresa)
    {
        $this->authorize('update', $empresa);
        return view('admin.empresas.edit-add', compact('empresa'));
    }

    public function update(UpdateEmpresaRequest $request, Empresa $empresa)
    {
        $data = $request->validated();
        if ($request->hasFile('logo')) {
            if ($empresa->logo) Storage::disk('public')->delete($empresa->logo);
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }
        $empresa->update($data);

        return redirect()->route('admin.empresas.index')
            ->with(['message' => 'Empresa actualizada.', 'alert-type' => 'success']);
    }

    public function destroy(Empresa $empresa)
    {
        $this->authorize('delete', $empresa);
        try {
            if ($empresa->logo) Storage::disk('public')->delete($empresa->logo);
            $empresa->delete();
            return redirect()->route('admin.empresas.index')
                ->with(['message' => 'Empresa eliminada.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->route('admin.empresas.index')
                ->with(['message' => 'Error al eliminar.', 'alert-type' => 'error']);
        }
    }
}
