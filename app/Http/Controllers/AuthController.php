<?php

namespace App\Http\Controllers;

use App\Models\Asesores;
use App\Models\Centros;
use App\Models\Centros_Grupos_Clientes;
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





    public function contador()
    {
        $rol = Auth::user()->rol;
        $departamentos = Departamentos::all();

        $centros = Centros::all();
        $grupo = Grupos::all();
        $centros_grupos_clientes = Centros_Grupos_Clientes::with('clientes', 'grupos', 'centros')->get();
        $clientes = Clientes::with('departamento', 'municipio', 'Centros_Grupos_Clientes.grupos', 'Centros_Grupos_Clientes.centros')->get();
        // Recupera el cliente, lanza error si no se encuentra
        if ($rol == 'contador') {
            return view('modules.dashboard.home', compact('rol', 'clientes', 'centros_grupos_clientes', 'centros', 'grupo', 'departamentos'));
        } else {
            return redirect()->back()->with('error', 'No tienes acceso a esta sección.');
        }
    }

    public function grupos()
    {
        $rol = Auth::user()->rol;

        // Verificar si el rol es 'contador'
        if ($rol !== 'contador' && $rol !== 'caja' && $rol !== 'administrador') {
            // Si no es contador, redirigir o mostrar un mensaje de error
            return redirect()->route('home')->with('error', 'No tienes acceso a esta sección.');
        }

        // Si el rol es 'contador', cargar la vista
        $centros = Centros::orderBy('id', 'DESC')->get();

        $gruposPorCentro = Grupos::select('id_centros', DB::raw('count(*) as cantidad_grupos'))
            ->with('centro')  // Cargar la relación con el centro
            ->groupBy('id_centros')
            ->get();
        $contar = Centros::withCount('grupos')->get();


        $clientesPorCentroYGrupo = Centros_Grupos_Clientes::select(
            'centro_id',
            'grupo_id',
            DB::raw('COUNT(cliente_id) as clientes_count')
        )
            ->with(['centros', 'grupos']) // Carga relaciones para usar los nombres
            ->groupBy('centro_id', 'grupo_id')
            ->get();

        return view('modules.dashboard.grupos', compact('rol', 'centros', 'gruposPorCentro', 'clientesPorCentroYGrupo', 'contar'));
    }


    public function reverliquidacion()
    {
        $rol = Auth::user()->rol;

        // Verificar si el rol es 'contador'
        if ($rol !== 'contador' && $rol !== 'administrador') {
            // Si no es contador, redirigir o mostrar un mensaje de error
            return redirect()->back()->with('error', 'No tienes acceso a esta sección.');
        }

        // Si el rol es 'contador', cargar la vista
        return view('modules.dashboard.reversionliquidacion')->with('rol', $rol);
    }



    public function cambiardatos()
    {
        $rol = Auth::user()->rol;
        $asesor = Asesores::all();
        $centro = Centros::all();


        // Verificar si el rol es 'contador'
        if ($rol !== 'contador') {
            // Si no es contador, redirigir o mostrar un mensaje de error
            return redirect()->route('home')->with('error', 'No tienes acceso a esta sección.');
        }

        // Si el rol es 'contador', cargar la vista
        return view('modules.dashboard.cambiodatos', compact('rol', 'asesor', 'centro'));
    }
}
