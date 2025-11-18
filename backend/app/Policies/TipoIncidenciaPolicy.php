<?php

namespace App\Policies;

use App\Models\TipoIncidencia;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TipoIncidenciaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('browse_tipos_incidencia');
    }

    public function view(User $user, TipoIncidencia $tipo)
    {
        return $user->hasPermission('read_tipos_incidencia');
    }

    public function create(User $user)
    {
        return $user->hasPermission('add_tipos_incidencia');
    }

    public function update(User $user, TipoIncidencia $tipo)
    {
        return $user->hasPermission('edit_tipos_incidencia');
    }

    public function delete(User $user, TipoIncidencia $tipo)
    {
        return $user->hasPermission('delete_tipos_incidencia');
    }
}
