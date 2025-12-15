<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Base de Voyager (Roles, Permisos, Menús, etc.)
        // VoyagerDatabaseSeeder ya llama a Roles, Permissions, etc.
        $this->call(VoyagerDatabaseSeeder::class);

        // 2. Usuario Administrador Principal
        $this->call(UsersTableSeeder::class);

        // 3. Datos Maestros ESENCIALES para la aplicación de Asistencia
        //    Estos son los catálogos base que la aplicación necesita para funcionar.
        //    NO se incluyen datos de prueba como empleados, registros, etc.
        $this->call([
            EmpresasTableSeeder::class,      // Crea la empresa principal (Gobernación)
            HorariosTableSeeder::class,      // Crea los tipos de horario (Administrativo, Continuo)
            TiposIncidenciaSeeder::class,    // Crea los tipos de incidencia (Falta, Atraso, etc.)
            AsistenciaMenuAppendSeeder::class, // Agrega los menús del módulo al panel de admin
        ]);

        $this->command->info('✅ Base de datos para PRODUCCIÓN sembrada exitosamente.');
        $this->call(MenusTableSeeder::class);
        $this->call(MenuItemsTableSeeder::class);
    }
}
