<?php

namespace App\Http\Controllers;


use App\Models\Sucursal;
use App\Models\Empresa;
use App\Http\Requests\StoreSucursalRequest;
use App\Http\Requests\UpdateSucursalRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\ManagesCrud;

class SucursalController extends Controller
{
    use ManagesCrud;

    protected $model = Sucursal::class;
    protected $browseView = 'admin.sucursales.browse';
    protected $listView = 'admin.sucursales.list';
    protected $with = ['empresa', 'creador'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query->when($search, fn($q) => $q->where('nombre_sucursal', 'like', "%$search%"));
    }

    public function show(Sucursal $sucursal)
    {
        $this->authorize('view', $sucursal);
        return view('admin.sucursales.read', compact('sucursal'));
    }

    public function create()
    {
        $this->authorize('create', Sucursal::class);
        $empresas = Empresa::where('estado', 'activo')->orderBy('nombre_empresa')->get(); // Se mantiene la lógica específica
        return view('admin.sucursales.edit-add', [
            'sucursal' => new Sucursal(),
            'empresas' => $empresas,
        ]);
    }

    public function store(StoreSucursalRequest $request)
    {
        $data = $request->validated();
        $data['creado_por'] = auth()->id();
        Sucursal::create($data);

        return redirect()->route('admin.sucursales.index')
            ->with(['message' => 'Sucursal creada.', 'alert-type' => 'success']);
    }

    public function edit(Sucursal $sucursal)
    {
        $this->authorize('update', $sucursal);
        $empresas = Empresa::where('estado', 'activo')->orderBy('nombre_empresa')->get();
        return view('admin.sucursales.edit-add', compact('sucursal', 'empresas'));
    }

    public function update(UpdateSucursalRequest $request, Sucursal $sucursal)
    {
        $sucursal->update($request->validated());
        return redirect()->route('admin.sucursales.index')
            ->with(['message' => 'Sucursal actualizada.', 'alert-type' => 'success']);
    }

    public function destroy(Sucursal $sucursal)
    {
        $this->authorize('delete', $sucursal);
        $sucursal->delete();
        return redirect()->route('admin.sucursales.index')
            ->with(['message' => 'Sucursal eliminada.', 'alert-type' => 'success']);
    }
}
