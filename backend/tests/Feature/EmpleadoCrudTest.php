<?php

namespace Tests\Feature;

use App\Models\Departamento;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use TCG\Voyager\Models\Role;
use Tests\TestCase;

class EmpleadoCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private Empresa $empresa;
    private Departamento $departamento;

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
        $this->departamento = Departamento::factory()->create();
    }

    public function test_admin_can_create_empleado()
    {
        $data = [
            'empresa_id' => $this->empresa->id,
            'departamento_id' => $this->departamento->id,
            'codigo_empleado' => 'EMP001',
            'dni' => '12345678',
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'fecha_nacimiento' => '1990-01-01',
            'fecha_contratacion' => '2020-01-01',
            'tipo_contrato' => 'indefinido',
            'genero' => 'M',
            'estado' => 'activo',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.empleados.store'), $data);

        $response->assertRedirect(route('admin.empleados.index'));
        $this->assertDatabaseHas('empleados', [
            'codigo_empleado' => 'EMP001',
            'dni' => '12345678',
        ]);
    }

    public function test_codigo_empleado_must_be_unique_per_empresa()
    {
        Empleado::factory()->create([
            'empresa_id' => $this->empresa->id,
            'codigo_empleado' => 'EMP001',
        ]);

        $data = [
            'empresa_id' => $this->empresa->id,
            'departamento_id' => $this->departamento->id,
            'codigo_empleado' => 'EMP001', // Duplicate
            'dni' => '87654321',
            'nombres' => 'Maria',
            'apellidos' => 'López',
            'fecha_nacimiento' => '1992-01-01',
            'fecha_contratacion' => '2021-01-01',
            'tipo_contrato' => 'indefinido',
            'genero' => 'F',
            'estado' => 'activo',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.empleados.store'), $data);

        $response->assertSessionHasErrors();
    }
}
