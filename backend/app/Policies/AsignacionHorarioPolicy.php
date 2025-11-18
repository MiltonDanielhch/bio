<?php

namespace App\Policies;

use App\Models\AsignacionHorario;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AsignacionHorarioPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('browse_asignacion_horarios');
    }

    public function view(User $user, AsignacionHorario $asignacionHorario)
    {
        return $user->hasPermission('read_asignacion_horarios');
    }

    public function create(User $user)
    {
        return $user->hasPermission('add_asignacion_horarios');
    }

    public function update(User $user, AsignacionHorario $asignacionHorario)
    {
        return $user->hasPermission('edit_asignacion_horarios');
    }

    public function delete(User $user, AsignacionHorario $asignacionHorario)
    {
        return $user->hasPermission('delete_asignacion_horarios');
    }
}
