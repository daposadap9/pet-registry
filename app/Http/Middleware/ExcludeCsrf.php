<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ExcludeCsrf
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Excluye CSRF para las rutas específicas
        if ($request->is('pets') || $request->is('categories')) {
            // No se aplica el middleware CSRF a estas rutas
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::withoutMiddleware();
        }

        return $next($request);
    }
}
