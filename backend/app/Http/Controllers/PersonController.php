<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Traits\ManagesCrud;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PersonController extends Controller
{
    use ManagesCrud;

    protected $model = Person::class;
    protected $browseView = 'administrations.people.browse';
    protected $listView = 'administrations.people.list';
    protected $orderBy = ['id', 'desc'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function applySearch(Builder $query, string $search): Builder
    {
        // Sub-consulta para el nombre completo
        $fullNameRaw = "TRIM(CONCAT(COALESCE(first_name, ''), ' ', COALESCE(middle_name, ''), ' ', COALESCE(paternal_surname, ''), ' ', COALESCE(maternal_surname, '')))";

        return $query->select('people.*') // Evita ambigüedad
            ->selectRaw("$fullNameRaw as full_name")
            ->where(function ($q) use ($search, $fullNameRaw) {
                // Búsqueda numérica exacta (id o ci)
                if (is_numeric($search)) {
                    $q->where('id', $search)
                      ->orWhere('ci', 'like', "%{$search}%");
                }

                // Búsqueda textual parcial
                $q->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('paternal_surname', 'like', "%{$search}%")
                  ->orWhere('maternal_surname', 'like', "%{$search}%")
                  ->orWhereRaw("{$fullNameRaw} like ?", ["%{$search}%"]);
            });
    }

    public function create()
    {
        $this->authorize('create', Person::class);
        return view('administrations.people.edit-add', ['person' => new Person()]);
    }


    public function store(Request $request)
    {
        $this->authorize('create', Person::class);
        $data = $this->validateRequest($request);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                $storageController = new StorageController();
                $data['image'] = $storageController->store_image($request->file('image'), 'people');
            }

            Person::create($data);

            DB::commit();
            return redirect()->route('admin.people.index')->with(['message' => 'Registrado exitosamente', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error("Error al crear persona: " . $th->getMessage());
            return redirect()->route('admin.people.index')->with(['message' => 'Ocurrió un error al registrar.', 'alert-type' => 'error']);
        }
    }

    public function edit(Person $person)
    {
        $this->authorize('update', $person);
        return view('administrations.people.edit-add', compact('person'));
    }

    public function update(Request $request, Person $person)
    {
        $this->authorize('update', $person);
        $data = $this->validateRequest($request, $person->id);

        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                $storageController = new StorageController();
                // Opcional: eliminar imagen anterior si existe
                // if ($person->image) { Storage::delete($person->image); }
                $data['image'] = $storageController->store_image($request->file('image'), 'people');
            }

            $person->update($data);

            DB::commit();
            return redirect()->route('admin.people.index')->with(['message' => 'Actualizado exitosamente', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error("Error al actualizar persona {$person->id}: " . $th->getMessage());
            return redirect()->route('admin.people.index')->with(['message' => 'Ocurrió un error al actualizar.', 'alert-type' => 'error']);
        }
    }

    private function validateRequest(Request $request, $personId = null): array
    {
        return $request->validate([
            'ci' => ['nullable', 'string', 'max:20', Rule::unique('people')->ignore($personId)],
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'paternal_surname' => 'nullable|string|max:100',
            'maternal_surname' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|in:Masculino,Femenino,otro',
            'email' => ['nullable', 'email', 'max:191', Rule::unique('people')->ignore($personId)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,bmp,webp|max:2048',
            'status' => 'nullable|boolean',
        ]);
    }
}
