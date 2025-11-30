<?php

namespace App\Policies;

use App\Models\DispositivoEmpleado;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DispositivoEmpleadoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     * Este método corresponde al permiso 'browse' y es llamado por el Trait para autorizar el listado.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('browse_dispositivo_empleado');
    }

    /**
     * Determine whether the user can view the model. (read)
     */
    public function view(User $user, DispositivoEmpleado $dispositivoEmpleado): bool
    {
        return $user->hasPermission('read_dispositivo_empleado');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('add_dispositivo_empleado');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DispositivoEmpleado $dispositivoEmpleado): bool
    {
        return $user->hasPermission('edit_dispositivo_empleado');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DispositivoEmpleado $dispositivoEmpleado): bool
    {
        return $user->hasPermission('delete_dispositivo_empleado');
    }
}
