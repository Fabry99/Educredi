<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

                if ($user->estado === 'activo') {

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
        // Cierra la sesión del usuario
        Auth::logout();
        
        // Invalida la sesión y regenera el token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        // Redirige al login
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
        return view('modules.dashboard.home')->with('rol', $rol);
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
        return view('modules.dashboard.grupos')->with('rol', $rol);
    }
   
    public function mantenimientoAsesores(){
        $rol = Auth::user()->rol;
    
        // Verificar si el rol es 'contador'
        if ($rol !== 'contador') {
            // Si no es contador, redirigir o mostrar un mensaje de error
            return redirect()->route('home')->with('error', 'No tienes acceso a esta sección.');
        }
    
        // Si el rol es 'contador', cargar la vista
        return view('modules.dashboard.mantenimientoasesor')->with('rol', $rol);
    }
    public function reverliquidacion(){
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
