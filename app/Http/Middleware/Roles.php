<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class Roles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Verificamos que el usuario esté autenticado y tenga el rol adecuado
        if (Auth::check() && Auth::user()->rol === $role) {
            
            // Obtener la ID de la sesión
            $sessionId = session()->getId();
            $userId = Auth::user()->id;

            // Actualizar la tabla 'sessions' con el 'user_id'
            DB::table('sessions')
                ->where('id', $sessionId)
                ->update([
                    'user_id' => $userId,  // Guardamos el ID del usuario
                    'last_activity' => Carbon::now()->timestamp,  // Actualizamos la última actividad
                ]);

            return $next($request);  // Si el rol es correcto, seguimos con la solicitud
        }

        // Si no tiene el rol adecuado, redirigimos al login
        return redirect()->route('login');
    }
}