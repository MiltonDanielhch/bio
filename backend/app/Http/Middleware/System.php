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
        // 1. Rutas críticas siempre abiertas
        $open = [
            'admin/login',
            'admin/logout',
            'admin/password/*',
            'admin/voyager-assets*',
            '/',
        ];
        if ($request->is($open)) {
            return $next($request);
        }

        // 2. Modo mantenimiento
        if (setting('configuracion.maintenance') === '1') {
            if (auth()->check() && auth()->user()->hasRole(['admin', 'Administrador'])) {
                return $next($request);
            }
            return response()->view('errors.503', [], 503);
        }

        // 3. Desarrollo: solo admins
        if (Auth::user()) {
            if (setting('system.development') && !auth()->user()->hasRole('admin')) {
               return response()->view('errors.503', [], 503);
            }
        }

        // 4. Lógica de licencia (solo si hay datos)
        $controller = new SolucionDigitalController();
        $data = $controller->settings_code();

        if ($data) {
            $payment = new Controller();
            if ($payment->payment_alert() === 'finalizado') {
                $blockedMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];
                $allowedRoutes  = ['admin/login', 'admin/logout', 'admin/settings'];

                if (
                    in_array($request->method(), $blockedMethods) &&
                    !in_array($request->path(), $allowedRoutes)
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
