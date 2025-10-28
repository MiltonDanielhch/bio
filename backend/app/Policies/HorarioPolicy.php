<?php

namespace App\Policies;

use App\Models\Horario;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HorarioPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user){
        return $user->hasPermission('browse_horarios');
    }

    public function view(User $user, Horario $h) {
        return $user->hasPermission('read_horarios');
    }

    public function create(User $user){
        return $user->hasPermission('add_horarios');
    }

    public function update(User $user, Horario $h) {
        return $user->hasPermission('edit_horarios');
    }

    public function delete(User $user, Horario $h) {
        return $user->hasPermission('delete_horarios');
    }

}
