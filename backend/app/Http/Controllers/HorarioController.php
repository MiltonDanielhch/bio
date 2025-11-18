<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use App\Models\Empresa;
use App\Http\Requests\StoreHorarioRequest;
use App\Http\Requests\UpdateHorarioRequest;
use App\Traits\ManagesCrud;
use Illuminate\Database\Eloquent\Builder;

class HorarioController extends Controller
{
    use ManagesCrud;

    protected $model = Horario::class;
    protected $browseView = 'admin.horarios.browse';
    protected $listView = 'admin.horarios.list';
    protected $with = ['empresa', 'creador'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query->when($search, fn($q) => $q->where('nombre_horario', 'like', "%$search%"));
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
        return view('admin.horarios.edit-add', ['horario' => new Horario(), 'empresas' => $empresas]);
    }

    public function store(StoreHorarioRequest $request)
    {
        $this->authorize('create', Horario::class);
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
        $this->authorize('update', $horario);
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
