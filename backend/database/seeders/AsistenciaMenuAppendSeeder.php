<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;

class AsistenciaMenuAppendSeeder extends Seeder
{
    protected $tree = [
        // 1. GRUPO: ESTRUCTURA ORGANIZACIONAL
        [
            'title'      => 'Estructura Organizacional',
            'order'      => 2, // Después de "Inicio"
            'icon_class' => 'voyager-settings',
            'route'      => null,
            'url'        => '',
            'children'   => [
                ['title' => 'Empresas',                 'route' => 'admin.empresas.index',                  'icon_class' => 'fa-solid fa-building',   'order' => 1],
                ['title' => 'Sucursales',               'route' => 'admin.sucursales.index',                'icon_class' => 'voyager-shop', 'order' => 2],
                ['title' => 'Departamentos',            'route' => 'admin.departamentos.index',             'icon_class' => 'voyager-categories', 'order' => 3],
            ],
        ],

        // 2. GRUPO: GESTIÓN DE PERSONAL
        [
            'title'      => 'Gestión de Personal',
            'order'      => 3,
            'icon_class' => 'voyager-people',
            'route'      => null,
            'url'        => '',
            'children'   => [
                ['title' => 'Empleados',                'route' => 'admin.empleados.index',                 'icon_class' => 'voyager-people', 'order' => 4],
                ['title' => 'Horarios',                 'route' => 'admin.horarios.index',                  'icon_class' => 'voyager-watch',     'order' => 5],
                ['title' => 'Asignación de Horarios',   'route' => 'admin.asignacion-horarios.index',       'icon_class' => 'voyager-forward', 'order' => 6],
            ],
        ],

        // 3. GRUPO: DISPOSITIVOS BIOMÉTRICOS
        [
            'title'      => 'Dispositivos',
            'order'      => 4,
            'icon_class' => 'voyager-wifi',
            'route'      => null,
            'url'        => '',
            'children'   => [
                ['title' => 'Dispositivos',             'route' => 'admin.dispositivos.index',              'icon_class' => 'voyager-wifi', 'order' => 7],
                ['title' => 'Mapeo Empleados',          'route' => 'admin.dispositivo-empleado.index',      'icon_class' => 'voyager-data', 'order' => 8],
            ],
        ],

        // 4. GRUPO: OPERACIONES Y REPORTES
        [
            'title'      => 'Operaciones',
            'order'      => 5,
            'icon_class' => 'voyager-activity',
            'route'      => null,
            'url'        => '',
            'children'   => [
                ['title' => 'Registros de Asistencia',  'route' => 'admin.registros-asistencia.index',      'icon_class' => 'voyager-list', 'order' => 9],
                ['title' => 'Tipos de Incidencia',      'route' => 'admin.tipos-incidencia.index',          'icon_class' => 'voyager-tag', 'order' => 10],
                ['title' => 'Incidencias',              'route' => 'admin.incidencias.index',               'icon_class' => 'voyager-warning', 'order' => 11],
                ['title' => 'Reportes',                 'route' => 'admin.reportes-asistencia.index',       'icon_class' => 'voyager-bar-chart', 'order' => 12],
            ],
        ],
    ];

    public function run()
    {
        $menu = Menu::where('name', 'admin')->firstOrFail();

        foreach ($this->tree as $root) {
            $this->createRecursive($menu, $root);
        }
    }

    private function createRecursive($menu, $item, $parentId = null)
    {
        $data = [
            'menu_id'    => $menu->id,
            'parent_id'  => $parentId,
            'title'      => $item['title'],
            'url'        => $item['url'] ?? '',
            'route'      => $item['route'] ?? null,
            'parameters' => '',
            'target'     => '_self',
            'icon_class' => $item['icon_class'],
            'color'      => null,
            'order'      => $item['order'],
        ];

        $dbItem = MenuItem::firstOrCreate(
            ['menu_id' => $menu->id, 'title' => $item['title'], 'parent_id' => $parentId],
            $data
        );

        foreach ($item['children'] ?? [] as $child) {
            $this->createRecursive($menu, $child, $dbItem->id);
        }
    }
}
