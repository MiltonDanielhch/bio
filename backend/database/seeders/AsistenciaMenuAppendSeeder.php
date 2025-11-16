<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use TCG\Voyager\Models\Menu;
use TCG\Voyager\Models\MenuItem;

class AsistenciaMenuAppendSeeder extends Seeder
{
    protected $tree = [
        // GRUPO BIOMÉTRICO ─ ASISTENCIA
        [
            'title'      => 'Asistencia',
            'order'      => 1,
            'icon_class' => 'voyager-calendar',
            'route'      => null,
            'url'        => '',
            'children'   => [
                ['title' => 'Empresas',          'route' => 'admin.empresas.index',           'icon_class' => 'voyager-briefcase',         'order' => 1],
                ['title' => 'Sucursales',        'route' => 'admin.sucursales.index',         'icon_class' => 'voyager-shop',              'order' => 2],
                ['title' => 'Departamentos',     'route' => 'admin.departamentos.index',      'icon_class' => 'voyager-categories',        'order' => 3],
                ['title' => 'Empleados',         'route' => 'admin.empleados.index',          'icon_class' => 'voyager-people',            'order' => 4],
                ['title' => 'Dispositivos',      'route' => 'admin.dispositivos.index',       'icon_class' => 'voyager-wifi',              'order' => 5],
                ['title' => 'Mapeo Empleados',   'route' => 'admin.dispositivo-empleado.index','icon_class' => 'voyager-data',              'order' => 6],
                ['title' => 'Horarios',          'route' => 'admin.horarios.index',           'icon_class' => 'voyager-clock',             'order' => 7],
                ['title' => 'Registros',         'route' => 'admin.registros-asistencia.index','icon_class'=> 'voyager-list',               'order' => 8],
                ['title' => 'Incidencias',       'route' => 'admin.incidencias.index',        'icon_class' => 'voyager-warning',           'order' => 9],
                ['title' => 'Reportes',          'route' => 'admin.reportes-asistencia.index','icon_class' => 'voyager-chart',             'order' => 10],
            ],
        ],

        // PERSONAS (IDTGB)
        // [
        //     'title'      => 'Personas',
        //     'order'      => 10,
        //     'icon_class' => 'voyager-person',
        //     'route'      => 'admin.people.index',
        //     'url'        => '',
        // ],
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
