<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodoPlanilla;
use App\Models\Empresa;
use App\Models\ResumenMensualAsistencia;
use App\Jobs\ProcesarPlanillaJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PlanillaExport;

class PeriodoPlanillaController extends Controller
{
    /**
     * Listado de períodos
     */
    public function index(Request $request)
    {
        $empresaId = $request->get('empresa_id', auth()->user()->empresa_id ?? null);

        $periodos = PeriodoPlanilla::with(['empresa', 'cerradoPor'])
            ->when($empresaId, function ($query) use ($empresaId) {
                return $query->where('empresa_id', $empresaId);
            })
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(15);

        $empresas = Empresa::all();

        return view('admin.periodos.index', compact('periodos', 'empresas', 'empresaId'));
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        $empresas = Empresa::all();
        return view('admin.periodos.create', compact('empresas'));
    }

    /**
     * Guardar nuevo período
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'empresa_id' => 'required|exists:empresas,id',
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $periodo = PeriodoPlanilla::create($request->all());

        return redirect()->route('admin.periodos.show', $periodo->id)
            ->with('success', 'Período creado exitosamente. Ahora puede cerrarlo para procesar la planilla.');
    }

    /**
     * Ver detalle del período
     */
    public function show($id)
    {
        $periodo = PeriodoPlanilla::with(['empresa', 'cerradoPor', 'resumenes.empleado.departamento'])
            ->findOrFail($id);

        // Estadísticas generales
        $stats = [
            'total_empleados' => $periodo->resumenes->count(),
            'total_dias_falta' => $periodo->resumenes->sum('total_dias_falta'),
            'total_tardanzas' => $periodo->resumenes->sum('total_tardanzas'),
            'total_horas_trabajadas' => $periodo->resumenes->sum('total_horas_trabajadas'),
            'total_horas_extra' => $periodo->resumenes->sum(function ($r) {
                return $r->total_horas_extra_25 + $r->total_horas_extra_50 + $r->total_horas_extra_100;
            }),
            'empleados_perfectos' => $periodo->resumenes->filter(fn($r) => $r->esPerfecto())->count(),
            'empleados_con_problemas' => $periodo->resumenes->filter(fn($r) => $r->tieneProblemas())->count(),
        ];

        return view('admin.periodos.show', compact('periodo', 'stats'));
    }

    /**
     * Cerrar período (despachar el job)
     */
    public function cerrar($id)
    {
        $periodo = PeriodoPlanilla::findOrFail($id);

        if (!$periodo->puedeCerrarse()) {
            return redirect()->back()
                ->with('error', "El período no puede cerrarse. Estado actual: {$periodo->estado}");
        }

        // Despachar el job para procesamiento en segundo plano
        ProcesarPlanillaJob::dispatch($periodo);

        return redirect()->route('admin.periodos.show', $periodo->id)
            ->with('success', 'El procesamiento del período ha sido iniciado. Esto puede tomar varios minutos dependiendo del número de empleados.');
    }

    /**
     * Reabrir un período cerrado
     */
    public function reabrir($id)
    {
        $periodo = PeriodoPlanilla::findOrFail($id);

        if ($periodo->estado !== 'cerrado') {
            return redirect()->back()
                ->with('error', 'Solo se pueden reabrir períodos cerrados.');
        }

        // Eliminar todos los resúmenes generados
        $periodo->resumenes()->delete();

        // Reabrir el período
        $periodo->update([
            'estado' => 'abierto',
            'fecha_cierre' => null,
            'cerrado_por' => null,
            'total_empleados_procesados' => 0,
        ]);

        return redirect()->route('admin.periodos.show', $periodo->id)
            ->with('success', 'El período ha sido reabierto. Los resúmenes anteriores fueron eliminados.');
    }

    /**
     * Exportar a Excel
     */
    public function exportar($id)
    {
        $periodo = PeriodoPlanilla::with(['resumenes.empleado.departamento'])->findOrFail($id);

        if ($periodo->estado !== 'cerrado') {
            return redirect()->back()
                ->with('error', 'Solo se pueden exportar períodos cerrados.');
        }

        $data = $periodo->resumenes->map(function ($resumen) {
            return $resumen->toExportArray();
        })->toArray();

        // Crear archivo Excel
        $fileName = "planilla_{$periodo->codigo}_" . now()->format('Ymd_His') . ".xlsx";

        return $this->generarExcel($data, $fileName, $periodo);
    }

    /**
     * Generar archivo Excel (método auxiliar)
     */
    private function generarExcel($data, $fileName, $periodo)
    {
        // Aquí puedes usar Laravel Excel o generar manualmente
        // Por ahora retornamos un CSV simple como ejemplo
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() use ($data, $periodo) {
            $file = fopen('php://output', 'w');
            
            // Encabezado del reporte
            fputcsv($file, ['PLANILLA DE ASISTENCIA']);
            fputcsv($file, ['Empresa:', $periodo->empresa->nombre_empresa ?? '']);
            fputcsv($file, ['Período:', $periodo->nombre]);
            fputcsv($file, ['Desde:', $periodo->fecha_inicio->format('d/m/Y')]);
            fputcsv($file, ['Hasta:', $periodo->fecha_fin->format('d/m/Y')]);
            fputcsv($file, []); // Línea vacía

            // Encabezados de columnas
            fputcsv($file, array_keys($data[0] ?? []));

            // Datos
            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Eliminar período
     */
    public function destroy($id)
    {
        $periodo = PeriodoPlanilla::findOrFail($id);

        if (!$periodo->puedeEditarse()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar un período que está siendo procesado o ya fue cerrado.');
        }

        $periodo->delete();

        return redirect()->route('admin.periodos.index')
            ->with('success', 'Período eliminado exitosamente.');
    }

    /**
     * Ver resumen individual de un empleado
     */
    public function verEmpleado($periodoId, $empleadoId)
    {
        $periodo = PeriodoPlanilla::findOrFail($periodoId);
        $resumen = ResumenMensualAsistencia::where('periodo_id', $periodoId)
            ->where('empleado_id', $empleadoId)
            ->with(['empleado.departamento', 'periodo'])
            ->firstOrFail();

        return view('admin.periodos.empleado', compact('resumen', 'periodo'));
    }
}
