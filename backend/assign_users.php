<?php

use App\Models\Dispositivo;
use App\Models\Empleado;

$dispositivo = Dispositivo::find(7);

if (!$dispositivo) {
    echo "Dispositivo 7 no encontrado\n";
    exit(1);
}

echo "Asignando empleados al dispositivo {$dispositivo->nombre_dispositivo}...\n";

// Asignar empleados 1 y 2
$dispositivo->empleados()->syncWithoutDetaching([
    1 => ['zk_user_id' => 101, 'privilegio' => 'User', 'estado_sincronizacion' => 'pendiente'],
    2 => ['zk_user_id' => 102, 'privilegio' => 'Admin', 'estado_sincronizacion' => 'pendiente']
]);

echo "Empleados asignados correctamente. Total: " . $dispositivo->empleados()->count() . "\n";
