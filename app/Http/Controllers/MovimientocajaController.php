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
    }

    public function obtenerPrestamos(Request $request)
    {
        $id_centro = $request->input('id_centro');
        $id_grupo = $request->input('id_grupo');

        $resultados = DB::table('centros_grupos_clientes as cgc')
            ->joinSub(function ($query) {
                $query->from('saldoprestamo as sp1')
                    ->join(DB::raw("(
              SELECT id_cliente, MAX(id) AS max_id
              FROM saldoprestamo
              GROUP BY id_cliente
          ) as latest"), 'sp1.id', '=', 'latest.max_id')
                    ->select(
                        'sp1.id',
                        'sp1.SALDO',
                        'sp1.CUOTA',
                        'sp1.ULTIMA_FECHA_PAGADA',
                        'sp1.id_cliente',
                        'sp1.FECHAAPERTURA',
                        'sp1.FECHAVENCIMIENTO',
                        'sp1.centro',
                        'sp1.interes'
                    );
            }, 'sp', 'cgc.cliente_id', '=', 'sp.id_cliente')
            ->join('clientes as c', 'c.id', '=', 'sp.id_cliente') // ← AQUÍ agregas el JOIN
            ->join('centros as cen', 'sp.centro', '=', 'cen.id')
            ->where('cgc.grupo_id', $id_grupo)
            ->where('cgc.centro_id', $id_centro)
            ->orderBy('cgc.grupo_id')
            ->select(
                'cgc.grupo_id',
                'sp.id',
                'sp.SALDO',
                'sp.CUOTA',
                'sp.ULTIMA_FECHA_PAGADA',
                'sp.id_cliente',
                'sp.FECHAAPERTURA',
                'sp.FECHAVENCIMIENTO',
                'sp.centro',
                'sp.interes',
                'c.nombre as cliente_nombre',     // ← Incluyes nombre
                'c.apellido as cliente_apellido',  // ← Incluyes apellido
                'cen.nombre as centro_nombre'
            )
            ->get();
        Log::info('Datos obtenidos', $resultados->toArray()); // <-- Esto es correcto
        // Construir array de filtros con cliente + fechas
        $filtros = $resultados->map(function ($item) {
            return [
                'id_cliente' => $item->id_cliente,
                'fecha_apertura' => $item->FECHAAPERTURA,
                'fecha_vencimiento' => $item->FECHAVENCIMIENTO,
            ];
        })->toArray();


        $debeserTodos = DB::table('debeser as d1')
            ->select('d1.*')
            ->join(
                DB::raw('(SELECT id_cliente, MAX(created_at) as max_created FROM debeser GROUP BY id_cliente) as d2'),
                function ($join) {
                    $join->on('d1.id_cliente', '=', 'd2.id_cliente')
                        ->on('d1.created_at', '=', 'd2.max_created');
                }
            )
            ->where(function ($query) use ($filtros) {
                foreach ($filtros as $filtro) {
                    $query->orWhere(function ($q) use ($filtro) {
                        $q->where('d1.id_cliente', $filtro['id_cliente'])
                            ->where('d1.fecha_apertura', $filtro['fecha_apertura'])
                            ->where('d1.fecha_vencimiento', $filtro['fecha_vencimiento']);
                    });
                }
            })
            ->orderBy('d1.fecha')
            ->get()
            ->groupBy('id_cliente')
            ->map(function ($grupo) {
                return $grupo->toArray();
            })
            ->toArray();


        $respuesta = [
            'datos' => $resultados->map(function ($dato) use ($debeserTodos) {
                // Obtener array o colección de registros para el cliente actual
                $debeserCliente = collect($debeserTodos[$dato->id_cliente] ?? []);
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
                    'cliente_nombre' => trim(($dato->cliente_nombre ?? '') . ' ' . ($dato->cliente_apellido ?? '')) ?: 'Sin nombre',
                    'proxima_fecha' => $proximaFila->fecha ?? null,
                    'manejo' => $proximaFila->manejo ?? null,
                    'seguro' => $proximaFila->seguro ?? null,
                    'capital' => $proximaFila->capital ?? null,
                    'iva' => $proximaFila->iva ?? null,
                    'intereses' => $proximaFila->intereses ?? null,
                    'datos_debeser' => $proximaFila,
                    'dias' => $diasTexto,
                    'centro' => $dato->centro_nombre ?? 'Sin centro',
                    'interes' => $dato->interes,

                ];



                return $resultado;
            }),


        ];
        log::info('Datos obtenidos debeser', $respuesta); // <-- Esto es correcto

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
