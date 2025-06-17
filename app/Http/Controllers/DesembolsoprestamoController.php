<?php

namespace App\Http\Controllers;

use App\Models\Aprobacion;
use App\Models\Asesores;
use App\Models\bancos;
use App\Models\Bitacora;
use App\Models\Centros_Grupos_Clientes;
use App\Models\Clientes;
use App\Models\Colector;
use App\Models\debeser;
use App\Models\Formapago;
use App\Models\HistorialPrestamos;
use App\Models\Linea;
use App\Models\saldoprestamo;
use App\Models\SpecialPassword;
use App\Models\Sucursales;
use App\Models\Supervisores;
use App\Models\Tipopago;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
use DateInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DesembolsoprestamoController extends Controller
{
    public function creditos()
    {
        $rol = Auth::user()->rol;
        $clientes = Clientes::with('saldoprestamo')->get();
        $sucursales = Sucursales::all();
        $linea = Linea::all();
        $supervisor = Supervisores::all();
        $colector = Colector::all();
        $aprobaciones = Aprobacion::all();
        $tipopago = Tipopago::all();
        $formapago = Formapago::all();
        $bancos = bancos::all();
        $asesores = Asesores::all();
        if ($rol !== 'contador' && $rol !== 'administrador') {
            return redirect()->back()->with('error', 'No tienes acceso a esta sección.');
        }

        // Si el rol es 'contador', cargar la vista
        return view('modules.dashboard.desembolso', compact(
            'rol',
            'clientes',
            'sucursales',
            'linea',
            'supervisor',
            'colector',
            'aprobaciones',
            'tipopago',
            'formapago',
            'bancos',
            'asesores'
        ));
    }

    public function obtenerCentrosGruposClientes($id)
    {
        $clientesgrupos = Centros_Grupos_Clientes::with('centros', 'grupos')->where('cliente_id', $id)->get();

        if ($clientesgrupos->isNotEmpty()) {
            return response()->json($clientesgrupos);
            dd();
        }

        return response()->json(['error' => 'No se encontraron datos'], 404);
    }
    public function obtenergruposclientes($centro_id, $grupo_id)
    {
        $grupoclientes = Centros_Grupos_Clientes::with('clientes')
            ->where('centro_id', $centro_id)
            ->where('grupo_id', $grupo_id)
            ->get();

        if ($grupoclientes->isNotEmpty()) {
            // Extraer solo los clientes
            $clientes = $grupoclientes->pluck('clientes')->flatten(); // aplanar por si hay varios

            // Transformar a formato deseado
            $clientesFormateados = $clientes->map(function ($cliente) {
                return [
                    'id'     => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'apellido' => $cliente->apellido,
                ];
            });

            return response()->json($clientesFormateados);
        }

        return response()->json(['error' => 'Error al obtener los datos'], 404);
    }

    public function almacenarPrestamos(Request $request)
    {


        $datosAInsertar = [];
        $datosSaldoprestamo = [];
        $datosParaPDF = [];

        // Recuperar todos los clientes de una vez
        $idsClientes = array_map(function ($prestamo) {
            return $prestamo['id'];
        }, $request->prestamos);

        $clientes = DB::table('clientes')
            ->whereIn('id', $idsClientes)
            ->select('id', 'nombre', 'apellido')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->id => $item->nombre . ' ' . $item->apellido];
            });

        $gruposCentros = [];

        // Preparar los datos a insertar
        foreach ($request->prestamos as $prestamo) {




            // Obtener los datos necesarios del préstamo
            $fechaApertura = $prestamo['detalleCalculo']['fechaapertura']; // Fecha de apertura
            $fechaVencimiento = $prestamo['detalleCalculo']['fechavencimiento']; // Fecha de vencimiento
            $fechaDebeSer = $prestamo['detalleCalculo']['fechaDebeSer'];
            $diasPorPago = $prestamo['detalleCalculo']['parametros']['diasporpago']; // Días por pago
            $idCliente = $prestamo['id']; // ID del cliente
            $fechaPagoCadaMibro = $prestamo['fechaMiembro']; //Fecha de pago para cada miembro
            $nombreCliente = isset($clientes[$idCliente]) ? $clientes[$idCliente] : 'Desconocido';
            $cuotaFinal = $prestamo['detalleCalculo']['cuotaFinal']; // Cuota final
            $monto = $prestamo['monto']; // Monto
            $tasa = $prestamo['tasa']; // Tasa de interés
            $plazo = $prestamo['detalleCalculo']['parametros']['cantPagos']; // Plazo de pagos
            $manejo = $prestamo['detalleCalculo']['manejo']; // Manejo
            $seguro = $prestamo['detalleCalculo']['parametros']['seguro']; // Seguro
            $capital = $prestamo['detalleCalculo']['parametros']['capital']; // Capital
            $iva = $prestamo['detalleCalculo']['iva']; // IVA
            $microseguro = $prestamo['detalleCalculo']['microseguro'];
            $interes = $prestamo['detalleCalculo']['calculosIntermedios']['interes'];
            $garantia_id = $prestamo['detalleCalculo']['garantia_id'];
            $linea = $prestamo['detalleCalculo']['linea'];
            $id_colector = $prestamo['detalleCalculo']['id_colector'];
            $grupo_id = $prestamo['detalleCalculo']['grupoId'];
            $centro_id = $prestamo['detalleCalculo']['centroId'];
            $asesor = $prestamo['asesor'];
            $nombre_asesor = $prestamo['nombre_asesor'];
            $nombre_supervisor = $prestamo['nombre_supervisor'];
            $nombre_sucursal = $prestamo['nombre_sucursal'];

            $grupo = DB::table('grupos')->where('id', $grupo_id)->first(); // Asegúrate de que 'grupos' sea la tabla correcta
            $centro = DB::table('centros')->where('id', $centro_id)->first(); // Asegúrate de que 'centros' sea la tabla correcta

            $clave = $centro_id . '_' . $grupo_id;
            $gruposCentros[$clave] = [
                'centro_id' => $centro_id,
                'grupo_id' => $grupo_id,
            ];

            // Si no se encuentra el grupo o el centro, asignar un valor por defecto
            $nombreGrupo = $grupo ? $grupo->nombre : 'Grupo desconocido';
            $nombreCentro = $centro ? $centro->nombre : 'Centro desconocido';

            $sucursal = $prestamo['detalleCalculo']['sucursal'];
            $id_supervisor = $prestamo['detalleCalculo']['supervisor'];
            $id_aprobado = $prestamo['detalleCalculo']['id_aprobador'];
            $id_formapago = $prestamo['detalleCalculo']['formapago'];

            // Convertir fecha de apertura y fecha de vencimiento a objetos Carbon para facilitar las operaciones
            $fechaActual = \Carbon\Carbon::parse($fechaApertura);
            $fechaParacadaMiembro = \Carbon\Carbon::parse($fechaPagoCadaMibro);
            $fechaPrimerPago = clone $fechaParacadaMiembro;
            $fechaFinal = \Carbon\Carbon::parse($fechaVencimiento);

            // Insertar la primera fila con cuota = 0 y iva = 0
            $datosAInsertar[] = [
                'id_cliente' => $idCliente, // ID del cliente
                'fecha' => $fechaActual->toDateString(), // Fecha calculada (primera fecha)
                'cuota' => 0, // Cuota 0
                'saldo' => $monto, // Monto completo
                'tasa_interes' => $tasa, // Tasa de interés
                'dias' => $diasPorPago, // Días por pago
                'plazo' => $plazo, // Plazo de pagos
                'manejo' => $manejo, // Manejo
                'seguro' => $seguro, // Seguro
                'capital' => 0, // Capital inicial (puede ser 0 aquí)
                'iva' => 0, // IVA 0
                'intereses' => $interes,
                // 'microseguro' => $microseguro
            ];


            $datosParaPDF[] = [
                'id_cliente' => $prestamo['id'],
                'nombre_cliente' => $nombreCliente, // Ahora le pasas el nombre
                'nombre_centro' => $nombreCentro,
                'nombre_grupo' => $nombreGrupo,
                'monto' => $prestamo['monto'],
                'saldo' => $prestamo['monto'],
                'cuota' => $prestamo['detalleCalculo']['cuotaFinal'],
                'fechaapertura' => $prestamo['detalleCalculo']['fechaapertura'],
                'fechavencimiento' => $prestamo['detalleCalculo']['fechavencimiento'],
                'garantia' => $prestamo['detalleCalculo']['garantia_id'],
                'plazo' => $prestamo['detalleCalculo']['parametros']['cantPagos'],
                'interes' => $prestamo['tasa'],
                'fecha_primer_pago' => $prestamo['detalleCalculo']['fechaDebeSer'],
                'colector' => $prestamo['detalleCalculo']['id_colector'],
                'manejo' => $prestamo['detalleCalculo']['manejo'],
                'groupsolid' => $prestamo['detalleCalculo']['grupoId'],
                'centro' => $prestamo['detalleCalculo']['centroId'],
                'sucursal' => $prestamo['detalleCalculo']['sucursal'],
                'supervisor' => $prestamo['detalleCalculo']['supervisor'],
                'segu_d' => $prestamo['detalleCalculo']['manejo'],
                'id_aprobadopor' => $prestamo['detalleCalculo']['id_aprobador'],
                'tip_pago' => $prestamo['detalleCalculo']['formapago'],
            ];

            // Inicializar el monto restante (el monto inicial en la primera fila)
            $montoRestante = $monto;

            // Iterar para crear registros repetidos según el intervalo de diasPorPago
            $montoRestante = $montoRestante;
            $tasa_diaria = ($tasa / 360) / 100;
            $intereses = $montoRestante * $tasa_diaria * $diasPorPago;
            $tasa_iva = 0.13;


            // Iterar para crear registros repetidos según el intervalo de diasPorPago
            $segundaFila = true;

            $nuevaFechaVencimiento = null;
            $fechasVencimientoClientes = [];


            for ($i = 0; $i < $plazo; $i++) {
                if ($i > 0) {
                    // Avanza la fecha de pago según intervalo
                    $fechaParacadaMiembro->add(new DateInterval("P{$diasPorPago}D"));
                }

                if ($segundaFila) {
                    // Primera cuota especial
                    $interesesCalculado = $interes;
                    $iva = $interesesCalculado * $tasa_iva;
                    $capital = $cuotaFinal - $interesesCalculado - $manejo - $microseguro - $iva;
                    $segundaFila = false;
                } else {
                    // Cuotas siguientes
                    $interesesCalculado = $montoRestante * $tasa_diaria * $diasPorPago;
                    $iva = $interesesCalculado * $tasa_iva;
                    $capital = $cuotaFinal - $interesesCalculado - $manejo - $microseguro - $iva;
                }

                if ($i == $plazo - 1) {
                    // Ajuste última cuota para que saldo llegue a 0
                    $capital = $montoRestante;
                    $cuota = $capital + $interesesCalculado + $iva + $manejo + $microseguro;
                    $montoRestante -= $capital;
                    $nuevaFechaVencimiento = clone $fechaParacadaMiembro;

                    // Guardar fecha de vencimiento para este cliente
                    $fechasVencimientoClientes[$idCliente] = $nuevaFechaVencimiento->format('Y-m-d');
                } else {
                    $cuota = $cuotaFinal;
                    $montoRestante -= $capital;
                }


                $datosAInsertar[] = [
                    'id_cliente'     => $idCliente,
                    'fecha'          => $fechaParacadaMiembro->format('Y-m-d'),
                    'cuota'          => round($cuota ?? $cuotaFinal, 2),
                    'saldo'          => round($montoRestante, 2),
                    'tasa_interes'   => $tasa,
                    'dias'           => $diasPorPago,
                    'plazo'          => $plazo,
                    'manejo'         => round($manejo, 2),
                    'seguro'         => round($microseguro, 2),
                    'capital'        => round($capital, 2),
                    'iva'            => round($iva ?? 0, 2),
                    'intereses'      => round($interesesCalculado, 2),
                    'fecha_apertura' => $fechaApertura,
                    'fecha_vencimiento' => $nuevaFechaVencimiento ? $nuevaFechaVencimiento->format('Y-m-d') : null,
                ];
                // Asignar la fecha de vencimiento correcta a cada fila

            }
            foreach ($datosAInsertar as &$fila) {
                $clienteID = $fila['id_cliente'];
                if (isset($fechasVencimientoClientes[$clienteID])) {
                    $fila['fecha_vencimiento'] = $fechasVencimientoClientes[$clienteID];
                }
            }
            unset($fila);
            $datosSaldoprestamo[] = [
                'id_cliente' => $idCliente,
                'monto' => $monto,
                'saldo' => $monto,
                'cuota' => $cuotaFinal,
                'fechaapertura' => $fechaApertura,
                'fechavencimiento' => $nuevaFechaVencimiento ? $nuevaFechaVencimiento->format('Y-m-d') : null,
                'ultima_fecha_pagada' => null,
                'garantia' => $garantia_id,
                'plazo' => $plazo,
                'interes' => $tasa,
                'fecha_primer_pago' => $fechaPrimerPago->format('Y-m-d'),
                'colector' => $id_colector,
                'manejo' => $manejo,
                'groupsolid' => $grupo_id,
                'centro' => $centro_id,
                'sucursal' => $sucursal,
                'supervisor' => $id_supervisor,
                'segu_d' => $microseguro,
                'id_aprobadopor' => $id_aprobado,
                'tip_pago' => $id_formapago,
                'asesor' => $asesor,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'nombre_cliente' => $nombreCliente,
                'nombre_centro' => $nombreCentro,
                'nombre_grupo' => $nombreGrupo,
                'nombre_sucursal' => $nombre_sucursal,
                'nombre_asesor' => $nombre_asesor,
                'nombre_supervisor' => $nombre_supervisor,
            ];
            $datosHistorialPrestamos[] = [
                'id_cliente' => $idCliente,
                'monto' => $monto,
                'cuota' => $cuotaFinal,
                'plazo' => $plazo,
                'interes' => $tasa,
                'manejo' => $manejo,
                'fecha_apertura' => $fechaApertura,
                'fecha_vencimiento' => $nuevaFechaVencimiento ? $nuevaFechaVencimiento->format('Y-m-d') : null,
                'asesor' => $asesor,
                'centro' => $centro_id,
                'grupo' => $grupo_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'linea' => $linea,

            ];
        }


        // Intentar insertar todos los registros de una sola vez
        try {
            DB::transaction(function () use (
                $datosAInsertar,
                $datosSaldoprestamo,
                $datosHistorialPrestamos,
                $datosParaPDF
            ) {
                // Filtrar datos con cuota distinta de 0
                $datosAInsertarFiltrados = array_filter($datosAInsertar, function ($fila) {
                    return $fila['cuota'] != 0;
                });

                DB::table('debeser')->insert($datosAInsertarFiltrados);
                $idsClientesActualizar = [];
                foreach ($datosSaldoprestamo as $saldo) {

                    $camposPermitidos = array_diff_key($saldo, array_flip([
                        'nombre_cliente',
                        'nombre_centro',
                        'nombre_grupo',
                        'nombre_asesor',
                        'nombre_supervisor',
                        'nombre_sucursal'
                    ]));

                    DB::table('saldoprestamo')->insert($camposPermitidos);

                    $idsClientesActualizar[] = $saldo['id_cliente'];

                    $textoBitacora = "";
                    $textoBitacora .= "CLIENTE: " . ($saldo['nombre_cliente']) . "\n";
                    $textoBitacora .= "MONTO: " . (isset($saldo['monto']) ? round($saldo['monto'], 2) : 'N/A') . "\n";
                    $textoBitacora .= "CUOTA: " . (isset($saldo['cuota']) ? round($saldo['cuota'], 2) : 'N/A') . "\n";
                    $textoBitacora .= "PLAZO: " . ($saldo['plazo'] ?? 'N/A') . "\n";
                    $textoBitacora .= "FECHA APERTURA: " . (isset($saldo['fechaapertura']) ? Carbon::parse($saldo['fechaapertura'])->format('d-m-Y') : 'N/A') . "\n";
                    $textoBitacora .= "FECHA VENCIMIENTO: " . (isset($saldo['fechavencimiento']) ? Carbon::parse($saldo['fechavencimiento'])->format('d-m-Y') : 'N/A') . "\n";
                    $textoBitacora .= "ASESOR: " . ($saldo['nombre_asesor'] ?? 'N/A') . "\n";
                    $textoBitacora .= "CENTRO: " . ($saldo['nombre_centro'] ?? 'N/A') . "\n";
                    $textoBitacora .= "GRUPO: " . ($saldo['nombre_grupo'] ?? 'N/A') . "\n";
                    $textoBitacora .= "SUCURSAL: " . ($saldo['nombre_sucursal'] ?? 'N/A') . "\n";
                    $textoBitacora .= "SUPERVISOR: " . ($saldo['nombre_supervisor'] ?? 'N/A') . "\n";
                    $textoBitacora .= "-------------------------\n";


                    Bitacora::create([
                        'usuario' => Auth::user()->name,
                        'tabla_afectada' => 'PRESTAMOS',
                        'accion' => 'INSERTAR',
                        'datos' => $textoBitacora,
                        'fecha' => Carbon::now(),
                        'id_asesor' => Auth::user()->id,
                    ]);
                }

                if (!empty($datosHistorialPrestamos)) {
                    DB::table('historial_prestamos')->insert($datosHistorialPrestamos);
                }
                $idsClientesActualizar = array_unique($idsClientesActualizar);

                // Actualizar los clientes según condición
                DB::table('clientes')
                    ->whereIn('id', $idsClientesActualizar)
                    ->where(function ($query) {
                        $query->whereNull('conteo_rotacion')
                            ->orWhere('conteo_rotacion', 0);
                    })
                    ->update(['conteo_rotacion' => 1]);

                // Aquí también podrías guardar info del PDF si es necesario dentro de la transacción
            });
            foreach ($gruposCentros as $item) {
                $conteo = DB::table('grupos')
                    ->where('id', $item['grupo_id'])
                    ->where('id_centros', $item['centro_id'])
                    ->value('conteo_rotacion');

                if (is_null($conteo) || $conteo == 0) {
                    DB::table('grupos')
                        ->where('id', $item['grupo_id'])
                        ->where('id_centros', $item['centro_id'])
                        ->update(['conteo_rotacion' => 1]);
                }
            }


            // Si todo fue exitoso, se genera el PDF
            $pdf = PDF::loadView('PDF.desembolsoPrestamoGrupal', ['prestamos' => $datosParaPDF])
                ->setPaper('a4', 'portrait')
                ->setOptions(['defaultFont' => 'sans-serif']);

            $pdfContent = $pdf->output();
            $pdfBase64 = base64_encode($pdfContent);

            return response()->json([
                'status' => 'success',
                'message' => 'Datos insertados correctamente',
                'pdf' => $pdfBase64
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hubo un problema al insertar los datos',
                'error' => $e->getMessage(), // útil para depuración, eliminar en producción si quieres
            ], 500);
        }
    }


    public function obtenerSaldoPrestamo($codigo)
    {
        $prestamo = saldoprestamo::where('id_cliente', $codigo)
            ->orderByDesc('id')
            ->first();

        if ($prestamo) {
            $respuesta = [
                'monto' => $prestamo->MONTO,
                'fecha_apertura' => $prestamo->FECHAAPERTURA,
                'fecha_vencimiento' => $prestamo->FECHAVENCIMIENTO,
            ];

            // Esta línea no se ejecuta si dd() está presente
            return response()->json($respuesta);
        }

        return response()->json(['monto' => null], 404);
    }

    public function validarPassword(Request $request)
    {
        // Validación del input
        $request->validate([
            'password' => 'required|string',
        ]);

        // Obtener la contraseña especial de la base de datos
        $password = $request->input('password');
        $SpecialPassword = SpecialPassword::first(); // Tomamos el primer registro de la tabla

        // Verificamos si la contraseña especial existe y si la ingresada es válida
        if ($SpecialPassword && Hash::check($password, $SpecialPassword->password)) {
            // Si la contraseña es correcta, respondemos con éxito
            return response()->json(['valida' => true]);
        }

        // Si la contraseña no es válida, devolvemos un error con el mensaje correspondiente
        return response()->json(['valida' => false, 'mensaje' => 'Contraseña incorrecta']); // <- sin 401
    }



    public function eliminarDesembolso(Request $request)
    {

        DB::beginTransaction();

        try {
            $fechaApertura = $request->input('fecha_apertura');
            $fechaVencimiento = $request->input('fecha_vencimiento');
            $codigoCliente = $request->input('codigoCliente');
            $motivo = $request->input('motivo');

            // Obtener datos del cliente para bitácora
            $clienteDatos = DB::table('clientes')
                ->select('nombre', 'apellido') // Cambia según columnas reales
                ->where('id', $codigoCliente)
                ->first();

            if (!$clienteDatos) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No se encontró el cliente'
                ], 404);
            }

            // Formar nombre completo
            $nombreCompleto = trim($clienteDatos->nombre . ' ' . $clienteDatos->apellido);

            // Buscar el último desembolso
            $cliente = Saldoprestamo::where('id_cliente', $codigoCliente)
                ->where('FECHAAPERTURA', $fechaApertura)
                ->where('FECHAVENCIMIENTO', $fechaVencimiento)
                ->latest('id')
                ->first();

            if (!$cliente) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No se encontró el desembolso para este cliente'
                ], 404);
            }

            // Aquí ya tienes el nombre completo y el registro del desembolso, puedes armar tu texto para bitácora
            $textoBitacora = "";
            $textoBitacora .= "Nombre: {$nombreCompleto}\n";
            $textoBitacora .= "Monto: " . number_format($cliente->MONTO, 2) . "\n";  // <-- aquí el monto con 2 decimales
            $textoBitacora .= "Fecha apertura: " . date('d-m-Y', strtotime($fechaApertura)) . "\n";
            $textoBitacora .= "Fecha vencimiento: " . date('d-m-Y', strtotime($fechaVencimiento)) . "\n";
            $textoBitacora .= "-------------------------\n";

            Bitacora::create([
                'usuario' => Auth::user()->name,
                'tabla_afectada' => 'Historial Prestamos',
                'accion' => 'REVERSIÓN PRESTAMO',
                'datos' => $textoBitacora,
                'fecha' => Carbon::now(),
                'id_asesor' => Auth::user()->id,
                'comentarios' => $motivo
                // 'comentarios' => $motivo
            ]);
            // Aquí agregarías el código para insertar en bitácora con $textoBitacora

            // Eliminar el registro de Saldoprestamo
            $cliente->delete();

            // Eliminar el último registro de la tabla debeser
            DB::table('debeser')
                ->where('id_cliente', $codigoCliente)
                ->where('fecha_apertura', $fechaApertura)
                ->where('fecha_vencimiento', $fechaVencimiento)
                ->where('created_at', function ($query) use ($codigoCliente, $fechaApertura, $fechaVencimiento) {
                    $query->select(DB::raw('MAX(created_at)'))
                        ->from('debeser')
                        ->where('id_cliente', $codigoCliente)
                        ->where('fecha_apertura', $fechaApertura)
                        ->where('fecha_vencimiento', $fechaVencimiento);
                })
                ->delete();

            // Eliminar el último registro del historial
            $registro = HistorialPrestamos::where('id_cliente', $codigoCliente)
                ->where('fecha_apertura', $fechaApertura)
                ->where('fecha_vencimiento', $fechaVencimiento)
                ->latest('id')
                ->first();

            if ($registro) {
                $registro->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'mensaje' => 'El desembolso ha sido eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'mensaje' => 'Error al eliminar el desembolso: ' . $e->getMessage()
            ], 500);
        }
    }





    public function almacenarPrestamoIndividual(Request $request)
    {
    
        DB::beginTransaction();

        try {
            $id_cliente = $request->input('id_cliente');

            $datos = [
                'id_cliente' => $id_cliente,
                'MONTO' => $request->input('montoOtorgar'),
                'SALDO' => $request->input('montoOtorgar'),
                'CUOTA' => $request->input('cuota'),
                'GARANTIA' => $request->input('garantia'),
                'FECHAAPERTURA' => $request->input('fechaApertura'),
                'FECHAVENCIMIENTO' => $request->input('fechaVencimiento'),
                'PLAZO' => $request->input('plazo'),
                'INTERES' => $request->input('interes'),
                'COLECTOR' => $request->input('colector'),
                'MANEJO' => $request->input('manejo'),
                'groupsolid' => 1,
                'centro' => 1,
                'sucursal' => $request->input('sucursal'),
                'supervisor' => $request->input('supervisor'),
                'segu_d' => $request->input('micro_seguro'),
                'id_aprobadopor' => $request->input('aprobadoPor'),
                'tip_pago' => $request->input('tipoPago'),
                'formapago' => $request->input('formaPago'),
                'MESES' => $request->input('frecuenciaMeses'),
                'DIAS' => $request->input('frecuenciaDias'),
                'ID_BANCO' => $request->input('banco'),
                'ASESOR' => $request->input('id_asesor'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $datosHistorialPrestamos = [
                'id_cliente' => $id_cliente,
                'monto' => $request->input('montoOtorgar'),
                'cuota' => $request->input('cuota'),
                'plazo' => $request->input('plazo'),
                'interes' => $request->input('interes'),
                'manejo' => $request->input('manejo'),
                'fecha_apertura' => $request->input('fechaApertura'),
                'fecha_vencimiento' => $request->input('fechaVencimiento'),
                'asesor' => $request->input('id_asesor'),
                'centro' => 1, // Asignar el centro por defecto
                'grupo' => 1, // Asignar el grupo por defecto
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'linea' => $request->input('linea'),

            ];


            $monto = round(floatval($request->input('montoOtorgar')), 2);
            $cuota = round(floatval($request->input('cuota')), 2);
            $fechaApertura = $request->input('fechaApertura')
                ? Carbon::parse($request->input('fechaApertura'))->format('d-m-Y')
                : null;

            $fechaVencimiento = $request->input('fechaVencimiento')
                ? Carbon::parse($request->input('fechaVencimiento'))->format('d-m-Y')
                : null;

            // // Solo ciertos campos para bitácora
            $sucursalNombre = $request->input('nombre_sucursal') ?? 'No especificado';
            $supervisorNombre = $request->input('nombre_supervisor') ?? 'No especificado';
            $asesorNombre = $request->input('nombre_asesor') ?? 'No especificado';
            $datosBitacora = [
                'CLIENTE' => $request->input('nombre'),
                'MONTO' => $monto,
                'CUOTA' => $cuota,
                'PLAZO' => $request->input('plazo'),
                'FECHA APERTURA' => $fechaApertura,
                'FECHA VENCIMIENTO' => $fechaVencimiento,
                'INTERES' => $request->input('interes'),
                'MANEJO' => $request->input('manejo'),
                'GRUPO' => 'INDIVIDUAL',
                'CENTRO' => 'INDIVIDUAL',
                'SUCURSAL' => $sucursalNombre,
                'SUPERVISOR' => $supervisorNombre,
                'ASESOR' => $asesorNombre,

            ];


            $segundaFila = true;
            $fechaApertura = Carbon::parse($request->input('fechaApertura'));
            $fechaPago = Carbon::parse($request->input('fechaPrimerPago')); // NUEVO
            $fechaFinal = Carbon::parse($request->input('fechaVencimiento'));
            $diasPorPago = (int) $request->input('cantDiasSelect');
            $frecuenciasdias = (int) $request->input('frecuenciaDias');
            $frecuenciasmeses = (int) $request->input('frecuenciaMeses');
            $interes = $request->input('interes');
            $monto = $request->input('montoOtorgar');
            $tasa = $request->input('interes');
            $manejo = $request->input('manejo');
            $micro_seguro = $request->input('micro_seguro');
            $cuota = $request->input('cuota');
            $frecuenciaSeleccionada = strtolower($request->input('textoTipoPagoIndi'));
            $plazo = $request->input('plazo');
            $montoRestante = $monto;

            $montoRestante = $montoRestante;
            $tasa_diaria = ($tasa / 360) / 100;

            $frecuenciasLargas = ['mensual', 'bimensual', 'trimestral', 'semestral', 'anual'];
            $frecuenciasCortas = ['diario', 'semanal', 'catorcenal'];
            $tasa_interes_mensuales = ($tasa / 100) / 12;
            // $intereses = $monto * $tasa_interes_mensuales;
            $tasa_iva = 0.13;
            $contador = 1;


            $nuevosPagos = [];


            while ($fechaPago->lte($fechaFinal)) {

                if (in_array($frecuenciaSeleccionada, $frecuenciasLargas)) {
                    $interesesCalculado = $montoRestante * $tasa_interes_mensuales;
                    $iva = $interesesCalculado * $tasa_iva;
                    $capital = $cuota - $interesesCalculado - $manejo - $micro_seguro - $iva;
                    if ($montoRestante < $capital) {
                        $capital = $montoRestante;
                        $cuota = $capital + $interesesCalculado + $manejo + $micro_seguro + $iva;
                    }
                    $montoRestante -= $capital;
                } else if (in_array($frecuenciaSeleccionada, $frecuenciasCortas)) {
                    $interesesCalculado = $montoRestante * $tasa_diaria * $frecuenciasdias;
                    $iva = $interesesCalculado * $tasa_iva;
                    $capital = $cuota - $interesesCalculado - $manejo - $micro_seguro - $iva;

                    if ($montoRestante < $capital) {
                        $capital = $montoRestante;
                        $cuota = $capital + $interesesCalculado + $manejo + $micro_seguro + $iva;
                    }
                    $montoRestante -= $capital;
                } else {
                    $interesesCalculado = 0;
                }
                $nuevosPagos[] = [
                    'id_cliente' => $id_cliente,
                    'fecha' => $fechaPago->toDateString(),
                    'cuota' => $cuota,
                    'saldo' => $montoRestante,
                    'tasa_interes' => $tasa,
                    'dias' => $diasPorPago,
                    'manejo' => $manejo,
                    'seguro' => $micro_seguro,
                    'capital' => $capital,
                    'iva' => $iva,
                    'intereses' => $interesesCalculado,
                    'fecha_apertura' => $fechaApertura->toDateString(),
                    'fecha_vencimiento' => $fechaFinal->toDateString(),

                ];


                $montoRestante = max(0, $montoRestante); // prevenir negativos por redondeo

                // 👇 Sumar al final
                if (in_array($frecuenciaSeleccionada, $frecuenciasLargas)) {
                    $fechaPago->addMonths($frecuenciasmeses); // ⬅️ correctamente modifica la fecha
                } else if (in_array($frecuenciaSeleccionada, $frecuenciasCortas)) {
                    $fechaPago->addDays($frecuenciasdias); // ⬅️ aquí era el error
                }


                $contador++;
            }
            $datosPdf = [
                'id_cliente' => $id_cliente,
                'nombre' => $request->input('nombre'),
                'MONTO' => $request->input('montoOtorgar'),
                'SALDO' => $request->input('montoOtorgar'),
                'CUOTA' => $request->input('cuota'),
                'GARANTIA' => $request->input('garantia'),
                'FECHAAPERTURA' => $request->input('fechaApertura'),
                'FECHAVENCIMIENTO' => $request->input('fechaVencimiento'),
                'PLAZO' => $request->input('plazo'),
                'INTERES' => $request->input('interes'),
                'COLECTOR' => $request->input('colector'),
                'MANEJO' => $request->input('colector'),
                'groupsolid' => 1,
                'centro' => 1,
                'sucursal' => $request->input('sucursal'),
                'supervisor' => $request->input('supervisor'),
                'segu_d' => $request->input('micro_seguro'),
                'id_aprobadopor' => $request->input('aprobadoPor'),
                'tip_pago' => $request->input('tipoPago'),
                'asesor' => $request->input('id_asesor'),
                'formapago' => $request->input('formaPago'),
                'MESES' => $request->input('frecuenciaMeses'),
                'DIAS' => $request->input('frecuenciaDias'),
                'ID_BANCO' => $request->input('banco'),
            ];


            DB::table('saldoprestamo')->insert($datos);

            $fechaAperturaFormat = Carbon::parse($datosBitacora['FECHA APERTURA'])->format('d-m-Y');
            $fechaVencimientoFormat = Carbon::parse($datosBitacora['FECHA VENCIMIENTO'])->format('d-m-Y');

            $datosBitacoraTextoPlano =
                "CLIENTE: {$datosBitacora['CLIENTE']}\n" .
                "MONTO: {$datosBitacora['MONTO']}\n" .
                "CUOTA: {$datosBitacora['CUOTA']}\n" .
                "PLAZO: {$datosBitacora['PLAZO']}\n" .
                "FECHA APERTURA: {$fechaAperturaFormat}\n" .
                "FECHA VENCIMIENTO: {$fechaVencimientoFormat}\n" .
                "ASESOR: {$datosBitacora['ASESOR']}\n" .
                "CENTRO: {$datosBitacora['CENTRO']}\n" .
                "GRUPO: {$datosBitacora['GRUPO']}\n" .
                "SUCURSAL: {$datosBitacora['SUCURSAL']}\n" .
                "SUPERVISOR: {$datosBitacora['SUPERVISOR']}\n" .
                "-------------------------";

            // Registrar en bitácora
            Bitacora::create([
                'usuario' => Auth::user()->name,
                'tabla_afectada' => 'PRESTAMOS',
                'accion' => 'INSERTAR',
                'datos' => $datosBitacoraTextoPlano,
                'fecha' => Carbon::now(),
                'id_asesor' => Auth::user()->id,
            ]);
            //Insertar en tabla Historialprestamos
            if (!empty($datosHistorialPrestamos)) {
                DB::table('historial_prestamos')->insert($datosHistorialPrestamos);
            }
            // Crear o actualizar relación en Centros_Grupos_Clientes
            Centros_Grupos_Clientes::updateOrCreate(
                [
                    'cliente_id' => $id_cliente,
                    'centro_id' => 1,
                    'grupo_id' => 1,
                ],
                [
                    'cliente_id' => $id_cliente,
                    'centro_id' => 1,
                    'grupo_id' => 1,
                    // Puedes agregar más campos si son requeridos
                ]
            );

            // Actualizar los clientes según condición
            $clienteid = (array) $id_cliente; // Fuerza a array

            DB::table('clientes')
                ->whereIn('id', $clienteid)
                ->where(function ($query) {
                    $query->whereNull('conteo_rotacion')
                        ->orWhere('conteo_rotacion', 0);
                })
                ->update(['conteo_rotacion' => 1]);

            DB::table('debeser')->insert($nuevosPagos);
            DB::commit();

            // Generar el PDF
            $pdf = PDF::loadView('PDF.desembolsoPrestamoIndividual', ['prestamo' => $datosPdf])
                ->setPaper('a4', 'portrait')
                ->setOptions(['defaultFont' => 'sans-serif']);
            $pdfContent = $pdf->output();
            $pdfBase64 = base64_encode($pdfContent);

            return response()->json([
                'status' => 'success',
                'message' => 'Datos insertados correctamente',
                'pdf' => $pdfBase64
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Error al guardar el préstamo.', 'details' => $e->getMessage()], 500);
        }
    }
}
