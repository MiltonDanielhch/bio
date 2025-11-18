<?php

namespace App\Http\Controllers;

use App\Models\ReporteAsistencia;
use App\Models\Empresa;
use App\Http\Requests\StoreReporteAsistenciaRequest;
use App\Jobs\GenerarReporteJob;
use App\Traits\ManagesCrud;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class ReporteAsistenciaController extends Controller
{
    use ManagesCrud;

    protected $model = ReporteAsistencia::class;
    protected $browseView = 'admin.reportes-asistencia.browse';
    protected $listView = 'admin.reportes-asistencia.list';
    protected $with = ['empresa', 'generador'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query->when($search, fn($q) => $q->where('nombre_reporte', 'like', "%$search%"));
    }

    /* ----------  VER  ---------- */
    public function show(ReporteAsistencia $reporte)
    {
        $this->authorize('view', $reporte);
        return view('admin.reportes-asistencia.read', compact('reporte'));
    }

    /* ----------  CREAR  ---------- */
    public function create()
    {
        $this->authorize('create', ReporteAsistencia::class);
        $empresas = Empresa::where('estado', 'activo')->orderBy('nombre_empresa')->get();
        return view('admin.reportes-asistencia.edit-add', compact('empresas'));
    }

    public function store(StoreReporteAsistenciaRequest $request)
    {
        $this->authorize('create', ReporteAsistencia::class);
        $data = $request->validated();
        $data['nombre_reporte'] = $this->nombreReporte($data);
        $data['filtros']        = $request->only(['tipo']);
        $data['estado']         = 'procesando';
        $data['generado_por']   = auth()->id();

        $reporte = ReporteAsistencia::create($data);

        // Encolar generación
        GenerarReporteJob::dispatch($reporte)->onQueue('reports');

        return redirect()->route('admin.reportes-asistencia.index')
            ->with(['message' => 'Reporte en cola. Recibirás un email cuando esté listo.', 'alert-type' => 'success']);
    }

    /* ----------  DESCARGA DIRECTA  ---------- */
    public function download(ReporteAsistencia $reporte)
    {
        $this->authorize('view', $reporte);

        if ($reporte->estado !== 'completado' || ! $reporte->archivo_path) {
            return redirect()->back()->with(['message' => 'El archivo aún no está disponible.', 'alert-type' => 'warning']);
        }

        return response()->download(Storage::path($reporte->archivo_path));
    }

    /* ----------  ELIMINAR  ---------- */
    public function destroy(ReporteAsistencia $reporte)
    {
        $this->authorize('delete', $reporte);

        if ($reporte->archivo_path) {
            Storage::delete($reporte->archivo_path);
        }

        $reporte->delete();

        return redirect()->route('admin.reportes-asistencia.index')
            ->with(['message' => 'Reporte eliminado.', 'alert-type' => 'success']);
    }

    /* ----------  HELPERS  ---------- */
    private function nombreReporte(array $data): string
    {
        $emp = Empresa::find($data['empresa_id'])->nombre_empresa;
        $tipo = $data['tipo'];
        $inicio = \Carbon\Carbon::parse($data['fecha_inicio'])->format('Y-m-d');
        $fin   = \Carbon\Carbon::parse($data['fecha_fin'])->format('Y-m-d');
        return "Reporte {$tipo} - {$emp} ({$inicio} al {$fin})";
    }
}
