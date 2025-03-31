<?php

namespace App\Http\Controllers;

use App\Models\Centros;
use App\Models\Departamentos;
use App\Models\Grupos;
use App\Models\UserSessions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session as FacadesSession;

class AuthController extends Controller
{

    public function login()
    {
        try {
            // Verifica si el usuario ya está autenticado
            if (Auth::check()) {
                // Obtiene el usuario autenticado
                $user = Auth::user();

                // Redirige al usuario según su rol
                switch ($user->rol) {
                    case 'administrador':
                        return redirect()->route('home');
                    case 'caja':
                        return redirect()->route('caja');
                    case 'contador':
                        return redirect()->route('contador');
                    default:
                        // Si el rol no es válido, redirige al login
                        return redirect()->route('login');
                }
            }

            // Si no está autenticado, muestra la vista de login
            return view('modules/auth/login');
        } catch (\Exception $e) {
            // Maneja cualquier error que ocurra durante la autenticación
            Log::error('Error al verificar o redirigir al usuario: ' . $e->getMessage());

            // Redirige al login con un mensaje de error si algo sale mal
            return redirect()->route('login')->withErrors(['error' => 'Ocurrió un error inesperado.']);
        }
    }




    public function loggear(Request $request)
    {
        try {
            $credenciales = [
                'email' => $request->email,
                'password' => $request->password,
            ];

            if (Auth::attempt($credenciales)) {
                $user = Auth::user();

                UserSessions::create([
                    'user_id' => $user->id,
                    'started_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                // Verificar si el usuario está activo
                if ($user->estado === 'activo') {
                    // Registra la hora de inicio de sesión en la tabla 'sessions'
                    $sessionId = session()->getId(); // Obtener el ID de la sesión
                    DB::table('sessions')->where('id', $sessionId)->update([
                        'user_id' => $user->id, // Guardar el ID del usuario en la sesión
                        'login_time' => Carbon::now(), // Registrar la hora de inicio de sesión
                    ]);

                    // Redirige al usuario según su rol
                    switch ($user->rol) {
                        case 'administrador':
                            return to_route('home');
                        case 'caja':
                            return to_route('caja');
                        case 'contador':
                            return to_route('contador');
                        default:
                            return to_route('login');
                    }
                } else {
                    Auth::logout();
                    session()->flash('error', 'Tu cuenta ha sido desactivada.');
                    return redirect()->route('login');
                }
            } else {
                return back()->withErrors([
                    'email' => 'Las credenciales no coinciden.',
                    'password' => 'Las credenciales no coinciden.'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error en el proceso de autenticación: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'Ocurrió un error inesperado. Por favor, intente nuevamente más tarde.'
            ]);
        }
    }


    public function logout(Request $request)
    {
        // Obtener la sesión activa del usuario
        $session = UserSessions::where('user_id', Auth::id())
            ->whereNull('ended_at')
            ->first();

        // Si hay una sesión activa, actualizar la hora de fin
        if ($session) {
            $session->update([
                'ended_at' => now(),
            ]);
        }

        // Cerrar sesión y redirigir al login
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }


    public function home()
    {
        $rol = Auth::user()->rol; // Obtener el rol del usuario autenticado
        return view('modules.dashboard.home')->with('rol', $rol); // Pasar el rol a la vista
    }
    public function caja()
    {
        $rol = Auth::user()->rol;
        return view('modules.dashboard.home')->with('rol', $rol);
    }

    public function contador()
    {
        $rol = Auth::user()->rol;
        $departamentos = Departamentos::all();
        return view('modules.dashboard.home', compact('rol', 'departamentos'));
    }
    public function grupos()
    {
        $rol = Auth::user()->rol;

        // Verificar si el rol es 'contador'
        if ($rol !== 'contador' && $rol !== 'caja') {
            // Si no es contador, redirigir o mostrar un mensaje de error
            return redirect()->route('home')->with('error', 'No tienes acceso a esta sección.');
        }

        // Si el rol es 'contador', cargar la vista
        $centros = Centros::orderBy('id', 'DESC')->get();
        $gruposPorCentro = Grupos::select('id_centros', DB::raw('count(*) as cantidad_grupos'))
            ->with('centro')  // Cargar la relación con el centro
            ->groupBy('id_centros')
            ->get();
        return view('modules.dashboard.grupos', compact('rol', 'centros', 'gruposPorCentro'));
    }

    public function mantenimientoAsesores()
    {
        $rol = Auth::user()->rol;

        // Verificar si el rol es 'contador'
        if ($rol !== 'contador') {
            // Si no es contador, redirigir o mostrar un mensaje de error
            return redirect()->route('home')->with('error', 'No tienes acceso a esta sección.');
        }

        // Si el rol es 'contador', cargar la vista
        return view('modules.dashboard.mantenimientoasesor')->with('rol', $rol);
    }
    public function reverliquidacion()
    {
        $rol = Auth::user()->rol;

        // Verificar si el rol es 'contador'
        if ($rol !== 'contador') {
            // Si no es contador, redirigir o mostrar un mensaje de error
            return redirect()->route('home')->with('error', 'No tienes acceso a esta sección.');
        }

        // Si el rol es 'contador', cargar la vista
        return view('modules.dashboard.reversionliquidacion')->with('rol', $rol);
    }
}
