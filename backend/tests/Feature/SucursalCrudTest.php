<?php

namespace Tests\Feature;

use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use TCG\Voyager\Models\Role;
use Tests\TestCase;

class SucursalCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private Empresa $empresa;

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

        $this->empresa = Empresa::factory()->create();
    }

    public function test_admin_can_create_sucursal()
    {
        $data = [
            'empresa_id' => $this->empresa->id,
            'nombre_sucursal' => 'Casa Matriz',
            'ciudad' => 'Trinidad',
            'pais' => 'Bolivia',
            'estado' => 'activo',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.sucursales.store'), $data);

        $response->assertRedirect(route('admin.sucursales.index'));
        $this->assertDatabaseHas('sucursales', [
            'nombre_sucursal' => 'Casa Matriz',
            'empresa_id' => $this->empresa->id,
        ]);
    }

    public function test_sucursal_requires_empresa_id()
    {
        $data = [
            'nombre_sucursal' => 'Test Branch',
            'ciudad' => 'Test City',
            'estado' => 'activo',
            // empresa_id missing
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.sucursales.store'), $data);

        $response->assertSessionHasErrors('empresa_id');
    }
}
