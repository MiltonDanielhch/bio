<?php

namespace Tests\Feature;

use App\Jobs\SyncAttendanceJob;
use App\Models\Dispositivo;
use App\Models\User;
use App\Services\ZkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;
use TCG\Voyager\Models\Role;

class DispositivoControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private Dispositivo $dispositivo;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup admin user
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator']);
        $this->adminUser = User::factory()->create(['role_id' => $adminRole->id]);
        
        // Ensure permissions exist (mocking Voyager permissions if needed, but actingAs admin usually bypasses or we can assume permissions are there if we seeded or if Voyager checks role name)
        // For this test, we are hitting controller methods directly or via route.
        // Let's assume standard Voyager routes exist.
        
        $this->dispositivo = Dispositivo::factory()->create();
    }

    public function test_sync_now_dispatches_job()
    {
        Bus::fake();

        // Assuming route name 'admin.dispositivos.sync' based on controller method `syncNow`
        // We might need to verify the actual route name.
        // If it's a custom route added to Voyager, it might be 'admin.dispositivos.sync'.
        // Let's try to hit the action.
        
        // We need to register the route for the test if it's not in standard routes, 
        // but usually it's in web.php. 
        // Let's assume the route exists as: Route::post('admin/dispositivos/{dispositivo}/sync', [DispositivoController::class, 'syncNow'])->name('admin.dispositivos.sync');
        // If not, we might need to check routes/web.php.
        
        // For now, let's try to call the route. If it fails, I'll check routes.
        // Actually, I should check routes first to be sure.
        // But I'll write the test assuming a standard naming convention or I'll check routes file in next step if this fails.
        // Let's assume the route is `admin.dispositivos.sync` or similar.
        
        // Wait, I can't easily check routes without running artisan route:list which might be slow or noisy.
        // I'll check routes/web.php quickly after this if I can.
        // Or I can just define the route in the test? No, that's integration testing.
        
        // Let's assume the route is `voyager.dispositivos.sync` or `admin.dispositivos.sync`.
        // Given the controller is in `Admin` namespace and uses `admin.dispositivos.index`, likely `admin.dispositivos.sync`.
        
        // However, to be safe, I'll mock the route or just instantiate the controller?
        // No, Feature tests should hit the HTTP layer.
        
        // Let's try to find the route file.
        
        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.dispositivos.sync_now', $this->dispositivo));

        $response->assertRedirect();
        $response->assertSessionHas('message', 'Sincronización iniciada para: ' . $this->dispositivo->nombre_dispositivo);

        Bus::assertDispatched(SyncAttendanceJob::class, function ($job) {
            $reflection = new \ReflectionClass($job);
            $property = $reflection->getProperty('dispositivo');
            $property->setAccessible(true);
            return $property->getValue($job)->id === $this->dispositivo->id;
        });
    }

    public function test_test_connection_uses_zk_service()
    {
        // Mock ZkService
        $this->mock(ZkService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getDeviceInfo')
                ->once()
                ->with($this->dispositivo->direccion_ip, $this->dispositivo->puerto, $this->dispositivo->password)
                ->andReturn(['serial_number' => 'SN123456']);
        });

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.dispositivos.test_connection', $this->dispositivo));

        $response->assertRedirect();
        $response->assertSessionHas('message', "Conexión exitosa con {$this->dispositivo->nombre_dispositivo} (SN: SN123456)");
    }
}
