<?php

namespace App\Http\Controllers;

use App\Models\TipoIncidencia;
use App\Traits\ManagesCrud;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TipoIncidenciaController extends Controller
{
    use ManagesCrud;

    protected $model = TipoIncidencia::class;
    protected $browseView = 'admin.tipos-incidencia.browse';
    protected $listView = 'admin.tipos-incidencia.list';

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query->when($search, fn($q) => $q->where('nombre', 'like', "%$search%")->orWhere('descripcion', 'like', "%$search%"));
    }

    public function create()
    {
        $this->authorize('create', TipoIncidencia::class);
        return view('admin.tipos-incidencia.edit-add', ['tipo' => new TipoIncidencia()]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', TipoIncidencia::class);
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:191', Rule::unique('tipos_incidencia')],
            'descripcion' => 'nullable|string|max:65535',
        ]);

        TipoIncidencia::create($data);

        return redirect()->route('admin.tipos-incidencia.index')
            ->with(['message' => 'Tipo de incidencia creado exitosamente.', 'alert-type' => 'success']);
    }

    public function edit(TipoIncidencia $tipo)
    {
        $this->authorize('update', $tipo);
        return view('admin.tipos-incidencia.edit-add', compact('tipo'));
    }

    public function update(Request $request, TipoIncidencia $tipo)
    {
        $this->authorize('update', $tipo);
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:191', Rule::unique('tipos_incidencia')->ignore($tipo->id)],
            'descripcion' => 'nullable|string|max:65535',
        ]);

        $tipo->update($data);

        return redirect()->route('admin.tipos-incidencia.index')
            ->with(['message' => 'Tipo de incidencia actualizado exitosamente.', 'alert-type' => 'success']);
    }

    public function destroy(TipoIncidencia $tipo)
    {
        $this->authorize('delete', $tipo);
        try {
            $tipo->delete();
            return redirect()->route('admin.tipos-incidencia.index')
                ->with(['message' => 'Tipo de incidencia eliminado exitosamente.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error("Error al eliminar tipo de incidencia {$tipo->id}: " . $e->getMessage());
            return redirect()->route('admin.tipos-incidencia.index')
                ->with(['message' => 'Error al eliminar el tipo de incidencia. Es posible que esté en uso.', 'alert-type' => 'error']);
        }
    }
}
