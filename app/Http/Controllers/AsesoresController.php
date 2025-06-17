<?php

namespace App\Http\Controllers;

use App\Models\Asesores;
use App\Models\Bitacora;
use App\Models\Sucursales;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        return view('modules.dashboard.mantenimientoasesor', compact('rol', 'sucursales', 'asesores'));
    }

    public function InsertarAsesor(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:250',
            'sucursal' => 'required|integer|max:10'
        ]);

        try {
            // Crear el asesor
            $asesor = Asesores::create([
                'nombre' => $request->nombre,
                'id_sucursal' => $request->sucursal
            ]);

            // Obtener el nombre de la sucursal
            $sucursal = \App\Models\Sucursales::find($request->sucursal);
            $nombreSucursal = $sucursal ? $sucursal->nombre : "Sucursal desconocida";

            // Preparar texto para bitácora
            $textoBitacora = "Nuevo asesor registrado:\n";
            $textoBitacora .= "- Nombre: {$asesor->nombre}\n";
            $textoBitacora .= "- Sucursal: {$nombreSucursal}\n";

            // Guardar en bitácora
            Bitacora::create([
                'usuario' => Auth::user()->name,
                'tabla_afectada' => 'ASESORES',
                'accion' => 'CREACIÓN DE ASESOR',
                'datos' => $textoBitacora,
                'fecha' => Carbon::now(),
                'id_asesor' => Auth::user()->id,
                'comentarios' => null
            ]);

            return redirect()->back()->with('success', 'Asesor Agregado Correctamente.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Ocurrio un Problema al Ingresar los Datos.');
        }
    }


    public function updateAsesor(Request $request, $id)
    {
        $asesor = Asesores::findOrFail($id);

        // Guardamos datos anteriores para comparación
        $asesorAnterior = clone $asesor;

        // Actualizar campos
        $asesor->nombre = $request->input('nombre');
        $asesor->id_sucursal = $request->input('sucursal');
        $asesor->save();

        // Obtener nombre sucursal anterior y nueva
        $sucursalAnterior = \App\Models\Sucursales::find($asesorAnterior->id_sucursal);
        $nombreSucursalAnterior = $sucursalAnterior ? $sucursalAnterior->nombre : "Sucursal desconocida";

        $sucursalNueva = \App\Models\Sucursales::find($asesor->id_sucursal);
        $nombreSucursalNueva = $sucursalNueva ? $sucursalNueva->nombre : "Sucursal desconocida";

        // Preparar texto bitácora solo con cambios
        $textoBitacora = "Asesor actualizado:\n";
        $textoBitacora .= "- Nombre: '{$asesorAnterior->nombre}' → '{$asesor->nombre}'\n";

        if ($asesorAnterior->id_sucursal != $asesor->id_sucursal) {
            $textoBitacora .= "- Sucursal: '{$nombreSucursalAnterior}' → '{$nombreSucursalNueva}'\n";
        }

        // Guardar en bitácora solo si hubo cambios
        if (trim($asesorAnterior->nombre) != trim($asesor->nombre) || $asesorAnterior->id_sucursal != $asesor->id_sucursal) {
            Bitacora::create([
                'usuario' => Auth::user()->name,
                'tabla_afectada' => 'ASESORES',
                'accion' => 'ACTUALIZACIÓN DE ASESOR',
                'datos' => $textoBitacora,
                'fecha' => Carbon::now(),
                'id_asesor' => Auth::user()->id,
                'comentarios' => null
            ]);
        }

        return response()->json(['message' => 'Asesor actualizado correctamente']);
    }

    public function obtenerAsesores()
    {
        $asesor = DB::table('asesores')
            ->get();

        return response()->json($asesor);
    }
}
