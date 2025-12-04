<?php

namespace Tests\Feature;

use App\Models\Dispositivo;
use App\Models\DispositivoEmpleado;
use App\Models\Empleado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TCG\Voyager\Models\Role;
use Tests\TestCase;

class DispositivoEmpleadoCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private Empleado $empleado;
    private Dispositivo $dispositivo;

    protected function setUp(): void
    {
        parent::setUp();
        // Create admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator']);
        
        $this->adminUser = User::factory()->create(['role_id' => $adminRole->id]);
        
        // Bypass authorization for tests by allowing all actions for admin users
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->role_id === Role::where('name', 'admin')->first()?->id) {
                return true; // Admin can do everything
            }
        });

        $this->empleado = Empleado::factory()->create();
        $this->dispositivo = Dispositivo::factory()->create();
    }

    public function test_admin_can_store_new_dispositivo_empleado()
    {
        $data = [
            'empleado_id' => $this->empleado->id,
            'dispositivo_id' => $this->dispositivo->id,
            'zk_user_id' => 101,
            'privilegio' => 'usuario', // Added required field
        ];

        // Assuming the route name is 'voyager.dispositivo-empleado.store' or similar based on Voyager conventions
        // The user snippet used 'admin.dispositivo-empleado.store'. I'll stick to that but might need to adjust.
        // If it's standard Voyager, it might be 'voyager.dispositivo-empleado.store'.
        // Let's check routes later if this fails.
        
        $response = $this->actingAs($this->adminUser)->post(route('admin.dispositivo-empleado.store'), $data);

        $response->assertRedirect(route('admin.dispositivo-empleado.index'));
        $this->assertDatabaseHas('dispositivo_empleado', [
            'empleado_id' => $this->empleado->id,
            'dispositivo_id' => $this->dispositivo->id,
            'zk_user_id' => 101,
        ]);
    }

    public function test_store_fails_if_zk_user_id_is_not_unique_for_the_same_device()
    {
        // Creamos un primer mapeo
        DispositivoEmpleado::factory()->create([
            'dispositivo_id' => $this->dispositivo->id,
            'zk_user_id' => 101,
        ]);

        // Intentamos crear otro con el mismo zk_user_id en el mismo dispositivo
        $data = [
            'empleado_id' => Empleado::factory()->create()->id,
            'dispositivo_id' => $this->dispositivo->id,
            'zk_user_id' => 101, // ID repetido
            'privilegio' => 'usuario',
        ];

        $response = $this->actingAs($this->adminUser)->post(route('admin.dispositivo-empleado.store'), $data);

        $response->assertSessionHasErrors(); // Debería fallar por la regla 'unique'
        $this->assertDatabaseCount('dispositivo_empleado', 1);
    }
}
