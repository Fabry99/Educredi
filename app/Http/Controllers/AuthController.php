<?php

namespace App\Http\Controllers;

use App\Models\Centros;
use App\Models\Clientes;
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
                    'user_agent' => $request->header('User-Agent'), 
                ]);
                if ($user->estado === 'activo') {
                    $sessionId = session()->getId();
                    DB::table('sessions')->where('id', $sessionId)->update([
                        'user_id' => $user->id,
                        'login_time' => Carbon::now(), 
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
        $session = UserSessions::where('user_id', Auth::id())
            ->whereNull('ended_at')
            ->orderByDesc('started_at') 
            ->first();
        if ($session->ended_at === null) {
            $session->update([
                'ended_at' => now(), 
            ]);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
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
        $centros = Centros::all();
        $grupo = Grupos::all();
        $clientes = Clientes::with('departamento', 'municipio', 'grupo','centro')->get();
        // Recupera el cliente, lanza error si no se encuentra
        if ($rol == 'contador') {
            return view('modules.dashboard.home', compact('rol', 'departamentos', 'clientes', 'grupo','centros'));
        }
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
            $clienteporGrupo = Clientes::select('id_grupo', DB::raw('count(*) as cantidad_persona'))
            ->with('grupo')  // Cargar la relación con el centro
            ->groupBy('id_grupo')
            ->get();

        return view('modules.dashboard.grupos', compact('rol', 'centros', 'gruposPorCentro','clienteporGrupo'));
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

    public function creditos(){
        $rol = Auth::user()->rol;
    
        // Verificar si el rol es 'contador'
        if ($rol !== 'contador') {
            // Si no es contador, redirigir o mostrar un mensaje de error
            return redirect()->route('home')->with('error', 'No tienes acceso a esta sección.');
        }
    
        // Si el rol es 'contador', cargar la vista
        return view('modules.dashboard.desembolso')->with('rol', $rol);
    }

    public function cambiardatos(){
        $rol = Auth::user()->rol;
    
        // Verificar si el rol es 'contador'
        if ($rol !== 'contador') {
            // Si no es contador, redirigir o mostrar un mensaje de error
            return redirect()->route('home')->with('error', 'No tienes acceso a esta sección.');
        }
    
        // Si el rol es 'contador', cargar la vista
        return view('modules.dashboard.cambiodatos')->with('rol', $rol);
    }

    public function transferenciadecartera(){
        $rol = Auth::user()->rol;
    
        // Verificar si el rol es 'contador'
        if ($rol !== 'contador') {
            // Si no es contador, redirigir o mostrar un mensaje de error
            return redirect()->route('home')->with('error', 'No tienes acceso a esta sección.');
        }
    
        // Si el rol es 'contador', cargar la vista
        return view('modules.dashboard.transferencia')->with('rol', $rol);
    }
}
