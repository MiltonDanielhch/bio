<?php

namespace App\Http\Controllers;

use App\Models\RegistroAsistencia;
use App\Models\Empleado;
use App\Models\Dispositivo;
use App\Traits\ManagesCrud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class RegistroAsistenciaController extends Controller
{
    use ManagesCrud;

    protected $model = RegistroAsistencia::class;
    protected $browseView = 'admin.registros_asistencia.browse';
    protected $listView = 'admin.registros_asistencia.list';
    protected $with = ['empleado', 'dispositivo'];
    protected $orderBy = ['fecha_hora', 'desc'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function applySearch(Builder $query, string $search): Builder
    {
        return $query->when($search, function ($query) use ($search) {
            $query->whereHas('empleado', function ($q) use ($search) {
                $q->where('nombres', 'like', "%$search%")
                    ->orWhere('apellidos', 'like', "%$search%")
                    ->orWhere('codigo_empleado', 'like', "%$search%");
            })
                ->orWhereHas('dispositivo', function ($q) use ($search) {
                    $q->where('nombre_dispositivo', 'like', "%$search%");
                });
        });
    }

    public function create()
    {
        $this->authorize('create', RegistroAsistencia::class);
        $empleados = Empleado::where('estado', 'activo')->orderBy('nombres')->get();
        $dispositivos = Dispositivo::where('estado', 'activo')->orderBy('nombre_dispositivo')->get();
        return view('admin.registros_asistencia.edit-add', [
            'registro' => new RegistroAsistencia(),
            'empleados' => $empleados,
            'dispositivos' => $dispositivos,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', RegistroAsistencia::class);
        $data = $this->validateAndPrepareData($request);

        RegistroAsistencia::create($data);

        return redirect()->route('admin.registros-asistencia.index')
            ->with(['message' => 'Registro de asistencia creado exitosamente.', 'alert-type' => 'success']);
    }

    public function edit(RegistroAsistencia $registro)
    {
        $this->authorize('update', $registro);
        $empleados = Empleado::where('estado', 'activo')->orderBy('nombres')->get();
        $dispositivos = Dispositivo::where('estado', 'activo')->orderBy('nombre_dispositivo')->get();

        return view('admin.registros_asistencia.edit-add', [
            'registro' => $registro,
            'empleados' => $empleados,
            'dispositivos' => $dispositivos,
        ]);
    }

    public function update(Request $request, RegistroAsistencia $registro)
    {
        $this->authorize('update', $registro);
        $data = $this->validateAndPrepareData($request);

        $registro->update($data);

        return redirect()->route('admin.registros-asistencia.index')
            ->with(['message' => 'Registro de asistencia actualizado exitosamente.', 'alert-type' => 'success']);
    }

    public function destroy(RegistroAsistencia $registro)
    {
        $this->authorize('delete', $registro);
        try {
            $registro->delete();
            return redirect()->route('admin.registros-asistencia.index')
                ->with(['message' => 'Registro de asistencia eliminado exitosamente.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            Log::error("Error al eliminar registro de asistencia {$registro->id}: " . $e->getMessage());
            return redirect()->route('admin.registros-asistencia.index')
                ->with(['message' => 'Error al eliminar el registro de asistencia.', 'alert-type' => 'error']);
        }
    }

    /**
     * Valida y prepara los datos del request.
     */
    private function validateAndPrepareData(Request $request): array
    {
        $validated = $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'dispositivo_id' => 'required|exists:dispositivos,id',
            'tipo_marcaje' => 'required|in:entrada,salida,entrada_almuerzo,salida_almuerzo,general',
            'fecha_local' => 'required|date',
            'hora_local' => 'required|date_format:H:i:s',
            'tipo_verificacion' => 'required|in:huella,rostro,tarjeta,manual,clave',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            'precision_ubicacion' => 'nullable|numeric',
            'confianza_verificacion' => 'nullable|numeric',
            'observaciones' => 'nullable|string',
        ]);

        // Combinar fecha y hora en un solo campo 'fecha_hora' usando Carbon para más seguridad
        $validated['fecha_hora'] = Carbon::parse($validated['fecha_local'] . ' ' . $validated['hora_local'])->toDateTimeString();

        return $validated;
    }
}
