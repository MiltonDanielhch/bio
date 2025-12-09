<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SolucionDigitalController;
use App\Http\Controllers\Controller;

class System
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Definimos las rutas que DEBEN ser excluidas de todas las comprobaciones.
        // Estas son rutas esenciales para que el sistema pueda arrancar y autenticar.
        $excludedRoutes = [
            'admin/login',
            'login',
            'admin/logout',
            'admin/password/*',
            'admin/voyager-assets*',
        ];
        foreach ($excludedRoutes as $route) {
            if ($request->is($route)) {
                return $next($request); // Si la ruta es excluida, no se aplica ninguna lógica y se continúa.
            }
        }

        // 2. Modo mantenimiento (se aplica a todas las rutas NO excluidas)
        if (setting('configuracion.maintenance') === '1') {
            if (auth()->check() && auth()->user()->hasRole(['admin', 'Administrador'])) {
                return $next($request);
            }
            return response()->view('errors.503', [], 503);
        }

        // 3. Desarrollo: solo admins (se aplica a todas las rutas NO excluidas)
        if (Auth::user()) {
            if (setting('system.development') && !auth()->user()->hasRole('admin')) {
               return response()->view('errors.503', [], 503);
            }
        }

        // 4. Lógica de licencia (se aplica a todas las rutas NO excluidas)
        $controller = new SolucionDigitalController();
        $data = $controller->settings_code();

        if ($data) {
            $payment = new Controller();
            if ($payment->payment_alert() === 'finalizado') {
                $blockedMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];
                $allowedRoutes  = [
                    'admin/login',
                    'login',
                    'admin/logout',
                    'admin/settings'
                ];
                if (
                    in_array($request->method(), $blockedMethods) &&
                    // Usamos $request->is() que es más flexible y maneja wildcards.
                    !$request->is($allowedRoutes) &&
                    // Añadimos una excepción para las rutas de Voyager que no queremos bloquear.
                    !$request->is('admin/profile', 'admin/settings')
                ) {
                    return redirect()->back()
                        ->withInput()
                        ->with([
                            'message' => 'Para continuar con el servicio sin interrupciones, contacte al administrador.',
                            'alert-type' => 'error'
                        ]);
                }
            }
        }

        // 5. Si todo está bien, continuar
        return $next($request);
    }
}
