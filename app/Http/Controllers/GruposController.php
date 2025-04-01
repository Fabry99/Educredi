<?php

namespace App\Http\Controllers;

use App\Models\Grupos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GruposController extends Controller
{
    public function savegroup(Request $request)
    {
        // Validación
        $request->validate([
            'nombre' => 'required|string|max:250',
            'id_centros' => 'required|exists:centros,id', // Asegúrate de que el centro existe
        ]);
    
        try {
            // Crear el grupo
            Grupos::create([
                'nombre' => $request->nombre,  // El nombre del grupo
                'id_centros' => $request->id_centros,  // El ID del centro seleccionado
                'id_asesor' => Auth::user()->id,  // El ID del asesor autenticado
            ]);
    
            // Mensaje de éxito
            return redirect()->back()->with('success', 'Grupo Agregado Correctamente.');
        } catch (\Exception $e) {
            // Error en la creación
            return redirect()->back()->with('error', 'Hubo un problema al agregar el grupo.');
        }
    }
}
