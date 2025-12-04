<?php

namespace Tests\Feature;

use App\Models\Departamento;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use TCG\Voyager\Models\Role;
use Tests\TestCase;

class DepartamentoCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private Sucursal $sucursal;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator']);
        $this->adminUser = User::factory()->create(['role_id' => $adminRole->id]);
        
        Gate::before(function ($user, $ability) {
            if ($user->role_id === Role::where('name', 'admin')->first()?->id) {
                return true;
            }
        });

        $this->sucursal = Sucursal::factory()->create();
    }

    public function test_admin_can_create_departamento()
    {
        $data = [
            'sucursal_id' => $this->sucursal->id,
            'nombre_departamento' => 'Recursos Humanos',
            'estado' => 'activo',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.departamentos.store'), $data);

        $response->assertRedirect(route('admin.departamentos.index'));
        $this->assertDatabaseHas('departamentos', [
            'nombre_departamento' => 'Recursos Humanos',
            'sucursal_id' => $this->sucursal->id,
        ]);
    }

    public function test_admin_can_update_departamento()
    {
        $departamento = Departamento::factory()->create([
            'nombre_departamento' => 'Old Name',
            'sucursal_id' => $this->sucursal->id,
        ]);

        $data = [
            'sucursal_id' => $this->sucursal->id,
            'nombre_departamento' => 'Finanzas',
            'estado' => 'activo',
        ];

        $response = $this->actingAs($this->adminUser)
            ->put(route('admin.departamentos.update', $departamento), $data);

        $response->assertRedirect(route('admin.departamentos.index'));
        $this->assertDatabaseHas('departamentos', [
            'id' => $departamento->id,
            'nombre_departamento' => 'Finanzas',
        ]);
    }
}
