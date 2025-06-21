<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class cambiardatosPrestamosController extends Controller
{
    public function obtenerClientes(Request $request)
    {
        $id_centro = $request->input('id_centro');
        $id_grupo = $request->input('id_grupo');
        $id_asesor = $request->input('asesorcambiar');

        // Obtener historial primero
        $historial = DB::table('saldoprestamo as sl')
            ->join('clientes as cl', 'sl.id_cliente', '=', 'cl.id')
            ->where('sl.centro', $id_centro)
            ->where('sl.groupsolid', $id_grupo)
            ->when($id_asesor, function ($query, $id_asesor) {
                return $query->where('sl.ASESOR', $id_asesor);
            })
            ->select(
                'sl.id_cliente',
                'cl.nombre',
                'cl.apellido',
                'sl.MONTO',
                'sl.INTERES',
                'sl.FECHAVENCIMIENTO',
                'sl.FECHAAPERTURA',
                'sl.FECHA_PRIMER_PAGO',
                'sl.PLAZO'
            )
            ->get();


        // Recorremos cada préstamo y buscamos el menor "dias"
        foreach ($historial as $prestamo) {


            $dias = DB::table('debeser')
                ->where('id_cliente', $prestamo->id_cliente)
                ->whereDate('fecha_apertura', '<=', $prestamo->FECHAAPERTURA)
                ->whereDate('fecha_vencimiento', '>=', $prestamo->FECHAVENCIMIENTO)
                ->min('dias');

            $prestamo->dias = $dias ?? null;
        }

        // Agrupar por FECHAAPERTURA
        $grupos = $historial->groupBy('FECHAAPERTURA');

        // Formatear respuesta
        $resultado = [];
        foreach ($grupos as $fecha => $items) {
            $resultado[] = [
                'fecha_apertura' => $fecha,
                'prestamos' => $items,
                'total_prestamos' => $items->count(),
                'monto_total' => $items->sum('MONTO')
            ];
        }

        return response()->json($resultado);
    }


    public function actualizarprestamo(Request $request)
    {
        $datos = $request->input('datos', []);
        $id_centro = $request->input('id_centro');
        $id_grupo = $request->input('id_grupo');


        try {
            DB::transaction(function () use ($datos, $id_centro, $id_grupo) {
                foreach ($datos as $index => $prestamo) {

                    if (!isset($prestamo['id_cliente'])) {
                        throw new \Exception("El préstamo #" . ($index + 1) . " no tiene id_cliente");
                    }

                    $idCliente = $prestamo['id_cliente'];
                    $plazoTexto = $prestamo['plazo'] ?? '';
                    $monto = (float)($prestamo['monto'] ?? 0);
                    $tasa = (float)($prestamo['interes'] ?? 0);
                    $fechaApertura = $prestamo['fecha_apertura'] ?? null;
                    $fechaVencimiento = $prestamo['fecha_vencimiento'] ?? null;
                    $dias = (int)($prestamo['dias'] ?? 0);


                    $tasa_iva = 0.13;
                    $plazoNumero = intval(preg_replace('/[^0-9]/', '', $plazoTexto));

                    if ($plazoNumero <= 0) {
                        throw new \Exception("Plazo inválido para cliente $idCliente");
                    }

                    $manejo = 10 / $plazoNumero;
                    $tasa_diaria = ($tasa / 360) / 100;
                    $porcentajemonto = $monto * 0.02;
                    $segurodiario = $porcentajemonto / 365;
                    $tasadiariaparacuota = ($tasa / 365) / 100;
                    $tasaporperiodo = $tasadiariaparacuota * $dias;


                    if ($tasaporperiodo == 0) {
                        throw new \Exception("Tasa por periodo es 0 para cliente $idCliente");
                    }

                    $baseCalculo = pow(1 + $tasaporperiodo, $plazoNumero);
                    $denominador = $baseCalculo - 1;

                    if ($denominador == 0) {
                        throw new \Exception("Denominador cero en cálculo cuota para cliente $idCliente");
                    }

                    $valorcuota = ($monto * $tasaporperiodo * $baseCalculo) / $denominador;

                    $fechaInicio = \DateTime::createFromFormat('d-m-Y', $fechaApertura);
                    $fechaFin = \DateTime::createFromFormat('d-m-Y', $fechaVencimiento);

                    if (!$fechaInicio || !$fechaFin || $dias <= 0) {
                        $errorMsg = "Datos inválidos: fechas incorrectas o días <= 0 para cliente $idCliente";
                        throw new \Exception($errorMsg);
                    }

                    $fechaInicioFormatted = $fechaInicio->format('Y-m-d');
                    $fechaFinFormatted = $fechaFin->format('Y-m-d');


                    $fechasCuotas = [];
                    $fechaTemp = clone $fechaInicio;
                    $fechaTemp->modify("+{$dias} days");

                    while ($fechaTemp <= $fechaFin && count($fechasCuotas) < $plazoNumero) {
                        $fechasCuotas[] = $fechaTemp->format('Y-m-d');
                        $fechaTemp->modify("+{$dias} days");
                    }


                    $saldoPendiente = $monto;
                    $cuotas = [];

                    foreach ($fechasCuotas as $index => $fecha) {
                        $numeroCuota = $index + 1;

                        $interes = $saldoPendiente * $tasa_diaria * $dias;
                        $microseguro = ($segurodiario * $dias) * (1 + $tasa_iva);
                        $iva = $interes * $tasa_iva;
                        $capital = $valorcuota - $interes;

                        if ($numeroCuota === count($fechasCuotas)) {
                            $capital = $saldoPendiente;
                            $interes = $saldoPendiente * $tasa_diaria * $dias;
                            $iva = $interes * $tasa_iva;
                            $valorcuota = $capital + $interes + $iva + $manejo + $microseguro;
                        }

                        $cuotaFinal = ($valorcuota + $iva + $manejo + $microseguro);
                        $saldoPendiente -= $capital;

                        if ($saldoPendiente < 0) {
                            $saldoPendiente = 0;
                        }

                        $cuotaData = [
                            'id_cliente' => $idCliente,
                            'fecha' => $fecha,
                            'numero' => $numeroCuota,
                            'cuota' => $cuotaFinal,
                            'capital' => $capital,
                            'interes' => $interes,
                            'iva' => $iva,
                            'microseguro' => $microseguro,
                            'manejo' => $manejo,
                            'saldo' => $saldoPendiente
                        ];

                        $cuotas[] = $cuotaData;

                    }

                    if (!empty($cuotas)) {
                        $primeraCuota = $cuotas[0];

                        $actualizado = DB::table('saldoprestamo')
                            ->where('id_cliente', $idCliente)
                            ->where('FECHAAPERTURA', $fechaInicioFormatted)
                            ->where('FECHAVENCIMIENTO', $fechaFinFormatted)
                            ->update([
                                'MONTO' => $monto,
                                'SALDO' => $monto,
                                'INTERES' => $tasa,
                                'CUOTA' => $primeraCuota['cuota'],
                                'FECHA_PRIMER_PAGO' => $primeraCuota['fecha'],
                                'updated_at' => now()
                            ]);

                        $actualizarhistorial = DB::table('historial_prestamos')
                            ->where('id_cliente', $idCliente)
                            ->where('fecha_apertura', $fechaInicioFormatted)
                            ->where('fecha_vencimiento', $fechaFinFormatted)
                            ->update([
                                'monto' => $monto,
                                'cuota' => $primeraCuota['cuota'],
                                'interes' => $tasa,
                                'manejo' => $primeraCuota['manejo'],
                                'updated_at' => now()
                            ]);

                    }

                    DB::table('debeser')
                        ->where('id_cliente', $idCliente)
                        ->where('fecha_apertura', $fechaInicioFormatted)
                        ->where('fecha_vencimiento', $fechaFinFormatted)
                        ->delete();

                    foreach ($cuotas as $cuota) {
                        try {
                            DB::table('debeser')->insert([
                                'id_cliente' => $idCliente,
                                'fecha_apertura' => $fechaInicioFormatted,
                                'fecha_vencimiento' => $fechaFinFormatted,
                                'fecha' => $cuota['fecha'],
                                'cuota' => $cuota['cuota'],
                                'saldo' => $cuota['saldo'],
                                'tasa_interes' => $tasa,
                                'dias' => $dias,
                                'plazo' => $plazoNumero,
                                'manejo' => $cuota['manejo'],
                                'seguro' => $cuota['microseguro'],
                                'capital' => $cuota['capital'],
                                'iva' => $cuota['iva'],
                                'intereses' => $cuota['interes'],
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                        } catch (\Exception $e) {
                            throw $e;
                        }
                    }

                    $nombreCliente = DB::table('clientes')
                        ->where('id', $idCliente)
                        ->select('nombre', 'apellido')
                        ->first();

                    $textoBitacora = "CLIENTE: " . (($nombreCliente->nombre ?? '') . ' ' . ($nombreCliente->apellido ?? '')) . "\n";
                    $textoBitacora .= "NUEVO MONTO: $" . round($monto, 2) . "\n";
                    $textoBitacora .= "NUEVA TASA: " . round($tasa, 2) . "%\n";
                    $textoBitacora .= "-------------------------\n";

                    Bitacora::create([
                        'usuario' => Auth::user()->name,
                        'tabla_afectada' => 'HISTORIAL PRESTAMOS',
                        'accion' => 'ACTUALIZACIÓN',
                        'datos' => $textoBitacora,
                        'fecha' => Carbon::now(),
                        'id_asesor' => Auth::user()->id,
                    ]);
                }
            });
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar los préstamos: ' . $e->getMessage()], 500);
        }


        return response()->json([
            'status' => 'success',
            'mensaje' => 'Préstamos actualizados correctamente',
            'total_prestamos' => count($datos)
        ]);
    }
}
