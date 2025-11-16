<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('permissions')->delete();

        Permission::firstOrCreate([
            'key'        => 'browse_admin',
            'keyDescription'=>'vista de acceso al sistema',
            'table_name' => 'admin',
            'tableDescription'=>'Panel del Sistema'
        ]);

        $keys = [
            // 'browse_admin',
            'browse_bread',
            'browse_database',
            'browse_media',
            'browse_compass',
            'browse_clear-cache',
        ];

        foreach ($keys as $key) {
            Permission::firstOrCreate([
                'key'        => $key,
                'table_name' => null,
            ]);
        }

        Permission::generateFor('menus');

        Permission::generateFor('roles');
        Permission::generateFor('permissions');
        Permission::generateFor('settings');

        Permission::generateFor('users');

        Permission::generateFor('posts');
        Permission::generateFor('categories');
        Permission::generateFor('pages');



        // Administracion
        $permissions = [
            'browse_people' => 'Ver lista de personas',
            'read_people' => 'Ver detalles de una persona',
            'edit_people' => 'Editar información de personas',
            'add_people' => 'Agregar nuevas personas',
            'delete_people' => 'Eliminar personas',
        ];

        foreach ($permissions as $key => $description) {
            Permission::firstOrCreate([
                'key'        => $key,
                'keyDescription'=> $description,
                'table_name' => 'people',
                'tableDescription'=>'Personas'
            ]);
        }

        // Empresas
        $permissionsEmpresa = [
            'browse_empresas' => 'Ver lista de empresas',
            'read_empresas'   => 'Ver detalles de una empresa',
            'edit_empresas'   => 'Editar información de empresas',
            'add_empresas'    => 'Agregar nuevas empresas',
            'delete_empresas' => 'Eliminar empresas',
        ];

        foreach ($permissionsEmpresa as $key => $description) {
            Permission::firstOrCreate([
                'key'             => $key,
                'keyDescription'  => $description,
                'table_name'      => 'empresas',
                'tableDescription'=> 'Empresas'
            ]);
        }

        $permissionsSucursal = [
            'browse_sucursales' => 'Ver lista de sucursales',
            'read_sucursales'   => 'Ver detalles de una sucursal',
            'edit_sucursales'   => 'Editar información de sucursales',
            'add_sucursales'    => 'Agregar nuevas sucursales',
            'delete_sucursales' => 'Eliminar sucursales',
        ];

        foreach ($permissionsSucursal as $key => $description) {
            Permission::firstOrCreate([
                'key'             => $key,
                'keyDescription'  => $description,
                'table_name'      => 'sucursales',
                'tableDescription'=> 'Sucresales'
            ]);
        }

        $permissionsDepartamento = [
            'browse_departamentos' => 'Ver lista de departamentos',
            'read_departamentos'   => 'Ver detalles de un departamento',
            'edit_departamentos'   => 'Editar información de departamentos',
            'add_departamentos'    => 'Agregar nuevos departamentos',
            'delete_departamentos' => 'Eliminar departamentos',
        ];

        foreach ($permissionsDepartamento as $key => $description) {
            Permission::firstOrCreate([
                'key'             => $key,
                'keyDescription'  => $description,
                'table_name'      => 'departamentos',
                'tableDescription'=> 'Departamentos'
            ]);
        }

        $permissionsHorario = [
            'browse_horarios' => 'Ver lista de horarios',
            'read_horarios'   => 'Ver detalles de un horario',
            'edit_horarios'   => 'Editar información de horarios',
            'add_horarios'    => 'Agregar nuevos horarios',
            'delete_horarios' => 'Eliminar horarios',
        ];

        foreach ($permissionsHorario as $key => $description) {
            Permission::firstOrCreate([
                'key'             => $key,
                'keyDescription'  => $description,
                'table_name'      => 'horarios',
                'tableDescription'=> 'Horarios'
            ]);
        }

        $permissionsAsignacion = [
            'browse_asignacion_horarios' => 'Ver lista de asignaciones de horario',
            'read_asignacion_horarios'   => 'Ver detalles de una asignación',
            'edit_asignacion_horarios'   => 'Editar asignaciones de horario',
            'add_asignacion_horarios'    => 'Agregar nuevas asignaciones',
            'delete_asignacion_horarios' => 'Eliminar asignaciones de horario',
        ];

        foreach ($permissionsAsignacion as $key => $description) {
            Permission::firstOrCreate([
                'key'             => $key,
                'keyDescription'  => $description,
                'table_name'      => 'asignacion_horarios',
                'tableDescription'=> 'Asignaciones de Horario'
            ]);
        }

        $permissionsReporte = [
            'browse_reportes_asistencia' => 'Ver lista de reportes de asistencia',
            'read_reportes_asistencia'   => 'Ver detalles de un reporte',
            'add_reportes_asistencia'    => 'Generar nuevos reportes',
            'edit_reportes_asistencia'   => 'Editar reportes de asistencia',
            'delete_reportes_asistencia' => 'Eliminar reportes de asistencia',
        ];

        foreach ($permissionsReporte as $key => $description) {
            Permission::firstOrCreate([
                'key'             => $key,
                'keyDescription'  => $description,
                'table_name'      => 'reportes_asistencia',
                'tableDescription'=> 'Reportes de Asistencia'
            ]);
        }

        // Empleados
        $permissionsEmpleado = [
            'browse_empleados' => 'Ver lista de empleados',
            'read_empleados'   => 'Ver detalles de un empleado',
            'edit_empleados'   => 'Editar información de empleados',
            'add_empleados'    => 'Agregar nuevos empleados',
            'delete_empleados' => 'Eliminar empleados',
        ];

        foreach ($permissionsEmpleado as $key => $description) {
            Permission::firstOrCreate([
                'key'             => $key,
                'keyDescription'  => $description,
                'table_name'      => 'empleados',
                'tableDescription'=> 'Empleados'
            ]);
        }

        // Dispositivos
        $permissionsDispositivo = [
            'browse_dispositivos' => 'Ver lista de dispositivos',
            'read_dispositivos'   => 'Ver detalles de un dispositivo',
            'edit_dispositivos'   => 'Editar información de dispositivos',
            'add_dispositivos'    => 'Agregar nuevos dispositivos',
            'delete_dispositivos' => 'Eliminar dispositivos',
        ];

        foreach ($permissionsDispositivo as $key => $description) {
            Permission::firstOrCreate([
                'key'             => $key,
                'keyDescription'  => $description,
                'table_name'      => 'dispositivos',
                'tableDescription'=> 'Dispositivos Biométricos'
            ]);
        }

        // Mapeo Dispositivo-Empleado
        $permissionsMapeo = [
            'browse_dispositivo_empleado' => 'Ver lista de mapeos',
            'read_dispositivo_empleado'   => 'Ver detalles de un mapeo',
            'edit_dispositivo_empleado'   => 'Editar mapeos',
            'add_dispositivo_empleado'    => 'Agregar nuevos mapeos',
            'delete_dispositivo_empleado' => 'Eliminar mapeos',
        ];

        foreach ($permissionsMapeo as $key => $description) {
            Permission::firstOrCreate([
                'key'             => $key,
                'keyDescription'  => $description,
                'table_name'      => 'dispositivo_empleado',
                'tableDescription'=> 'Mapeo Dispositivo-Empleado'
            ]);
        }

        // Registros de Asistencia
        $permissionsRegistro = [
            'browse_registros_asistencia' => 'Ver lista de registros de asistencia',
            'read_registros_asistencia'   => 'Ver detalles de un registro',
            'edit_registros_asistencia'   => 'Editar registros de asistencia',
            'add_registros_asistencia'    => 'Agregar nuevos registros manualmente',
            'delete_registros_asistencia' => 'Eliminar registros de asistencia',
        ];

        foreach ($permissionsRegistro as $key => $description) {
            Permission::firstOrCreate([
                'key'             => $key,
                'keyDescription'  => $description,
                'table_name'      => 'registros_asistencia',
                'tableDescription'=> 'Registros de Asistencia'
            ]);
        }

        // Tipos de Incidencia
        $permissionsTipoIncidencia = [
            'browse_tipos_incidencia' => 'Ver lista de tipos de incidencia',
            'read_tipos_incidencia'   => 'Ver detalles de un tipo de incidencia',
            'edit_tipos_incidencia'   => 'Editar tipos de incidencia',
            'add_tipos_incidencia'    => 'Agregar nuevos tipos de incidencia',
            'delete_tipos_incidencia' => 'Eliminar tipos de incidencia',
        ];

        foreach ($permissionsTipoIncidencia as $key => $description) {
            Permission::firstOrCreate([
                'key'             => $key,
                'keyDescription'  => $description,
                'table_name'      => 'tipos_incidencia',
                'tableDescription'=> 'Tipos de Incidencia'
            ]);
        }

        // Incidencias
        Permission::generateFor('incidencias');


    }
}
