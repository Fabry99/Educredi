<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Obtén la respuesta del siguiente middleware
        $response = $next($request);

        // Verifica si la respuesta es una instancia de Response
        if ($response instanceof \Illuminate\Http\Response) {
            // Asegúrate de que los encabezados de la respuesta impidan el almacenamiento en caché
            return $response->withHeaders([
                'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate', // Evita caché
                'Pragma' => 'no-cache', // Para versiones antiguas de HTTP
                'Expires' => 'Sat, 01 Jan 1990 00:00:00 GMT', // Fecha en el pasado
            ]);
        }

        return $response; // Si no es una instancia de Response, devuelve la respuesta sin cambios
    }
}
