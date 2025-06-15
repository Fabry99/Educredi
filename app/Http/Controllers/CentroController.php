<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Centros;
use Carbon\Carbon;
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
            $centro = Centros::create([
                'nombre' => $request->nombre,
                'id_asesor' => Auth::user()->id, // ID del asesor autenticado
            ]);

            // Crear texto plano para la bitácora
            $textoBitacora = "";
            $textoBitacora .= "Centro creado: {$centro->nombre}\n";
            $textoBitacora .= "-------------------------\n";

            // Guardar en la bitácora
            Bitacora::create([
                'usuario' => Auth::user()->name,
                'tabla_afectada' => 'CENTROS',
                'accion' => 'CREACIÓN DE CENTRO',
                'datos' => $textoBitacora,
                'fecha' => Carbon::now(),
                'id_asesor' => Auth::user()->id,
                'comentarios' => null // opcional
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
        // Buscar el centro por su ID
        $centro = Centros::find($id);

        if (!$centro) {
            return redirect()->back()->with('error', 'Hubo un problema al eliminar el centro.');
        }

        // Guardar nombre antes de eliminar
        $nombreCentro = $centro->nombre;

        // Texto plano para la bitácora
        $textoBitacora = "";
        $textoBitacora .= "Centro eliminado: {$nombreCentro}\n";
        $textoBitacora .= "-------------------------\n";

        // Guardar en la bitácora
        Bitacora::create([
            'usuario' => Auth::user()->name,
            'tabla_afectada' => 'CENTROS',
            'accion' => 'ELIMINACIÓN DE CENTRO',
            'datos' => $textoBitacora,
            'fecha' => Carbon::now(),
            'id_asesor' => Auth::user()->id,
            'comentarios' => null // o algún comentario si lo tienes
        ]);

        // Eliminar el centro
        $centro->delete();

        return redirect()->back()->with('success', 'Centro eliminado correctamente.');
    }
    public function obtenercentro($id)
    {
        // Buscar el cliente junto con su grupo y centro
        $centro = Centros::find($id);

        // Si el cliente existe, devolver los datos en formato JSON
        if ($centro) {
            return response()->json($centro);
        }

        // Si no se encuentra el cliente, devolver un error
        return response()->json(['error' => 'Cliente no encontrado'], 404);
    }
    public function actualizarCentro(Request $request, $id)
    {
        // Validar los datos recibidos
        $request->validate([
            'nombrecentro' => 'required|string|max:255',
        ]);

        // Obtener el centro a actualizar
        $centro = Centros::find($id);

        if ($centro) {
            // Guardar el nombre anterior
            $nombreAnterior = $centro->nombre;

            // Guardar el nuevo nombre
            $nuevoNombre = $request->input('nombrecentro');

            // Actualizar los datos del centro
            $centro->nombre = $nuevoNombre;
            $centro->save();

            // Construir texto plano para la bitácora
            $textoBitacora = "";
            $textoBitacora .= "Centro actualizado:\n";
            $textoBitacora .= "Nombre anterior: {$nombreAnterior}\n";
            $textoBitacora .= "Nuevo nombre: {$nuevoNombre}\n";
            $textoBitacora .= "-------------------------\n";

            // Guardar en la bitácora
            Bitacora::create([
                'usuario' => Auth::user()->name,
                'tabla_afectada' => 'CENTROS',
                'accion' => 'ACTUALIZACIÓN DE CENTRO',
                'datos' => $textoBitacora,
                'fecha' => Carbon::now(),
                'id_asesor' => Auth::user()->id,
                'comentarios' => null // puedes agregar un campo de comentario si lo deseas
            ]);

            return response()->json(['success' => 'Centro Actualizado con éxito.']);
        }

        return response()->json(['error' => 'Ocurrió un error'], 404);
    }
}
