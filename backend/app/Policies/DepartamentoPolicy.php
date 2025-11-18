<?php

namespace App\Policies;

use App\Models\Departamento;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartamentoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('browse_departamentos');
    }

    public function view(User $user, Departamento $departamento)
    {
        return $user->hasPermission('read_departamentos');
    }

    public function create(User $user)
    {
        return $user->hasPermission('add_departamentos');
    }

    public function update(User $user, Departamento $departamento)
    {
        return $user->hasPermission('edit_departamentos');
    }

    public function delete(User $user, Departamento $departamento)
    {
        return $user->hasPermission('delete_departamentos');
    }
}
