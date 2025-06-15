<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Centros;
use App\Models\Grupos;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GruposController extends Controller
{
    public function savegroup(Request $request)
    {
        // Validación
        $request->validate([
            'nombre' => 'required|string|max:250',
            'id_centros' => 'required|exists:centros,id',
        ]);

        try {
            // Obtener el centro para registrar el nombre
            $centro = Centros::find($request->id_centros);

            if (!$centro) {
                return redirect()->back()->with('error', 'Centro no encontrado.');
            }

            // Crear el grupo
            $grupo = Grupos::create([
                'nombre' => $request->nombre,
                'id_centros' => $request->id_centros,
                'id_asesor' => Auth::user()->id,
            ]);

            // Crear texto para bitácora
            $textoBitacora = "";
            $textoBitacora .= "Grupo creado: {$grupo->nombre}\n";
            $textoBitacora .= "Centro asociado: {$centro->nombre}\n";
            $textoBitacora .= "-------------------------\n";

            // Guardar en bitácora
            Bitacora::create([
                'usuario' => Auth::user()->name,
                'tabla_afectada' => 'GRUPOS',
                'accion' => 'CREACIÓN DE GRUPO',
                'datos' => $textoBitacora,
                'fecha' => Carbon::now(),
                'id_asesor' => Auth::user()->id,
                'comentarios' => null // opcional
            ]);

            return redirect()->back()->with('success', 'Grupo Agregado Correctamente.');
        } catch (\Exception $e) {
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
    public function eliminarGrupo(Request $request, $id)
    {
        // Encontrar el grupo con centro relacionado
        $grupo = Grupos::with('centro')->find($id);

        if (!$grupo) {
            return response()->json(['error' => 'Grupo no encontrado'], 404);
        }

        $motivo = $request->input('comentarios', ''); // si quieres capturar motivo, o lo defines aquí

        $nombreGrupo = $grupo->nombre;
        $nombreCentro = $grupo->centro ? $grupo->centro->nombre : 'Centro desconocido';

        // Construir texto plano para bitácora
        $textoBitacora = "";
        $textoBitacora .= "Eliminación de grupo\n";
        $textoBitacora .= "Centro: {$nombreCentro}\n";
        $textoBitacora .= "Grupo: {$nombreGrupo}\n";
        $textoBitacora .= "-------------------------\n";

        // Guardar en bitácora antes de eliminar
        Bitacora::create([
            'usuario' => Auth::user()->name,
            'tabla_afectada' => 'GRUPOS',
            'accion' => 'ELIMINACIÓN DE GRUPO',
            'datos' => $textoBitacora,
            'fecha' => Carbon::now(),
            'id_asesor' => Auth::user()->id,
            'comentarios' => $motivo
        ]);

        // Eliminar el grupo
        $grupo->delete();

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
