<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Empresa;
use App\Http\Requests\StoreHorarioRequest;
use App\Http\Requests\UpdateHorarioRequest;

class HorarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('viewAny', Horario::class);
        return view('admin.horarios.browse');
    }

    public function list(\Illuminate\Http\Request $request)
    {
        $this->authorize('viewAny', Horario::class);
        $search   = $request->get('search', '');
        $paginate = $request->get('paginate', 10);

        $horarios = Horario::with(['empresa', 'creador'])
            ->when($search, fn($q) => $q->where('nombre_horario', 'like', "%$search%"))
            ->orderBy('id', 'desc')
            ->paginate($paginate);

        return view('admin.horarios.list', compact('horarios'));
    }

    public function show(Horario $horario)
    {
        $this->authorize('view', $horario);
        return view('admin.horarios.read', compact('horario'));
    }

    public function create()
    {
        $this->authorize('create', Horario::class);
        $empresas = Empresa::where('estado', 'activo')->orderBy('nombre_empresa')->get();
        return view('admin.horarios.edit-add', compact('empresas'));
    }

    public function store(StoreHorarioRequest $request)
    {
        $data = $request->validated();
        $data['creado_por'] = auth()->id();
        Horario::create($data);

        return redirect()->route('admin.horarios.index')
            ->with(['message' => 'Horario creado.', 'alert-type' => 'success']);
    }

    public function edit(Horario $horario)
    {
        $this->authorize('update', $horario);
        $empresas = Empresa::where('estado', 'activo')->orderBy('nombre_empresa')->get();
        return view('admin.horarios.edit-add', compact('horario', 'empresas'));
    }

    public function update(UpdateHorarioRequest $request, Horario $horario)
    {
        $horario->update($request->validated());
        return redirect()->route('admin.horarios.index')
            ->with(['message' => 'Horario actualizado.', 'alert-type' => 'success']);
    }

    public function destroy(Horario $horario)
    {
        $this->authorize('delete', $horario);
        $horario->delete();
        return redirect()->route('admin.horarios.index')
            ->with(['message' => 'Horario eliminado.', 'alert-type' => 'success']);
    }
}
