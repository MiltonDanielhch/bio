<?php

namespace App\Policies;

use App\Models\DispositivoEmpleado;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DispositivoEmpleadoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('browse_dispositivo_empleados');
    }

    public function view(User $user, DispositivoEmpleado $map)
    {
        return $user->hasPermission('read_dispositivo_empleados');
    }

    public function create(User $user)
    {
        return $user->hasPermission('add_dispositivo_empleados');
    }

    public function update(User $user, DispositivoEmpleado $map)
    {
        return $user->hasPermission('edit_dispositivo_empleados');
    }

    public function delete(User $user, DispositivoEmpleado $map)
    {
        return $user->hasPermission('delete_dispositivo_empleados');
    }
}
