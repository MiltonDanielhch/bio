<?php

namespace App\Policies;

use App\Models\Empleado;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmpleadoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('browse_empleados');
    }

    public function view(User $user, Empleado $empleado)
    {
        // Asumiendo que 'read' es el permiso para ver un solo registro
        return $user->hasPermission('read_empleados');
    }

    public function create(User $user)
    {
        return $user->hasPermission('add_empleados');
    }

    public function update(User $user, Empleado $empleado)
    {
        return $user->hasPermission('edit_empleados');
    }

    public function delete(User $user, Empleado $empleado)
    {
        return $user->hasPermission('delete_empleados');
    }
}
