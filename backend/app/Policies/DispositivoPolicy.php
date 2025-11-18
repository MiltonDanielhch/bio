<?php

namespace App\Policies;

use App\Models\Dispositivo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DispositivoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('browse_dispositivos');
    }

    public function view(User $user, Dispositivo $dispositivo)
    {
        return $user->hasPermission('read_dispositivos');
    }

    public function create(User $user)
    {
        return $user->hasPermission('add_dispositivos');
    }

    public function update(User $user, Dispositivo $dispositivo)
    {
        return $user->hasPermission('edit_dispositivos');
    }

    public function delete(User $user, Dispositivo $dispositivo)
    {
        return $user->hasPermission('delete_dispositivos');
    }
}
