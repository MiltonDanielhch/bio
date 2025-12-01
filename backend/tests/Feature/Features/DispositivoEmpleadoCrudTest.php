<?php

namespace Tests\Feature;

use App\Models\Dispositivo;
use App\Models\DispositivoEmpleado;
use App\Models\Empleado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use TCG\Voyager\Models\Role;
use Tests\TestCase;

class DispositivoEmpleadoCrudTest extends TestCase
{
    /**
     * Este Trait es mágico. Antes de cada prueba, ejecutará todas las migraciones
     * para crear la estructura de la BD, y después de la prueba, la limpiará.
     * Esto asegura que cada prueba se ejecute en un entorno limpio y aislado.
     */
    use RefreshDatabase;

    private User $adminUser;
    private User $regularUser;
    private Empleado $empleado;
    private Dispositivo $dispositivo;

    /**
     * El método setUp() se ejecuta antes de cada una de las pruebas en este archivo.
     * Lo usamos para preparar los datos comunes que necesitaremos.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 1. Creamos los roles necesarios (si no existen)
        // El rol 'admin' tiene todos los permisos por defecto en Voyager.
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'display_name' => 'Administrator']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'display_name' => 'Normal User']);

        // 2. Creamos un usuario Administrador
        $this->adminUser = User::factory()->create(['role_id' => $adminRole->id]);

        // 3. Creamos un usuario Regular (sin permisos para este CRUD)
        $this->regularUser = User::factory()->create(['role_id' => $userRole->id]);

        // 4. Creamos los datos relacionados que necesitaremos para el mapeo
        // NOTA: Asumo que tienes Factories para Empleado y Dispositivo.
        // Si no los tienes, créalos con `php artisan make:factory EmpleadoFactory --model=Empleado`
        $this->empleado = Empleado::factory()->create();
        $this->dispositivo = Dispositivo::factory()->create();

        // 5. Damos el permiso explícito al rol de admin para el CRUD.
        // Esto es crucial porque tu Policy (`DispositivoEmpleadoPolicy`) lo verifica.
        $this->artisan('voyager:admin', ['--create' => true]); // Asegura que los permisos base existan
        $adminRole->permissions()->syncWithoutDetaching(
            \TCG\Voyager\Models\Permission::where('table_name', 'dispositivo_empleado')->pluck('id')
        );
    }

    /**
     * Prueba que un usuario no autenticado sea redirigido a la página de login.
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/dispositivo-empleado');
        $response->assertRedirect('/admin/login');
    }

    /**
     * Prueba que un usuario autenticado pero sin permisos reciba un error 403 (Prohibido).
     * Esto valida que tu Policy está funcionando correctamente.
     */
    public function test_unauthorized_user_cannot_access_crud(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/admin/dispositivo-empleado');
        $response->assertForbidden();
    }

    /**
     * Prueba que el administrador puede ver la página principal del CRUD.
     */
    public function test_admin_can_browse_dispositivo_empleado(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/dispositivo-empleado');

        $response->assertStatus(200);
        $response->assertSee('Mapeo Dispositivo-Empleado'); // Verifica que el título de la página esté presente.
    }

    /**
     * Prueba que el administrador puede crear un nuevo mapeo con datos válidos.
     */
    public function test_admin_can_store_new_dispositivo_empleado(): void
    {
        $data = [
            'empleado_id' => $this->empleado->id,
            'dispositivo_id' => $this->dispositivo->id,
            'zk_user_id' => 101,
            'privilegio' => 'usuario',
        ];

        $response = $this->actingAs($this->adminUser)->post('/admin/dispositivo-empleado', $data);

        // Después de crear, usualmente se redirige a la lista.
        $response->assertRedirect('/admin/dispositivo-empleado');

        // Verificamos que los datos realmente se guardaron en la base de datos.
        $this->assertDatabaseHas('dispositivo_empleado', [
            'empleado_id' => $this->empleado->id,
            'dispositivo_id' => $this->dispositivo->id,
            'zk_user_id' => 101,
        ]);
    }

    /**
     * Prueba que la validación (FormRequest) falla si se envían datos incorrectos.
     */
    public function test_store_fails_with_invalid_data(): void
    {
        $data = [
            'empleado_id' => $this->empleado->id,
            // Falta 'dispositivo_id' y 'zk_user_id' a propósito
        ];

        $response = $this->actingAs($this->adminUser)->post('/admin/dispositivo-empleado', $data);

        // La respuesta debe indicar que hay errores de validación para los campos faltantes.
        $response->assertSessionHasErrors(['dispositivo_id', 'zk_user_id']);

        // Verificamos que NADA se guardó en la base de datos.
        $this->assertDatabaseCount('dispositivo_empleado', 0);
    }

    /**
     * Prueba que el administrador puede eliminar un mapeo existente.
     */
    public function test_admin_can_delete_dispositivo_empleado(): void
    {
        // Primero, creamos un registro para poder borrarlo.
        $mapeo = DispositivoEmpleado::create([
            'empleado_id' => $this->empleado->id,
            'dispositivo_id' => $this->dispositivo->id,
            'zk_user_id' => 102,
            'privilegio' => 'usuario',
        ]);

        $this->assertDatabaseCount('dispositivo_empleado', 1);

        $response = $this->actingAs($this->adminUser)->delete("/admin/dispositivo-empleado/{$mapeo->id}");

        $response->assertRedirect('/admin/dispositivo-empleado');

        // Verificamos que el registro fue eliminado de la base de datos.
        $this->assertDatabaseCount('dispositivo_empleado', 0);
    }
}
