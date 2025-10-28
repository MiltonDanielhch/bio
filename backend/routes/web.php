<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ErrorController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AsignacionHorarioController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\HorarioController;
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

    // Rutas de Voyager (no tocar)
    Voyager::routes();

    // ──────────────── EMPRESAS ────────────────
    Route::prefix('empresas')->group(function () {
        Route::get('/', [EmpresaController::class, 'index'])->name('admin.empresas.index');
        Route::get('/ajax/list', [EmpresaController::class, 'list'])->name('admin.empresas.ajax.list');
        Route::get('/create', [EmpresaController::class, 'create'])->name('admin.empresas.create');
        Route::post('/', [EmpresaController::class, 'store'])->name('admin.empresas.store');
        Route::get('/{empresa}/edit', [EmpresaController::class, 'edit'])->name('admin.empresas.edit');
        Route::put('/{empresa}', [EmpresaController::class, 'update'])->name('admin.empresas.update');
        Route::delete('/{empresa}', [EmpresaController::class, 'destroy'])->name('admin.empresas.destroy');
        Route::get('/{empresa}', [EmpresaController::class, 'show'])->name('admin.empresas.show');
    });

    Route::prefix('sucursales')->group(function () {
        Route::get('/', [SucursalController::class, 'index'])->name('admin.sucursales.index');
        Route::get('/ajax/list', [SucursalController::class, 'list'])->name('admin.sucursales.ajax.list');
        Route::get('/create', [SucursalController::class, 'create'])->name('admin.sucursales.create');
        Route::post('/', [SucursalController::class, 'store'])->name('admin.sucursales.store');
        Route::get('/{sucursal}/edit', [SucursalController::class, 'edit'])->name('admin.sucursales.edit');
        Route::put('/{sucursal}', [SucursalController::class, 'update'])->name('admin.sucursales.update');
        Route::delete('/{sucursal}', [SucursalController::class, 'destroy'])->name('admin.sucursales.destroy');
        Route::get('/{sucursal}', [SucursalController::class, 'show'])->name('admin.sucursales.show');
    });

    Route::prefix('departamentos')->group(function () {
        Route::get('/', [DepartamentoController::class, 'index'])->name('admin.departamentos.index');
        Route::get('/ajax/list', [DepartamentoController::class, 'list'])->name('admin.departamentos.ajax.list');
        Route::get('/create', [DepartamentoController::class, 'create'])->name('admin.departamentos.create');
        Route::post('/', [DepartamentoController::class, 'store'])->name('admin.departamentos.store');
        Route::get('/{departamento}/edit', [DepartamentoController::class, 'edit'])->name('admin.departamentos.edit');
        Route::put('/{departamento}', [DepartamentoController::class, 'update'])->name('admin.departamentos.update');
        Route::delete('/{departamento}', [DepartamentoController::class, 'destroy'])->name('admin.departamentos.destroy');
        Route::get('/{departamento}', [DepartamentoController::class, 'show'])->name('admin.departamentos.show');
    });

    Route::prefix('horarios')->group(function () {
        Route::get('/', [HorarioController::class, 'index'])->name('admin.horarios.index');
        Route::get('/ajax/list', [HorarioController::class, 'list'])->name('admin.horarios.ajax.list');
        Route::get('/create', [HorarioController::class, 'create'])->name('admin.horarios.create');
        Route::post('/', [HorarioController::class, 'store'])->name('admin.horarios.store');
        Route::get('/{horario}/edit', [HorarioController::class, 'edit'])->name('admin.horarios.edit');
        Route::put('/{horario}', [HorarioController::class, 'update'])->name('admin.horarios.update');
        Route::delete('/{horario}', [HorarioController::class, 'destroy'])->name('admin.horarios.destroy');
        Route::get('/{horario}', [HorarioController::class, 'show'])->name('admin.horarios.show');
    });

    Route::prefix('asignacion-horarios')->group(function () {
        Route::get('/', [AsignacionHorarioController::class, 'index'])->name('admin.asignacion-horarios.index');
        Route::get('/ajax/list', [AsignacionHorarioController::class, 'list'])->name('admin.asignacion-horarios.ajax.list');
        Route::get('/create', [AsignacionHorarioController::class, 'create'])->name('admin.asignacion-horarios.create');
        Route::post('/', [AsignacionHorarioController::class, 'store'])->name('admin.asignacion-horarios.store');
        Route::get('/{asignacionHorario}/edit', [AsignacionHorarioController::class, 'edit'])->name('admin.asignacion-horarios.edit');
        Route::put('/{asignacionHorario}', [AsignacionHorarioController::class, 'update'])->name('admin.asignacion-horarios.update');
        Route::delete('/{asignacionHorario}', [AsignacionHorarioController::class, 'destroy'])->name('admin.asignacion-horarios.destroy');
        Route::get('/{asignacionHorario}', [AsignacionHorarioController::class, 'show'])->name('admin.asignacion-horarios.show');
    });

    Route::prefix('reportes-asistencia')->group(function () {
        Route::get('/', [ReporteAsistenciaController::class, 'index'])->name('admin.reportes-asistencia.index');
        Route::get('/ajax/list', [ReporteAsistenciaController::class, 'list'])->name('admin.reportes-asistencia.ajax.list');
        Route::get('/create', [ReporteAsistenciaController::class, 'create'])->name('admin.reportes-asistencia.create');
        Route::post('/', [ReporteAsistenciaController::class, 'store'])->name('admin.reportes-asistencia.store');
        Route::get('/{reporte}/edit', [ReporteAsistenciaController::class, 'edit'])->name('admin.reportes-asistencia.edit');
        Route::put('/{reporte}', [ReporteAsistenciaController::class, 'update'])->name('admin.reportes-asistencia.update');
        Route::delete('/{reporte}', [ReporteAsistenciaController::class, 'destroy'])->name('admin.reportes-asistencia.destroy');
        Route::get('/{reporte}', [ReporteAsistenciaController::class, 'show'])->name('admin.reportes-asistencia.show');
        Route::get('/{reporte}/download', [ReporteAsistenciaController::class, 'download'])->name('admin.reportes-asistencia.download');
    });

    // ──────────────── PERSONAS ────────────────
    Route::prefix('people')->group(function () {
        Route::get('/', [PersonController::class, 'index'])->name('voyager.people.index');
        Route::get('/ajax/list', [PersonController::class, 'list'])->name('voyager.people.ajax.list');
        Route::post('/', [PersonController::class, 'store'])->name('voyager.people.store');
        Route::put('/{id}', [PersonController::class, 'update'])->name('voyager.people.update');
    });

    // ──────────────── USUARIOS ────────────────
    Route::prefix('users')->group(function () {
        Route::get('/ajax/list', [UserController::class, 'list'])->name('voyager.users.ajax.list');
        Route::post('/store', [UserController::class, 'store'])->name('voyager.users.store');
        Route::put('/{id}', [UserController::class, 'update'])->name('voyager.users.update');
        Route::delete('/{id}/deleted', [UserController::class, 'destroy'])->name('voyager.users.destroy');
    });

    // ──────────────── ROLES ────────────────
    Route::prefix('roles')->group(function () {
        Route::get('/ajax/list', [RoleController::class, 'list'])->name('voyager.roles.ajax.list');
    });

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
