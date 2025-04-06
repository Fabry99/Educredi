<?php

namespace App\Http\Controllers;

use App\Models\Centros;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CentroController extends Controller
{
    public function store(Request $request)
    {
        // Validar el nombre (sin id_asesor)
        $request->validate([
            'nombre' => 'required|string|max:250|unique:centros,nombre',
        ], [
            'nombre.unique' => 'El Centro Ingresado ya Existe.'
        ]);

        try {
            // Insertar el centro
            Centros::create([
                'nombre' => $request->nombre,
                'id_asesor' => Auth::user()->id, // ID del asesor autenticado
            ]);

            // Mensaje de éxito
            return redirect()->back()->with('success', 'Centro Agregado Correctamente.');
        } catch (\Exception $e) {
            // En caso de algún error, pasar mensaje de error
            return redirect()->back()->with('error', 'Hubo un problema al agregar el centro.');
        }
    }

    public function eliminar($id)
    {
        
        $centro = Centros::find($id);

        if (!$centro) {
            return redirect()->back()->with('error', 'Hubo un problema al Eliminiar el centro.');
        }
    
        $centro->delete();
    
        return redirect()->back()->with('success', 'Centro Eliminado Correctamente.');
    }
}
