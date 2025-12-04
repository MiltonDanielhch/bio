<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AsignacionHorarioController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\Admin\DispositivoController; // Importa el nuevo controlador
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\RegistroAsistenciaController;
use App\Http\Controllers\ReporteAsistenciaController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\TipoIncidenciaController;
use TCG\Voyager\Facades\Voyager;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirección raíz y login
Route::redirect('login', 'admin/login')->name('login');
Route::redirect('/', 'admin');

// Grupo principal con middleware personalizado
Route::prefix('admin')->middleware(['loggin', 'system'])->group(function () {

    // Sobrescribimos la ruta del dashboard de Voyager para usar nuestro controlador personalizado.
    // Debe estar ANTES de Voyager::routes() para tener prioridad.

    // Rutas de Voyager (no tocar)
    Voyager::routes();
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('voyager.dashboard');

    // ───────────────── RUTAS DE RECURSOS REFACTORIZADAS ─────────────────

    // Empresas
    Route::get('empresas/ajax/list', [EmpresaController::class, 'list'])->name('admin.empresas.ajax.list');
    Route::resource('empresas', EmpresaController::class)->names('admin.empresas');

    // Sucursales
    Route::get('sucursales/ajax/list', [SucursalController::class, 'list'])->name('admin.sucursales.ajax.list');
    Route::resource('sucursales', SucursalController::class)->names('admin.sucursales');

    // Departamentos
    Route::get('departamentos/ajax/list', [DepartamentoController::class, 'list'])->name('admin.departamentos.ajax.list');
    Route::resource('departamentos', DepartamentoController::class)->names('admin.departamentos');

    // Horarios
    Route::get('horarios/ajax/list', [HorarioController::class, 'list'])->name('admin.horarios.ajax.list');
    Route::resource('horarios', HorarioController::class)->names('admin.horarios');

    // Asignación de Horarios
    Route::get('asignacion-horarios/ajax/list', [AsignacionHorarioController::class, 'list'])->name('admin.asignacion-horarios.ajax.list');
    Route::resource('asignacion-horarios', AsignacionHorarioController::class)->names('admin.asignacion-horarios');

    // Reportes de Asistencia
    Route::get('reportes-asistencia/ajax/list', [ReporteAsistenciaController::class, 'list'])->name('admin.reportes-asistencia.ajax.list');
    Route::get('reportes-asistencia/{reporte}/download', [ReporteAsistenciaController::class, 'download'])->name('admin.reportes-asistencia.download');
    Route::resource('reportes-asistencia', ReporteAsistenciaController::class)->except(['edit', 'update'])->names('admin.reportes-asistencia');

    // Registros de Asistencia
    Route::get('registros-asistencia/ajax/list', [RegistroAsistenciaController::class, 'list'])->name('admin.registros-asistencia.ajax.list');
    Route::resource('registros-asistencia', RegistroAsistenciaController::class)->except(['show'])->names('admin.registros-asistencia');

    // Empleados
    Route::get('empleados/ajax/list', [EmpleadoController::class, 'list'])->name('admin.empleados.ajax.list');
    Route::resource('empleados', EmpleadoController::class)->except(['show'])->names('admin.empleados');

    // Dispositivos
    Route::get('dispositivos/ajax/list', [DispositivoController::class, 'list'])->name('admin.dispositivos.ajax.list');
    Route::post('dispositivos/{dispositivo}/test-connection', [DispositivoController::class, 'testConnection'])->name('admin.dispositivos.test_connection');
    Route::post('dispositivos/{dispositivo}/sync-now', [DispositivoController::class, 'syncNow'])->name('admin.dispositivos.sync_now');
    Route::post('dispositivos/{dispositivo}/sync-users', [DispositivoController::class, 'syncUsers'])->name('admin.dispositivos.sync_users')->withTrashed();
    Route::get('dispositivos/{dispositivo}/assign-employees', [DispositivoController::class, 'assignEmployees'])->name('admin.dispositivos.assign_employees');
    Route::post('dispositivos/{dispositivo}/assign-employees', [DispositivoController::class, 'storeEmployees'])->name('admin.dispositivos.store_employees');
    Route::resource('dispositivos', DispositivoController::class)->names('admin.dispositivos');

    // Mapeo Dispositivo <-> Empleado
    Route::get('dispositivo-empleado/ajax/list', [App\Http\Controllers\Admin\DispositivoEmpleadoController::class, 'list'])->name('admin.dispositivo-empleado.ajax.list');
    Route::resource('dispositivo-empleado', App\Http\Controllers\Admin\DispositivoEmpleadoController::class)->except(['show'])->names('admin.dispositivo-empleado');

    // Tipos de Incidencia
    Route::get('tipos-incidencia/ajax/list', [TipoIncidenciaController::class, 'list'])->name('admin.tipos-incidencia.ajax.list');
    Route::resource('tipos-incidencia', TipoIncidenciaController::class)->except(['show'])->names('admin.tipos-incidencia');

    // Incidencias
    Route::get('incidencias/ajax/list', [\App\Http\Controllers\IncidenciaController::class, 'list'])->name('admin.incidencias.ajax.list');
    Route::resource('incidencias', \App\Http\Controllers\IncidenciaController::class)->except(['show'])->names('admin.incidencias');

    // ───────────────── RUTAS LEGACY (REFACTORIZADAS) ─────────────────

    // Personas (Usuarios de Voyager)
    Route::get('people/ajax/list', [PersonController::class, 'list'])->name('admin.people.ajax.list');
    Route::resource('people', PersonController::class)->except(['show'])->names('admin.people');

    // Usuarios (del sistema, si es diferente a Personas)
    Route::get('users/ajax/list', [UserController::class, 'list'])->name('admin.users.ajax.list');
    Route::resource('users', UserController::class)->except(['show'])->names('admin.users');

    // ──────────────── ROLES ────────────────
    // Nota: Voyager maneja las rutas de roles. Si necesitas una lista AJAX, esta ruta está bien.
    // Si se refactoriza a un CRUD completo, se usaría Route::resource.
    Route::get('roles/ajax/list', [RoleController::class, 'list'])->name('admin.roles.ajax.list');


    // ──────────────── AJAX GENÉRICO ────────────────
    Route::prefix('ajax')->group(function () {
        Route::get('/personList', [AjaxController::class, 'personList']);
        Route::post('/person/store', [AjaxController::class, 'personStore']);
    });

    // ──────────────── UTILIDADES ────────────────
    Route::get('/clear-cache', function () {
        Artisan::call('optimize:clear');
        return redirect('/admin/profile')->with([
            'message' => 'Cache eliminada.',
            'alert-type' => 'success'
        ]);
    })->name('clear.cache');
});
