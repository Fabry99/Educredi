<?php

namespace App\Http\Controllers;

use App\Models\Centros_Grupos_Clientes;
use App\Models\saldoprestamo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransferenciacarteraController extends Controller
{


    public function obtenerGrupos($id_centro)
    {

        $grupos = Centros_Grupos_Clientes::where('centro_id', $id_centro)
            ->with('grupos')
            ->get();
        return response()->json($grupos);
    }

    public function obtenerDatosTabla($id_asesor, $id_grupo, $id_centro)
    {
        Log::info("Datos del JS", [
            'id_asesor' => $id_asesor,
            'id_grupo' => $id_grupo,
            'id_centro' => $id_centro,
        ]);

        $DatosClientes = saldoprestamo::with('clientes') // Asegúrate de que la relación esté definida
            ->where('centro', $id_centro)
            ->where('groupsolid', $id_grupo)
            ->where('ASESOR', $id_asesor)
            ->select('id', 'MONTO', 'id_cliente', 'FECHAAPERTURA')
            ->get();

        $totalFilas = $DatosClientes->count();
        $totalMonto = $DatosClientes->sum('MONTO');

        // Estructura de respuesta con nombres incluidos
        $respuesta = [
            'datos' => $DatosClientes->map(function ($dato) {
                return [
                    'id' => $dato->id,
                    'monto' => $dato->MONTO,
                    'fecha_apertura' => $dato->FECHAAPERTURA,
                    'cliente_id' => $dato->id_cliente,
                    'cliente_nombre' => optional($dato->clientes)->nombre
                        . ' ' . optional($dato->clientes)->apellido ?? 'Sin nombre',
                ];
            }),
            'total_registros' => $totalFilas,
            'total_monto' => $totalMonto,
        ];

        // Opcional: mostrar en el log
        Log::info('📋 Datos enviados al frontend:', $respuesta);

        return response()->json($respuesta);
    }
}
