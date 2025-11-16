<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Permission;
use TCG\Voyager\Models\Role;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('permission_role')->delete();

        // Root
        $role = Role::where('name', 'admin')->firstOrFail();
        $permissions = Permission::all();
        $role->permissions()->sync($permissions->pluck('id')->all());



        // Administrador
        $role = Role::where('name', 'administrador')->firstOrFail();
        $adminPermissions = Permission::whereIn('table_name', [
            'admin',
            'menus',
            'roles',
            'users',
            'settings',
            "people",
            // Módulos de Asistencia
            'empresas',
            'sucursales',
            'departamentos',
            'empleados',
            'horarios',
            'asignacion_horarios',
            'dispositivos',
            'dispositivo_empleado',
            'registros_asistencia',
            'tipos_incidencia',
            'incidencias',
            'reportes_asistencia',
        ])->pluck('id');

        // Añadir permisos específicos por 'key' que no tienen 'table_name'
        $specificAdminPermissions = Permission::whereIn('key', ['browse_clear-cache'])->pluck('id');
        $allAdminPermissions = $adminPermissions->merge($specificAdminPermissions);

        $role->permissions()->sync($allAdminPermissions);

        // Técnico
        $role = Role::where('name', 'tecnico')->firstOrFail();
        $tecnicoPermissions = Permission::whereIn('table_name', [
            'admin', // Acceso básico al panel
            // Módulos de Asistencia
            'empresas',
            'sucursales',
            'departamentos',
            'empleados',
            'horarios',
            'asignacion_horarios',
            'dispositivos',
            'dispositivo_empleado',
            'registros_asistencia',
            'tipos_incidencia',
            'incidencias',
            'reportes_asistencia',
            'people',
        ])->pluck('id');

        // Añadir permiso para limpiar caché
        $specificTecnicoPermissions = Permission::where('key', 'browse_clear-cache')->pluck('id');
        $allTecnicoPermissions = $tecnicoPermissions->merge($specificTecnicoPermissions);

        $role->permissions()->sync($allTecnicoPermissions);
    }
}
