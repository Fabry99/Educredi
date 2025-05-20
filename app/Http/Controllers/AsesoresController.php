<?php

namespace App\Http\Controllers;

use App\Models\Asesores;
use App\Models\Sucursales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AsesoresController extends Controller
{
    public function mantenimientoAsesores()
    {
        $rol = Auth::user()->rol;
        $sucursales = Sucursales::all();
        $asesores = Asesores::with('sucursales')->get();
        // dd($asesores);
        // Verificar si el rol es 'contador'
        if ($rol !== 'contador') {
            // Si no es contador, redirigir o mostrar un mensaje de error
            return redirect()->route('home')->with('error', 'No tienes acceso a esta sección.');
        }

        // Si el rol es 'contador', cargar la vista
        return view('modules.dashboard.mantenimientoasesor', compact('rol','sucursales','asesores'));
    }

    public function InsertarAsesor(Request $request){

        $request->validate(['nombre' => 'required|string|max:250',
        'sucursal' => 'required|integer|max:10']);

        try {
            Asesores::create([ 'nombre' => $request->nombre,
            'id_sucursal' => $request->sucursal]);

            return redirect()->back()->with('success','Asesor Agregado Correctamente.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Ocurrio un Problema al Ingresar los Datos.');
        }
    }
   
    public function updateAsesor(Request $request, $id)
    {
    
        $asesor = Asesores::findOrFail($id);
        $asesor->nombre = $request->input('nombre');
        $asesor->id_sucursal = $request->input('sucursal'); // Ajusta si usas relación
        $asesor->save();
    
        return response()->json(['message' => 'Asesor actualizado correctamente']);
    }
    
    
}
