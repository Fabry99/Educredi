<?php

namespace App\Http\Controllers;

use App\Models\bancos;
use App\Models\Bitacora;
use App\Models\Centros;
use App\Models\debeser;
use App\Models\Grupos;
use App\Models\saldoprestamo;
use Carbon\Carbon;
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

        $resultados = DB::select("
    WITH cgc_filtered AS (
        SELECT cliente_id
        FROM centros_grupos_clientes
        WHERE centro_id = ? AND grupo_id = ?
    ),
    cgc_count AS (
        SELECT COUNT(*) AS total FROM cgc_filtered
    ),
    saldos_numerados AS (
        SELECT sp.id, sp.id_cliente, sp.SALDO, sp.CUOTA, sp.ULTIMA_FECHA_PAGADA,
               sp.FECHAAPERTURA, sp.FECHAVENCIMIENTO, sp.centro, sp.INTERES, sp.groupsolid,
               ROW_NUMBER() OVER (PARTITION BY sp.id_cliente ORDER BY sp.id DESC) AS rn
        FROM saldoprestamo AS sp
        WHERE sp.centro = ? AND sp.groupsolid = ? 
          AND sp.id_cliente IN (SELECT cliente_id FROM cgc_filtered)
    )
    SELECT 
      sn.id,
      sn.id_cliente,
      c.nombre AS cliente_nombre,
      c.apellido AS cliente_apellido,
      sn.SALDO,
      sn.CUOTA,
      sn.ULTIMA_FECHA_PAGADA,
      sn.FECHAAPERTURA,
      sn.FECHAVENCIMIENTO,
      sn.centro,
      cen.nombre AS centro_nombre,
      sn.INTERES,
      sn.groupsolid,
      cc.total AS total_registros
    FROM saldos_numerados sn
    JOIN cgc_count cc ON 1=1
    JOIN clientes c ON c.id = sn.id_cliente
    JOIN centros cen ON cen.id = sn.centro
    WHERE sn.rn = 1
    ORDER BY sn.id DESC
", [
            $id_centro,
            $id_grupo,
            $id_centro,
            $id_grupo
        ]);


        $resultados = collect($resultados);

        // Construir array de filtros con cliente + fechas
        $filtros = collect($resultados)->map(function ($item) {
            return [
                'id_cliente' => $item->id_cliente,
                'fecha_apertura' => $item->FECHAAPERTURA,
                'fecha_vencimiento' => $item->FECHAVENCIMIENTO,
            ];
        })->toArray();

        $debeserTodos = DB::table('debeser as d1')
            ->select('d1.*')
            ->join(
                DB::raw('(
            SELECT id_cliente, fecha_apertura, fecha_vencimiento, MAX(created_at) as max_created
            FROM debeser
            GROUP BY id_cliente, fecha_apertura, fecha_vencimiento
        ) as d2'),
                function ($join) {
                    $join->on('d1.id_cliente', '=', 'd2.id_cliente')
                        ->on('d1.fecha_apertura', '=', 'd2.fecha_apertura')
                        ->on('d1.fecha_vencimiento', '=', 'd2.fecha_vencimiento')
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

                ];



                return $resultado;
            }),


        ];


        return response()->json($respuesta);
    }
    public function obtenerConteoCuotas(Request $request)
    {

        $id_cliente = $request->input('id_cliente');
        $fechaApertura = $request->input('Apertura');
        $fechaVencimiento = $request->input('Vencimiento');
        $id_centro = $request->input('centroId');
        $id_grupo = $request->input('grupoId');

        if (!$id_cliente) {
            return response()->json(['error' => 'ID de cliente no proporcionado'], 400);
        }
        try {
            $fechaApertura = Carbon::createFromFormat('d-m-Y', $fechaApertura)->format('Y-m-d');
            $fechaVencimiento = Carbon::createFromFormat('d-m-Y', $fechaVencimiento)->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Formato de fecha inválido'], 400);
        }

        $totalcuotas = DB::table('movimientos_presta')->where('id_cliente', $id_cliente)
            ->where('fecha_apertura', $fechaApertura)
            ->where('fecha_vencimiento', $fechaVencimiento)
            ->where('id_centro', $id_centro)
            ->where('id_grupo', $id_grupo)
            ->sum('valor_cuota');

        // Obtener la ULTIMA_FECHA_PAGADA
        $ultimaFechaPagada = DB::table('saldoprestamo')
            ->where('id_cliente', $id_cliente)
            ->where('FECHAAPERTURA', $fechaApertura)
            ->where('FECHAVENCIMIENTO', $fechaVencimiento)
            ->where('centro', $id_centro)
            ->where('groupsolid', $id_grupo)
            ->value('ULTIMA_FECHA_PAGADA');

        $subquery = DB::table('debeser')
            ->selectRaw('COUNT(*) AS total_filas, MAX(created_at) AS max_created_at, id_cliente, fecha_apertura, fecha_vencimiento')
            ->where('id_cliente', $id_cliente)
            ->where('fecha_apertura', $fechaApertura)
            ->where('fecha_vencimiento', $fechaVencimiento)
            ->groupBy('id_cliente', 'fecha_apertura', 'fecha_vencimiento');

        $registros = DB::table('debeser as d')
            ->joinSub($subquery, 't', function ($join) {
                $join->on('d.id_cliente', '=', 't.id_cliente')
                    ->on('d.fecha_apertura', '=', 't.fecha_apertura')
                    ->on('d.fecha_vencimiento', '=', 't.fecha_vencimiento')
                    ->on('d.created_at', '=', 't.max_created_at');
            })
            ->orderBy('d.fecha', 'asc')
            ->select('d.*', 't.total_filas')
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
            'total_cuotas' => $totalcuotas,
        ]);
    }

    public function obtenerEstadoCuentaDebeser(Request $request)
    {
        $id_cliente = $request->input('id_cliente');
        $fechaApertura = $request->input('FechaApertura');
        $fechaVencimiento = $request->input('FechaVencimiento');


        try {
            $fechaApertura = Carbon::createFromFormat('d-m-Y', $fechaApertura)->format('Y-m-d');
            $fechaVencimiento = Carbon::createFromFormat('d-m-Y', $fechaVencimiento)->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Formato de fecha inválido'], 400);
        }

        $registros = DB::table('debeser')
            ->select('fecha', 'cuota', 'capital', 'intereses', 'iva', 'saldo', 'tasa_interes')
            ->addSelect(DB::raw('COUNT(*) OVER () AS total_filas'))
            ->where('id_cliente', $id_cliente)
            ->where('fecha_apertura', $fechaApertura)
            ->where('fecha_vencimiento', $fechaVencimiento)
            ->where('created_at', function ($query) use ($id_cliente, $fechaApertura, $fechaVencimiento) {
                $query->selectRaw('MAX(created_at)')
                    ->from('debeser')
                    ->where('id_cliente', $id_cliente)
                    ->where('fecha_apertura', $fechaApertura)
                    ->where('fecha_vencimiento', $fechaVencimiento);
            })
            ->get();

        $registrosSaldoPrestamo = saldoprestamo::with('asesor')
            ->where('id_cliente', $id_cliente)
            ->first();
        return response()->json([
            'debeser' => $registros,

            'nombreAsesor' => $registrosSaldoPrestamo->asesor->nombre ?? null,
        ]);
    }

    public function ObtenerComprobante()
    {
        $ultimoComprobante = DB::table('movimientos_presta')->max('comprobante');

        $nuevoComprobante = $ultimoComprobante ? $ultimoComprobante + 1 : 1000;


        return response()->json([
            'comprobante' => $nuevoComprobante
        ]);
    }

    public function AlmacenarCuota(Request $request)
    {

        DB::beginTransaction();
        try {
            $datos = $request->input('datos');

            if (!$datos || !is_array($datos)) {
                return response()->json(['error' => 'Datos inválidos o vacíos'], 400);
            }
            $grupoId = null;
            $centroId = null;
            $fechaApertura = null;
            $fechaVencimiento = null;

            foreach ($datos as $index => $fila) {

                $fechaAbono = Carbon::parse($fila['fecha_abono'])->format('Y-m-d');
                $fechaApertura = Carbon::parse($fila['fecha_apertura'])->format('Y-m-d');
                $fechaVencimiento = Carbon::parse($fila['fecha_vencimiento'])->format('Y-m-d');
                $fechaContable = Carbon::parse($fila['fecha_contable'])->format('Y-m-d');

                $grupoId = $fila['id_grupo'];
                $centroId = $fila['id_centro'];
                // Insert en movimientos_presta
                DB::table('movimientos_presta')->insert([
                    'id_cliente' => $fila['cliente_id'],
                    'fecha' => $fechaAbono,
                    'comprobante' => $fila['comprobante'],
                    'valor_cuota' => $fila['cuota'],
                    'saldo' => $fila['saldo'],
                    'int_apli' => $fila['intereses'],
                    'manejo' => $fila['manejo'],
                    'seguro' => $fila['seguro'],
                    'iva' => $fila['iva'],
                    'capital' => $fila['capital'],
                    'dias' => $fila['dias'],
                    'fecha_apertura' => $fechaApertura,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'fecha_conta' => $fechaContable,
                    'ctabanco' => $fila['id_cuenta'],
                    'id_centro' => $fila['id_centro'],
                    'id_grupo' => $fila['id_grupo'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);


                // Bitácora SOLO para movimientos_presta
                Bitacora::create([
                    'usuario' => Auth::user()->name,
                    'tabla_afectada' => 'HISTORIAL DE PAGOS',
                    'accion' => 'INGRESAR',
                    'datos' => json_encode([
                        'CLIENTE' => $fila['cliente_id'],
                        'FECHA' => $fechaAbono,
                        'COMPROBANTE' => $fila['comprobante'],
                        'SALDO' => $fila['saldo'],
                        'VALOR CUOTA' => $fila['cuota'],
                        'CAPITAL' => $fila['capital'],
                        'FECHA APERTURA' => $fechaApertura,
                        'FECHA VENCIMIENTO' => $fechaVencimiento,
                        'FECHA CONTABLE' => $fechaContable,
                        'FECHA ABONO' => $fechaAbono,
                        'CENTRO' => $fila['id_centro'],
                        'GRUPO' => $fila['id_grupo'],
                    ]),
                    'fecha' => Carbon::now(),
                    'id_asesor' => Auth::user()->id,
                ]);

                // Actualización de saldoprestamo (sin bitácora)
                DB::table('saldoprestamo')
                    ->where('id_cliente', $fila['cliente_id'])
                    ->where('centro', $fila['id_centro'])
                    ->where('groupsolid', $fila['id_grupo'])
                    ->whereDate('FECHAAPERTURA', $fechaApertura)
                    ->whereDate('FECHAVENCIMIENTO', $fechaVencimiento)
                    ->update([
                        'SALDO' => $fila['saldo'],
                        'ULTIMA_FECHA_PAGADA' => $fechaAbono,
                        'updated_at' => now()
                    ]);

                
                // Obtener todos los clientes para ese grupo y centro

            }
            // 1. Obtener los IDs de clientes filtrados
            $clientesFiltrados = DB::table('centros_grupos_clientes')
                ->where('centro_id', $centroId)
                ->where('grupo_id', $grupoId)
                ->pluck('cliente_id');

            // 2. Obtener el conteo total
            $totalClientes = $clientesFiltrados->count();

            // 3. Consulta para obtener los últimos registros por cliente
            $ultimosSaldos = DB::table('saldoprestamo as sp')
                ->where('sp.centro', $centroId)
                ->where('sp.groupsolid', $grupoId)
                ->whereIn('sp.id_cliente', $clientesFiltrados)
                ->whereIn('sp.id', function ($query) use ($clientesFiltrados, $centroId, $grupoId) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('saldoprestamo')
                        ->where('centro', $centroId)
                        ->where('groupsolid', $grupoId)
                        ->whereIn('id_cliente', $clientesFiltrados)
                        ->groupBy('id_cliente');
                });

            // 4. Obtener los resultados
            $resultados = $ultimosSaldos->orderByDesc('id_cliente')
                ->take($totalClientes)
                ->get();

            // 5. Verificar si todos los saldos son 0
            $todosSaldosCero = $resultados->every(function ($item) {
                return $item->SALDO == 0; // Asegúrate de que 'saldo' es el nombre correcto de la columna
            });

            // 6. Actualizar conteo_rotacion si todos los saldos son 0
            if ($todosSaldosCero && $totalClientes > 0) {
                DB::table('grupos')
                    ->where('id_centros', $centroId)
                    ->where('id', $grupoId)
                    ->increment('conteo_rotacion'); // Incrementa en 1 el valor actual
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Cuotas almacenadas correctamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al almacenar las cuotas: ' . $e->getMessage()
            ], 500);
        }
    }
}
