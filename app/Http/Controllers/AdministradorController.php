<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdministradorController extends Controller
{
    public function home()
    {
        $rol = Auth::user()->rol; // Obtener el rol del usuario autenticado
        return view('modules.dashboard.home')->with('rol', $rol); // Pasar el rol a la vista
    }
    public function bitacora(){

        $rol = Auth::user()->rol;
        $bitacora = Bitacora::with('user:id,name,last_name')->get();
        if ($rol == 'administrador') {
            return view('modules.dashboard.bitacora', compact('rol','bitacora'));
        }
    }

    public function usuarios(){
        $rol = Auth::user()->rol;
        $usuarios = User::with('latestSession')->get(); // Carga solo la última sesión
        if ($rol == 'administrador') {
            return view('modules.dashboard.usuarios',compact('rol', 'usuarios'));
        }
    }

}
