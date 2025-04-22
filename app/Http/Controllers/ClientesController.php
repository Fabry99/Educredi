<?php

namespace App\Http\Controllers;

use App\Models\Centros_Grupos_Clientes;
use App\Models\Clientes;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ClientesController extends Controller
{
    public function saveclient(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:250',
            'apellido' => 'required|string|max:250',
            'direccion' => 'required|string|max:250',
            'genero' => 'required|string|max:50',
            'fecha_nacimiento' => 'required|date',
            'sector' => 'required|string|max:250',
            'dir_negocio' => 'required|string|max:250',
            'telcasa' => 'required|string|max:8',
            'teloficina' => 'required|string|max:8',
            'sueldo' => 'nullable|numeric|min:0|max:999999.99',
            'egreso' => 'nullable|numeric|min:0|max:999999.99',
            'otroingreso' => 'nullable|numeric|min:0|max:999999.99',
            'dui' => 'required|string|max:10',
            'expedida' => 'required|string|max:250',
            'estado_civil' => 'required|in:soltero,casado,divorciado,viudo',
            'NIT' => 'required|string|max:17',
            'conyugue' => 'nullable|string|max:250',
            'id_departamento' => 'required|exists:departamentos,id',
            'id_municipio' => 'required|exists:municipios,id',
            'lugarnacimiento' => 'nullable|string|max:250',
            'perdependiente' => 'nullable|string|max:20',
            'expedicion' => 'required|date',
            'nacionalidad' => 'required|string|max:250',
            'actividadeconomica' => 'required|string|max:250',
            'ocupacion' => 'nullable|string|max:250',
            'firma' => 'required|in:si,no',
            'nrc' => 'nullable|string|max:22'



        ]);
        try {
            // Insertar el centro
            Clientes::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'direccion' => $request->direccion,
                'genero' => $request->genero,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'sector' => $request->sector,
                'direc_trabajo' => $request->dir_negocio,
                'telefono_casa' => $request->telcasa,
                'telefono_oficina' => $request->teloficina,
                'ing_economico' => $request->sueldo,
                'egre_economico' => $request->egreso,
                'otros_ing' => $request->otroingreso,
                'dui' => $request->dui,
                'lugar_expe' => $request->expedida,
                'estado_civil' => $request->estado_civil,
                'nit' => $request->NIT,
                'nombre_conyugue' => $request->conyugue,
                'id_departamento' => $request->id_departamento,
                'id_municipio' => $request->id_municipio,
                'lugar_nacimiento' => $request->lugarnacimiento,
                'persona_dependiente' => $request->perdependiente,
                'fecha_expedicion' => $request->expedicion,
                'nacionalidad' => $request->nacionalidad,
                'act_economica' => $request->actividadeconomica,
                'ocupacion' => $request->ocupacion,
                'puede_firmar' => $request->firma,
                'nrc' => $request->nrc,


            ]);

            // Mensaje de éxito
            return redirect()->back()->with('success', 'Cliente Agregado Correctamente.');
        } catch (\Exception $e) {
            // En caso de algún error, pasar mensaje de error
            return redirect()->back()->with('error', 'Hubo un Problema al Agregar el Cliente.');
        }
    }
    public function obtenerCliente($id)
    {
        // Buscar el cliente junto con su grupo y centro
        $cliente = Clientes::with(['departamento', 'municipio'])->find($id);

        // Si el cliente existe, devolver los datos en formato JSON
        if ($cliente) {
            return response()->json($cliente);
        }

        // Si no se encuentra el cliente, devolver un error
        return response()->json(['error' => 'Cliente no encontrado'], 404);
    }

    public function updateclient(Request $request)
    {
        $id = $request->id;

        // Validar campos básicos del cliente
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
        ]);

        // Actualizar datos del cliente (exceptuando los campos de relación)
        $cliente = Clientes::findOrFail($id);
        $cliente->update($request->except(['id_centroeditar', 'id_grupoeditar']));

        // Verifica que ambos campos estén presentes y no sean nulos
        if ($request->filled('id_centroeditar') && $request->filled('id_grupoeditar')) {

            // Verifica que no exista ya la relación
            $yaExisteRelacion = \App\Models\Centros_Grupos_Clientes::where('cliente_id', $cliente->id)
                ->where('centro_id', $request->id_centroeditar)
                ->where('grupo_id', $request->id_grupoeditar)
                ->exists();

            if (!$yaExisteRelacion && $request->filled('id_centroeditar') && $request->filled('id_grupoeditar')) {
                \App\Models\Centros_Grupos_Clientes::create([
                    'cliente_id' => $cliente->id,
                    'centro_id' => $request->id_centroeditar,
                    'grupo_id' => $request->id_grupoeditar
                ]);
            }
        }

        return redirect()->back()->with('success', 'Cliente actualizado correctamente');
    }

    public function clientesPorGrupo($grupoId)
    {
        // Obtén los clientes que pertenecen al grupo
        $clientes = Centros_Grupos_Clientes::where('grupo_id', $grupoId)->with('clientes')->get();

        // Retorna los clientes en formato JSON
        return response()->json($clientes);
    }

    public function eliminarDelGrupo(Request $request)
{
    $clienteId = $request->input('cliente_id');
    $grupoId = $request->input('grupo_id');
    $centroId = $request->input('centro_id');
    // Validar o buscar si existe la relación
    $registro = Centros_Grupos_Clientes::where('cliente_id', $clienteId)
                ->where('grupo_id', $grupoId)
                ->where('centro_id', $centroId)
                ->first();

    if ($registro) {
        $registro->delete(); // elimina la relación del grupo

        return response()->json(['success' => 'Cliente eliminado correctamente del grupo.']);
    }

    return response()->json(['error' => 'No se encontró la relación.'], 404);
}

}

// $cliente = Clientes::find($id);

        // if ($cliente) {
        //     // Eliminar el cliente del grupo y centro
        //     $cliente->id_grupo = null;
        //     $cliente->id_centro = null;
        //     $cliente->save();

        //     return response()->json(['success' => 'Cliente Eliminado Correctamente.']);
        // }

        // return response()->json(['error' => 'Hubo un problema al eliminar al cliente.']);