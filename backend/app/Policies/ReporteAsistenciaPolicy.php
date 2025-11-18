<?php

namespace App\Policies;

use App\Models\ReporteAsistencia;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReporteAsistenciaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('browse_reportes_asistencia');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ReporteAsistencia $reporte): bool
    {
        return $user->hasPermission('read_reportes_asistencia');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('add_reportes_asistencia');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ReporteAsistencia $reporte): bool
    {
        return $user->hasPermission('delete_reportes_asistencia');
    }
}
