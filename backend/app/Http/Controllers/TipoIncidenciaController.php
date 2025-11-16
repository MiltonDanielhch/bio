<?php

namespace App\Http\Controllers;

use App\Models\TipoIncidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TipoIncidenciaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // $this->authorize('browse_tipos_incidencia');
        return view('admin.tipos-incidencia.browse');
    }

    public function list(Request $request)
    {
        // $this->authorize('browse_tipos_incidencia');
        $search = $request->get('search', '');
        $paginate = $request->get('paginate', 10);

        $tipos = TipoIncidencia::query()
            ->when($search, fn($q) => $q->where('nombre', 'like', "%$search%")->orWhere('descripcion', 'like', "%$search%"))
            ->orderBy('id', 'desc')
            ->paginate($paginate);

        return view('admin.tipos-incidencia.list', compact('tipos'));
    }

    public function create()
    {
        // $this->authorize('add_tipos_incidencia');
        return view('admin.tipos-incidencia.edit-add', ['tipo' => new TipoIncidencia()]);
    }

    public function store(Request $request)
    {
        // $this->authorize('add_tipos_incidencia');
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
        // $this->authorize('edit_tipos_incidencia');
        return view('admin.tipos-incidencia.edit-add', compact('tipo'));
    }

    public function update(Request $request, TipoIncidencia $tipo)
    {
        // $this->authorize('edit_tipos_incidencia');
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
        // $this->authorize('delete_tipos_incidencia');
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
