<?php

namespace App\Http\Controllers;

use App\Models\Centros;
use App\Models\Grupos;
use App\Models\saldoprestamo;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MovimientocajaController extends Controller
{
    public function caja()
    {
        $rol = Auth::user()->rol;
        $centro = Centros::all();
        $grupos = Grupos::all();

        if ($rol !== 'caja' ) {
            return redirect()->back()->with('error', 'No tienes acceso a esta sección.');
        }
        return view('modules.dashboard.home', compact('rol', 'centro', 'grupos'));

        // return view('modules.dashboard.home')->with('rol', $rol);
    }

    public function obtenerPrestamos(Request $request)
    {
        $id_centro = $request->input('id_centro');
        $id_grupo = $request->input('id_grupo');


        $DatosClientes = saldoprestamo::with('clientes')
            ->where('centro', $id_centro)
            ->where('groupsolid', $id_grupo)
            ->select(
                'id',
                'SALDO',
                'CUOTA',
                'ULTIMA_FECHA_PAGADA',
                'id_cliente',
                'FECHAAPERTURA',
                'FECHAVENCIMIENTO'
            )
            ->get();

        $idsClientes = $DatosClientes->pluck('id_cliente')->unique();

        $debeserTodos = DB::table('debeser')
            ->whereIn('id_cliente', $idsClientes)
            ->orderBy('id_cliente')
            ->orderBy('fecha')
            ->get()
            ->groupBy('id_cliente');

        $respuesta = [
            'datos' => $DatosClientes->map(function ($dato) use ($debeserTodos) {
                $debeserCliente = $debeserTodos[$dato->id_cliente] ?? collect();
                $ultimaFecha = $dato->ULTIMA_FECHA_PAGADA;

                $proximaFila = null;

                if ($debeserCliente->isNotEmpty()) {
                    if ($ultimaFecha) {
                        foreach ($debeserCliente as $index => $fila) {
                            if (\Carbon\Carbon::parse($fila->fecha)->diffInDays($ultimaFecha) <= 1) {
                                $proximaFila = $debeserCliente->get($index + 1);
                                break;
                            }
                        }
                    } else {
                        // Si no hay fecha de última paga, mostrar la primera fila
                        $proximaFila = $debeserCliente->first();
                    }
                }
                $diasTexto = match ((int)($proximaFila->dias ?? 0)) {
                    7 => 'semanal',
                    14 => 'catorcenal',
                    15 => 'quincenal',
                    30 => 'mensual',
                    60 => 'bimestral',
                    365 => 'anual',
                    default => 'otro',
                };


                $resultado = [
                    'id' => $dato->id,
                    'saldo' => $dato->SALDO,
                    'cuota' => $proximaFila->cuota ?? $dato->CUOTA,
                    'ultima_fecha' => $ultimaFecha,
                    'fecha_apertura' => $dato->FECHAAPERTURA,
                    'fecha_vencimiento' => $dato->FECHAVENCIMIENTO,
                    'cliente_id' => $dato->id_cliente,
                    'cliente_nombre' => (optional($dato->clientes)->nombre . ' ' . optional($dato->clientes)->apellido) ?? 'Sin nombre',
                    'proxima_fecha' => $proximaFila->fecha ?? null,
                    'manejo' => $proximaFila->manejo ?? null,
                    'seguro' => $proximaFila->seguro ?? null,
                    'capital' => $proximaFila->capital ?? null,
                    'iva' => $proximaFila->iva ?? null,
                    'intereses' => $proximaFila->intereses ?? null,
                    'datos_debeser' => $proximaFila,
                    'dias' => $diasTexto,

                ];



                return $resultado;
            }),
        ];

        return response()->json($respuesta);
    }
    public function obtenerConteoCuotas(Request $request)
    {

        $id_cliente = $request->input('id_cliente');

        if (!$id_cliente) {
            return response()->json(['error' => 'ID de cliente no proporcionado'], 400);
        }

        // Obtener la ULTIMA_FECHA_PAGADA
        $ultimaFechaPagada = DB::table('saldoprestamo')
            ->where('id_cliente', $id_cliente)
            ->value('ULTIMA_FECHA_PAGADA');


        // Obtener las fechas ordenadas de 'debeser'
        $fechas = DB::table('debeser')
            ->where('id_cliente', $id_cliente)
            ->whereNotNull('fecha')
            ->orderBy('fecha')
            ->pluck('fecha')
            ->map(function ($fecha) {
                return date('Y-m-d', strtotime($fecha));
            })
            ->values()
            ->toArray();

        $conteoTotal = count($fechas);

        $conteoComparativo = 0;

        if ($ultimaFechaPagada !== null) {
            $ultima = new DateTime($ultimaFechaPagada);

            foreach ($fechas as $i => $fechaDebeser) {
                $fecha = new DateTime($fechaDebeser);
                $diff = abs($fecha->diff($ultima)->days);

                // Considera "igual o cercana" si la diferencia es de 0 a 2 días
                if ($diff <= 20) {
                    $conteoComparativo = $i + 1;
                    break;
                }
            }
        }


        return response()->json([
            'conteo_total' => $conteoTotal,
            'conteo_comparativo' => $conteoComparativo,
        ]);
    }
}
