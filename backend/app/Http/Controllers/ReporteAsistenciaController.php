<?php

namespace App\Http\Controllers;

use App\Models\ReporteAsistencia;
use App\Models\Empresa;
use App\Http\Requests\StoreReporteAsistenciaRequest;
use App\Jobs\GenerarReporteJob;
use Illuminate\Support\Facades\Storage;

class ReporteAsistenciaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* ----------  LISTADO  ---------- */
    public function index()
    {
        // $this->authorize('browse_reportes_asistencia');
        return view('admin.reportes-asistencia.browse');
    }

    public function list(\Illuminate\Http\Request $request)
    {
        // $this->authorize('browse_reportes_asistencia');
        $search   = $request->get('search', '');
        $paginate = $request->get('paginate', 10);

        $reportes = ReporteAsistencia::with(['empresa', 'generador'])
            ->when($search, fn($q) => $q->where('nombre_reporte', 'like', "%$search%"))
            ->orderBy('id', 'desc')
            ->paginate($paginate);

        return view('admin.reportes-asistencia.list', compact('reportes'));
    }

    /* ----------  VER  ---------- */
    public function show(ReporteAsistencia $reporte)
    {
        // $this->authorize('read_reportes_asistencia');
        return view('admin.reportes-asistencia.read', compact('reporte'));
    }

    /* ----------  CREAR  ---------- */
    public function create()
    {
        // $this->authorize('add_reportes_asistencia');
        $empresas = Empresa::where('estado', 'activo')->orderBy('nombre_empresa')->get();
        return view('admin.reportes-asistencia.edit-add', compact('empresas'));
    }

    public function store(StoreReporteAsistenciaRequest $request)
    {
        // $this->authorize('add_reportes_asistencia');
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
        // $this->authorize('read_reportes_asistencia');

        if ($reporte->estado !== 'completado' || ! $reporte->archivo_path) {
            return redirect()->back()->with(['message' => 'El archivo aún no está disponible.', 'alert-type' => 'warning']);
        }

        return response()->download(Storage::path($reporte->archivo_path));
    }

    /* ----------  ELIMINAR  ---------- */
    public function destroy(ReporteAsistencia $reporte)
    {
        // $this->authorize('delete_reportes_asistencia');

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
