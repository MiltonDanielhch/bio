<?php

namespace App\Policies;

use App\Models\Incidencia;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IncidenciaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('browse_incidencias');
    }

    public function view(User $user, Incidencia $incidencia)
    {
        return $user->hasPermission('read_incidencias');
    }

    public function create(User $user)
    {
        return $user->hasPermission('add_incidencias');
    }

    public function update(User $user, Incidencia $incidencia)
    {
        return $user->hasPermission('edit_incidencias');
    }

    public function delete(User $user, Incidencia $incidencia)
    {
        return $user->hasPermission('delete_incidencias');
    }
}
