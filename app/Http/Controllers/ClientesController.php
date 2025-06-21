<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Centros;
use App\Models\Centros_Grupos_Clientes;
use App\Models\Clientes;
use App\Models\Grupos;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            $cliente = Clientes::create([
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

            // Obtener texto para la bitácora
            $textoBitacora = "Nuevo cliente registrado:\n";
            $textoBitacora .= "- Nombre: {$request->nombre} {$request->apellido}\n";
            $textoBitacora .= "- Dirección: {$request->direccion}\n";
            $textoBitacora .= "- Género: {$request->genero}\n";
            $textoBitacora .= "- Fecha nacimiento: {$request->fecha_nacimiento}\n";
            $textoBitacora .= "- DUI: {$request->dui}\n";
            $textoBitacora .= "- NIT: {$request->NIT}\n";
            $textoBitacora .= "- Tel. Casa: {$request->telcasa}, Tel. Oficina: {$request->teloficina}\n";
            $textoBitacora .= "- Actividad Económica: {$request->actividadeconomica}\n";
            $textoBitacora .= "- Nacionalidad: {$request->nacionalidad}\n";

            // Bitácora
            Bitacora::create([
                'usuario' => Auth::user()->name,
                'tabla_afectada' => 'CLIENTES',
                'accion' => 'CREACIÓN DE CLIENTE',
                'datos' => $textoBitacora,
                'fecha' => Carbon::now(),
                'id_asesor' => Auth::user()->id,
                'comentarios' => null
            ]);

            return redirect()->back()->with('success', 'Cliente Agregado Correctamente.');
        } catch (\Exception $e) {
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

        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
        ]);

        $cliente = Clientes::findOrFail($id);

        $clienteAnterior = $cliente->replicate();

        // Mapeamos los nombres correctos esperados por el modelo
        $datosActualizar = $request->except([
            'id_centroeditar',
            'id_grupoeditar',
            '_token',
            '_method',
            'id',
            'id_departamentoeditcliente',
            'id_municipioedit'
        ]);

        // Mapear manualmente a los nombres de columnas reales
        $datosActualizar['id_departamento'] = $request->input('id_departamentoeditcliente');
        $datosActualizar['id_municipio'] = $request->input('id_municipioedit');


        $cliente->update($datosActualizar);

        function obtenerNombreDepartamento($id_departamento)
        {
            $dep = \App\Models\Departamentos::find($id_departamento);
            return $dep ? $dep->nombre : $id_departamento;
        }

        function obtenerNombreMunicipio($id_municipio)
        {
            $mun = \App\Models\Municipios::find($id_municipio);
            return $mun ? $mun->nombre : $id_municipio;
        }

        function obtenerNombreCentro($id_centro)
        {
            $cen = \App\Models\Centros::find($id_centro);
            return $cen ? $cen->nombre : $id_centro;
        }

        function obtenerNombreGrupo($id_grupo)
        {
            $gru = \App\Models\Grupos::find($id_grupo);
            return $gru ? $gru->nombre : $id_grupo;
        }

        $campos = [
            'nombre' => 'Nombre',
            'apellido' => 'Apellido',
            'direccion' => 'Dirección',
            'genero' => 'Género',
            'fecha_nacimiento' => 'Fecha nacimiento',
            'sector' => 'Sector',
            'direc_trabajo' => 'Dirección negocio',
            'telefono_casa' => 'Tel. Casa',
            'telefono_oficina' => 'Tel. Oficina',
            'ing_economico' => 'Ingreso económico',
            'egre_economico' => 'Egreso económico',
            'otros_ing' => 'Otros ingresos',
            'dui' => 'DUI',
            'lugar_expe' => 'Lugar expedición',
            'estado_civil' => 'Estado civil',
            'nit' => 'NIT',
            'nombre_conyugue' => 'Cónyuge',
            'id_departamento' => 'Departamento',
            'id_municipio' => 'Municipio',
            'lugar_nacimiento' => 'Lugar nacimiento',
            'persona_dependiente' => 'Persona dependiente',
            'fecha_expedicion' => 'Fecha expedición',
            'nacionalidad' => 'Nacionalidad',
            'act_economica' => 'Actividad económica',
            'ocupacion' => 'Ocupación',
            'puede_firmar' => 'Puede firmar',
            'nrc' => 'NRC',
        ];

        $textoBitacora = "Cliente actualizado:\n";
        $textoBitacora .= "- Nombre completo: {$cliente->nombre} {$cliente->apellido}\n";

        foreach ($campos as $campo => $nombreCampo) {
            $valorAnterior = $clienteAnterior->$campo ?? '';
            $valorNuevo = $cliente->$campo ?? '';

            if ($campo == 'id_departamento') {
                $valorAnterior = obtenerNombreDepartamento($valorAnterior);
                $valorNuevo = obtenerNombreDepartamento($valorNuevo);
            } elseif ($campo == 'id_municipio') {
                $valorAnterior = obtenerNombreMunicipio($valorAnterior);
                $valorNuevo = obtenerNombreMunicipio($valorNuevo);
            }


            if ($valorAnterior != $valorNuevo) {
                if (empty($valorAnterior)) {
                    $textoBitacora .= "- {$nombreCampo}: '{$valorNuevo}'\n";
                } else {
                    $textoBitacora .= "- {$nombreCampo}: '{$valorAnterior}' → '{$valorNuevo}'\n";
                }
            }
        }

        $logBitacora = trim($textoBitacora);
  

        if (strlen($logBitacora) > strlen("Cliente actualizado:\n- Nombre completo: {$cliente->nombre} {$cliente->apellido}\n")) {
            Bitacora::create([
                'usuario' => Auth::user()->name,
                'tabla_afectada' => 'CLIENTES',
                'accion' => 'ACTUALIZACIÓN DE CLIENTE',
                'datos' => $textoBitacora,
                'fecha' => Carbon::now(),
                'id_asesor' => Auth::user()->id,
                'comentarios' => null
            ]);
        } else {
        }

        if ($request->filled('id_centroeditar') && $request->filled('id_grupoeditar')) {
            $yaExisteRelacion = \App\Models\Centros_Grupos_Clientes::where('cliente_id', $cliente->id)
                ->where('centro_id', $request->id_centroeditar)
                ->where('grupo_id', $request->id_grupoeditar)
                ->exists();


            if (!$yaExisteRelacion) {
                \App\Models\Centros_Grupos_Clientes::create([
                    'cliente_id' => $cliente->id,
                    'centro_id' => $request->id_centroeditar,
                    'grupo_id' => $request->id_grupoeditar
                ]);

                $nombreCentro = obtenerNombreCentro($request->id_centroeditar);
                $nombreGrupo = obtenerNombreGrupo($request->id_grupoeditar);

                $textoRelacion = "Relación agregada:\n";
                $textoRelacion .= "- Cliente: {$cliente->nombre} {$cliente->apellido}\n";
                $textoRelacion .= "- Centro: {$nombreCentro}\n";
                $textoRelacion .= "- Grupo: {$nombreGrupo}\n";

                Bitacora::create([
                    'usuario' => Auth::user()->name,
                    'tabla_afectada' => 'CENTROS_GRUPOS_CLIENTES',
                    'accion' => 'RELACIÓN AGREGADA',
                    'datos' => $textoRelacion,
                    'fecha' => Carbon::now(),
                    'id_asesor' => Auth::user()->id,
                    'comentarios' => null
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

        // Buscar la relación que se quiere eliminar
        $registro = Centros_Grupos_Clientes::where('cliente_id', $clienteId)
            ->where('grupo_id', $grupoId)
            ->where('centro_id', $centroId)
            ->first();

        if (!$registro) {
            return response()->json(['error' => 'No se encontró la relación.'], 404);
        }

        // Obtener nombres para la bitácora
        $cliente = Clientes::find($clienteId);
        $grupo = Grupos::find($grupoId);
        $centro = Centros::find($centroId);

        $nombreCliente = $cliente ? "{$cliente->nombre} {$cliente->apellido}" : 'Cliente desconocido';
        $nombreGrupo = $grupo ? $grupo->nombre : 'Grupo desconocido';
        $nombreCentro = $centro ? $centro->nombre : 'Centro desconocido';

        // Construir texto plano para bitácora
        $textoBitacora = "";
        $textoBitacora .= "Eliminación de cliente del grupo\n";
        $textoBitacora .= "Cliente: {$nombreCliente}\n";
        $textoBitacora .= "Grupo: {$nombreGrupo}\n";
        $textoBitacora .= "Centro: {$nombreCentro}\n";
        $textoBitacora .= "-------------------------\n";

        // Guardar en bitácora antes de eliminar
        Bitacora::create([
            'usuario' => Auth::user()->name,
            'tabla_afectada' => 'CENTROS_GRUPOS_CLIENTES',
            'accion' => 'ELIMINACIÓN DE RELACIÓN CLIENTE-GRUPO-CENTRO',
            'datos' => $textoBitacora,
            'fecha' => Carbon::now(),
            'id_asesor' => Auth::user()->id,
        ]);

        // Eliminar la relación
        $registro->delete();

        return response()->json(['success' => 'Cliente eliminado correctamente del grupo.']);
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