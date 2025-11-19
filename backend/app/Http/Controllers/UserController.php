<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\User;
use App\Traits\ManagesCrud;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use TCG\Voyager\Models\Role;

class UserController extends Controller
{
    use ManagesCrud;

    protected $model = User::class;
    protected $browseView = 'vendor.voyager.users.browse';
    protected $listView = 'vendor.voyager.users.list';
    protected $with = ['person', 'role'];
    protected $orderBy = ['id', 'desc'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function applySearch(Builder $query, string $search): Builder
    {
        // Si el usuario no es Super Admin, no puede ver al Super Admin (role_id 1)
        if (Auth::user()->role_id !== 1) {
            $query->where('role_id', '!=', 1);
        }

        return $query->when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%")
              ->orWhereHas('person', function ($personQuery) use ($search) {
                  $personQuery->where('first_name', 'like', "%$search%")
                              ->orWhere('paternal_surname', 'like', "%$search%")
                              ->orWhere('ci', 'like', "%$search%");
              });
        });
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $roles = Role::where('id', '!=', 1)->get(); // No se puede asignar el rol de Super Admin
        return view('vendor.voyager.users.edit-add', [
            'user' => new User(),
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        $data = $this->validateRequest($request);

        $person = Person::findOrFail($data['person_id']);

        DB::beginTransaction();
        try {
            User::create([
                'person_id' => $data['person_id'],
                'name' => $person->first_name . ' ' . $person->paternal_surname,
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $data['role_id'],
                'avatar' => 'users/default.png',
            ]);
            DB::commit();
            return redirect()->route('admin.users.index')->with(['message' => 'Usuario registrado exitosamente.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error al crear usuario: " . $e->getMessage());
            return redirect()->route('admin.users.index')->with(['message' => 'Ocurrió un error al registrar el usuario.', 'alert-type' => 'error']);
        }
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $roles = Role::where('id', '!=', 1)->get();
        return view('vendor.voyager.users.edit-add', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $data = $this->validateRequest($request, $user->id);

        DB::beginTransaction();
        try {
            $updateData = [
                'status' => $data['status'] ?? $user->status,
                'role_id' => $data['role_id'] ?? $user->role_id,
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $user->update($updateData);

            DB::commit();
            return redirect()->route('admin.users.index')->with(['message' => 'Usuario actualizado exitosamente.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error al actualizar usuario {$user->id}: " . $e->getMessage());
            return redirect()->route('admin.users.index')->with(['message' => 'Ocurrió un error al actualizar.', 'alert-type' => 'error']);
        }
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        DB::beginTransaction();
        try {
            $user->delete();
            DB::commit();
            return redirect()->route('admin.users.index')->with(['message' => 'Usuario eliminado exitosamente.', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Error al eliminar usuario {$user->id}: " . $e->getMessage());
            return redirect()->route('admin.users.index')->with(['message' => 'Ocurrió un error al eliminar.', 'alert-type' => 'error']);
        }
    }

    private function validateRequest(Request $request, $userId = null): array
    {
        $passwordRules = $userId ? ['nullable', 'min:8', 'confirmed'] : ['required', 'min:8', 'confirmed'];

        return $request->validate([
            'person_id' => $userId ? 'nullable|exists:people,id' : 'required|exists:people,id',
            'email' => ['required', 'email', Rule::unique('users')->ignore($userId)],
            'password' => $passwordRules,
            'role_id' => 'required|exists:roles,id',
            'status' => 'nullable|boolean',
        ]);
    }
}
