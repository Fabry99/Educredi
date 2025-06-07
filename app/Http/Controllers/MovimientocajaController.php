<?php

namespace App\Http\Controllers;

use App\Models\bancos;
use App\Models\Centros;
use App\Models\debeser;
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
        $bancos = bancos::all();

        if ($rol !== 'caja') {
            return redirect()->back()->with('error', 'No tienes acceso a esta sección.');
        }
        return view('modules.dashboard.home', compact('rol', 'centro', 'grupos', 'bancos'));

        // return view('modules.dashboard.home')->with('rol', $rol);
    }

    public function obtenerPrestamos(Request $request)
    {
        $id_centro = $request->input('id_centro');
        $id_grupo = $request->input('id_grupo');


        $DatosClientes = saldoprestamo::with(['clientes', 'centros'])
            ->where('centro', $id_centro)
            ->where('groupsolid', $id_grupo)
            ->select(
                'id',
                'SALDO',
                'CUOTA',
                'ULTIMA_FECHA_PAGADA',
                'id_cliente',
                'FECHAAPERTURA',
                'FECHAVENCIMIENTO',
                'centro',
                'interes',
            )
            ->get();

        $idsClientes = $DatosClientes->pluck('id_cliente')->unique();

        $debeserTodos =  DB::table('debeser as d1')
            ->selectRaw('d1.*')
            ->join(
                DB::raw('(SELECT id_cliente, MAX(created_at) as max_created FROM debeser GROUP BY id_cliente) as d2'),
                function ($join) {
                    $join->on('d1.id_cliente', '=', 'd2.id_cliente')
                        ->on('d1.created_at', '=', 'd2.max_created');
                }
            )
            ->whereIn('d1.id_cliente', $idsClientes)
            ->orderBy('d1.fecha')
            ->get();

        $respuesta = [
            'datos' => $DatosClientes->map(function ($dato) use ($debeserTodos) {
                $debeserCliente = $debeserTodos->where('id_cliente', $dato->id_cliente)->values();
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
                    'centro' => $dato->centros->nombre ?? 'Sin centro',
                    'interes' => $dato->interes,

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


        $registros = DB::table('debeser')
            ->selectRaw('*, COUNT(*) OVER () AS total_filas')
            ->where('id_cliente', $id_cliente)
            ->where('created_at', function ($query) use ($id_cliente) {
                $query->selectRaw('MAX(created_at)')
                    ->from('debeser')
                    ->where('id_cliente', $id_cliente);
            })
            ->orderBy('fecha') // Asegúrate de que estén en orden ascendente por fecha
            ->get();

        $conteoTotal = $registros->isNotEmpty() ? $registros[0]->total_filas : 0;
        $conteoComparativo = 0;

        if ($ultimaFechaPagada !== null && $registros->isNotEmpty()) {
            $ultima = new DateTime($ultimaFechaPagada);

            foreach ($registros as $i => $registro) {
                $fechaDebeser = new DateTime($registro->fecha);
                $margenDias = isset($registro->dias) ? intval($registro->dias) : 0;

                // Si la última fecha pagada fue ANTES de la fecha del debeser
                if ($ultima <= $fechaDebeser) {
                    $diff = $fechaDebeser->diff($ultima)->days;

                    if ($diff <= $margenDias) {
                        // Posición encontrada con margen válido
                        $conteoComparativo = $i + 1; // Sumar 1 porque index empieza en 0
                        break;
                    }
                }
            }
        }




        return response()->json([
            'conteo_total' => $conteoTotal,
            'conteo_comparativo' => $conteoComparativo,
        ]);
    }

    public function obtenerEstadoCuentaDebeser(Request $request)
    {
        $id_cliente = $request->input('id_cliente');
        $fechaApertura = $request->input('FechaApertura');
        $fechaVencimiento = $request->input('FechaVencimiento');



        $registros = DB::table('debeser')
            ->selectRaw('fecha, cuota, capital, intereses, iva, saldo,tasa_interes, COUNT(*) OVER () AS total_filas')
            ->where('id_cliente', $id_cliente)
            ->whereBetween('fecha', [$fechaApertura, $fechaVencimiento])
            ->where('created_at', function ($query) use ($id_cliente, $fechaApertura, $fechaVencimiento) {
                $query->selectRaw('MAX(created_at)')
                    ->from('debeser')
                    ->where('id_cliente', $id_cliente)
                    ->whereBetween('fecha', [$fechaApertura, $fechaVencimiento]);
            })
            ->get();
        $registrosSaldoPrestamo = saldoprestamo::with('asesor')
            ->where('id_cliente', $id_cliente)
            ->first();
        log::info('datos del js', $registrosSaldoPrestamo->toArray());
        return response()->json([
            'debeser' => $registros,

            'nombreAsesor' => $registrosSaldoPrestamo->asesor->nombre ?? null,
        ]);
    }
}
