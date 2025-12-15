<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // --- PASO 1: Sembrar datos fundamentales (SIEMPRE PRIMERO) ---
        // Esto asegura que los roles, permisos y el usuario admin existan
        // antes de que cualquier otro seeder intente usarlos.
        $this->call(VoyagerDatabaseSeeder::class);
        $this->call(UsersTableSeeder::class);

        // --- PASO 2: Sembrar datos específicos del entorno ---
        if (App::environment('production')) {
            // --- SEEDERS PARA PRODUCCIÓN ---
            // Carga solo lo estrictamente necesario para que la app funcione.
            $this->call([
                EmpresasTableSeeder::class,        // Crea la empresa principal (Gobernación)
                HorariosTableSeeder::class,        // Crea los tipos de horario (Administrativo, Continuo)
                TiposIncidenciaSeeder::class,      // Crea los tipos de incidencia (Falta, Atraso, etc.)
                AsistenciaMenuAppendSeeder::class, // Agrega los menús del módulo al panel de admin
            ]);
            $this->command->info('✅ Base de datos para PRODUCCIÓN sembrada exitosamente.');
        } else {
            // --- SEEDERS PARA DESARROLLO ---
            // Carga el seeder maestro que incluye datos de prueba.
            $this->call(AsistenciaMaestrosSeeder::class);
            $this->command->info('✅ Base de datos para DESARROLLO sembrada con datos de prueba.');
        }
    }
}
