<?php

namespace App\Policies;

use App\Models\ReporteAsistencia;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReporteAsistenciaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user){
        return $user->hasPermission('browse_reportes_asistencia');
    }

    public function view(User $user, ReporteAsistencia $r) {
        return $user->hasPermission('read_reportes_asistencia');
    }

    public function create(User $user){
        return $user->hasPermission('add_reportes_asistencia');
    }

    public function update(User $user, ReporteAsistencia $r) {
        return $user->hasPermission('edit_reportes_asistencia');
    }

    public function delete(User $user, ReporteAsistencia $r) {
        return $user->hasPermission('delete_reportes_asistencia');
    }

}
