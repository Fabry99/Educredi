<?php

namespace App\Http\Controllers;

use App\Models\Grupos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    public function obtenerGruposPorCentro($id)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $usuario = Auth::user();

        return response()->json([
            'rol' => $usuario->rol,  // <- Asegúrate de que este campo exista en tu tabla users
            'id_usuario' => $usuario->id,
            'grupos' => Grupos::where('id_centros', $id)->get()
        ]);
    }
    public function eliminarGrupo($id)
    {
        // Encontrar el grupo por su ID
        $grupo = Grupos::find($id);

        // Verificar si el grupo existe
        if (!$grupo) {
            return response()->json(['error' => 'Grupo no encontrado'], 404);
        }

        // Eliminar el grupo
        $grupo->delete();

        // Responder con un mensaje de éxito
        return response()->json(['success' => 'Grupo eliminado con éxito.']);
    }

    public function gruposcentros($id)
    {
        $grupos = Grupos::where('id_centros', $id)
            ->get();
    
        $resultados = DB::table('centros_grupos_clientes')
            ->select(
                DB::raw('COUNT(cliente_id) AS clientes_en_grupo'),
                'grupo_id',
                'centro_id'
            )
            ->where('centro_id', $id)
            ->groupBy('centro_id', 'grupo_id')
            ->get();
    
        // Asociar el conteo de clientes con los grupos
        foreach ($grupos as $grupo) {
            $grupo->clientes_count = $resultados->firstWhere('grupo_id', $grupo->id)->clientes_en_grupo ?? 0;
        }
    
        return response()->json(['grupos' => $grupos]);
    }
    
}
