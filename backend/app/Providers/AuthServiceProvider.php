<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\AsignacionHorario;
use App\Models\Departamento;
use App\Models\Empresa;
use App\Models\DispositivoEmpleado;
use App\Models\Dispositivo;
use App\Models\Empleado;
use App\Models\RegistroAsistencia;
use App\Models\Horario;
use App\Models\ReporteAsistencia as ModelsReporteAsistencia;
use App\Models\Incidencia;
use App\Models\Person;
use App\Models\User;
use App\Models\TipoIncidencia;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Empresa::class => \App\Policies\EmpresaPolicy::class,
        Departamento::class => \App\Policies\DepartamentoPolicy::class,
        Horario::class => \App\Policies\HorarioPolicy::class,
        AsignacionHorario::class => \App\Policies\AsignacionHorarioPolicy::class,
        RegistroAsistencia::class => \App\Policies\RegistroAsistenciaPolicy::class,
        Empleado::class => \App\Policies\EmpleadoPolicy::class,
        Dispositivo::class => \App\Policies\DispositivoPolicy::class,
        TipoIncidencia::class => \App\Policies\TipoIncidenciaPolicy::class,
        Incidencia::class => \App\Policies\IncidenciaPolicy::class,
        DispositivoEmpleado::class => \App\Policies\DispositivoEmpleadoPolicy::class,
        ModelsReporteAsistencia::class => \App\Policies\ReporteAsistenciaPolicy::class,
        Person::class => \App\Policies\PersonPolicy::class,
        User::class => \App\Policies\UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
