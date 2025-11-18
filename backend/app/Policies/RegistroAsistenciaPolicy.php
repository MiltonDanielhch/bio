<?php

namespace App\Policies;

use App\Models\RegistroAsistencia;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegistroAsistenciaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('browse_registros_asistencia');
    }

    public function view(User $user, RegistroAsistencia $registroAsistencia)
    {
        return $user->hasPermission('read_registros_asistencia');
    }

    public function create(User $user)
    {
        return $user->hasPermission('add_registros_asistencia');
    }

    public function update(User $user, RegistroAsistencia $registroAsistencia)
    {
        return $user->hasPermission('edit_registros_asistencia');
    }

    public function delete(User $user, RegistroAsistencia $registroAsistencia)
    {
        return $user->hasPermission('delete_registros_asistencia');
    }
}
