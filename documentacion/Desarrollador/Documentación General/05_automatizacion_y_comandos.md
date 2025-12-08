# 5. Automatización y Comandos Útiles

Para que GobeBio funcione de manera autónoma, es crucial configurar la ejecución periódica de tareas y conocer los comandos de Artisan que facilitan la gestión y depuración del sistema.

## 5.1. Automatización con el Scheduler de Laravel

La tarea más importante a automatizar es la descarga de asistencias desde los dispositivos. Esto se logra utilizando el programador de tareas (Scheduler) de Laravel, que a su vez depende de un único `cron job` en el servidor.

### Configuración del Cron Job

Se debe agregar la siguiente entrada al `crontab` del servidor que aloja los contenedores de Docker. Esto ejecutará el programador de Laravel cada minuto.

```bash
# Acceder al crontab
crontab -e

# Añadir la siguiente línea. Asegúrate de que la ruta al proyecto sea la correcta.
* * * * * cd /ruta/a/tu/proyecto/gobebio && docker-compose exec -T backend php artisan schedule:run >> /dev/null 2>&1
```

*   **`docker-compose exec -T backend`**: Ejecuta el comando dentro del contenedor `backend` sin asignarle una TTY, lo cual es ideal para scripts.
*   **`schedule:run`**: Es el comando de Artisan que revisa las tareas programadas y ejecuta las que correspondan en ese minuto.

### Programación de Tareas

Las tareas se definen en el método `schedule` del archivo `app/Console/Kernel.php`.

**Ejemplo: Programar la sincronización de asistencias para todos los dispositivos cada 15 minutos.**

```php
// In app/Console/Kernel.php

use App\Jobs\SyncAttendanceJob;
use App\Models\Dispositivo;

protected function schedule(Schedule $schedule): void
{
    $schedule->call(function () {
        // Obtiene todos los dispositivos activos
        $dispositivos = Dispositivo::where('activo', true)->get();

        foreach ($dispositivos as $dispositivo) {
            // Despacha un job para cada dispositivo a la cola
            SyncAttendanceJob::dispatch($dispositivo);
        }
    })->everyFifteenMinutes(); // Frecuencia de ejecución
}
```

## 5.2. Comandos de Artisan Esenciales

Estos comandos se ejecutan desde la línea de comandos, generalmente dentro del contenedor `backend`.

*   **`php artisan queue:work`**: Inicia un worker para procesar los jobs de la cola. El contenedor `worker` de Docker ya ejecuta este comando de forma persistente.
*   **`php artisan queue:restart`**: Reinicia los workers. Es **necesario** ejecutarlo después de cada despliegue de código nuevo para que los workers carguen la última versión de la aplicación.
*   **`php artisan queue:failed`**: Muestra una lista de los jobs que han fallado.
*   **`php artisan queue:retry <id>`**: Reintenta la ejecución de un job que ha fallado.
*   **`php artisan tinker`**: Abre una consola interactiva (REPL) para ejecutar código PHP y probar lógica o interactuar con los modelos de Eloquent. Es una herramienta de depuración invaluable.
*   **`php artisan migrate --seed`**: Ejecuta las migraciones y los seeders para inicializar la base de datos.
