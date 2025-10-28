<?php

namespace App\Policies;

use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SucursalPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user){
        return $user->hasPermission('browse_sucursales');
    }

    public function view(User $user, Sucursal $s) {
        return $user->hasPermission('read_sucursales');
    }

    public function create(User $user)       {
        return $user->hasPermission('add_sucursales');
    }

    public function update(User $user, Sucursal $s) {
        return $user->hasPermission('edit_sucursales');
    }

    public function delete(User $user, Sucursal $s) {
        return $user->hasPermission('delete_sucursales');
    }

}
