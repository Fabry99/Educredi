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
use App\Models\Linea;
use App\Models\saldoprestamo;
use App\Models\SpecialPassword;
use App\Models\Sucursales;
use App\Models\Supervisores;
use App\Models\Tipopago;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Carbon\Carbon;
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
        // Array para almacenar todos los datos a insertar
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

        // Preparar los datos a insertar
        foreach ($request->prestamos as $prestamo) {


            // Obtener los datos necesarios del préstamo
            $fechaApertura = $prestamo['detalleCalculo']['fechaapertura']; // Fecha de apertura
            $fechaVencimiento = $prestamo['detalleCalculo']['fechavencimiento']; // Fecha de vencimiento
            $fechaDebeSer = $prestamo['detalleCalculo']['fechaDebeSer'];
            $diasPorPago = $prestamo['detalleCalculo']['parametros']['diasporpago']; // Días por pago
            $idCliente = $prestamo['id']; // ID del cliente
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
            $id_colector = $prestamo['detalleCalculo']['id_colector'];
            $grupo_id = $prestamo['detalleCalculo']['grupoId'];
            $centro_id = $prestamo['detalleCalculo']['centroId'];
            $asesor = $prestamo['asesor'];

            $grupo = DB::table('grupos')->where('id', $grupo_id)->first(); // Asegúrate de que 'grupos' sea la tabla correcta
            $centro = DB::table('centros')->where('id', $centro_id)->first(); // Asegúrate de que 'centros' sea la tabla correcta


            // Si no se encuentra el grupo o el centro, asignar un valor por defecto
            $nombreGrupo = $grupo ? $grupo->nombre : 'Grupo desconocido';
            $nombreCentro = $centro ? $centro->nombre : 'Centro desconocido';

            $sucursal = $prestamo['detalleCalculo']['sucursal'];
            $id_supervisor = $prestamo['detalleCalculo']['supervisor'];
            $id_aprobado = $prestamo['detalleCalculo']['id_aprobador'];
            $id_formapago = $prestamo['detalleCalculo']['formapago'];

            // Convertir fecha de apertura y fecha de vencimiento a objetos Carbon para facilitar las operaciones
            $fechaActual = \Carbon\Carbon::parse($fechaApertura);
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

            $datosSaldoprestamo[] = [
                'id_cliente' => $idCliente,
                'monto' => $monto,
                'saldo' => $monto,
                'cuota' => $cuotaFinal,
                'fechaapertura' => $fechaApertura,
                'fechavencimiento' => $fechaVencimiento,
                'garantia' => $garantia_id,
                'plazo' => $plazo,
                'interes' => $tasa,
                'fecha_primer_pago' => $fechaDebeSer,
                'colector' => $id_colector,
                'manejo' => $manejo,
                'groupsolid' => $grupo_id,
                'centro' => $centro_id,
                'sucursal' => $sucursal,
                'supervisor' => $id_supervisor,
                'segu_d' => $manejo,
                'id_aprobadopor' => $id_aprobado,
                'tip_pago' => $id_formapago,
                'asesor' => $asesor,
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
            $primeraFila = true;
            $segundaFila = true;

            while ($fechaActual < $fechaFinal) {
                $fechaActual->addDays($diasPorPago);



                if ($segundaFila) {
                    $interesesCalculado = $interes;
                    // El IVA ya está en $iva, tomado del JSON
                    $segundaFila = false;
                } else {
                    $interesesCalculado = $montoRestante * $tasa_diaria * $diasPorPago;
                    $iva = $interesesCalculado * $tasa_iva;
                    $capital = $cuotaFinal - $interesesCalculado - $manejo - $microseguro - $iva;
                }

                $montoRestante -= $capital;

                // Aquí corregimos si queda negativo
                if ($montoRestante < 0) {
                    $montoRestante = 0;
                }

                $datosAInsertar[] = [
                    'id_cliente' => $idCliente,
                    'fecha' => $fechaActual->toDateString(),
                    'cuota' => $cuotaFinal,
                    'saldo' => $montoRestante,
                    'tasa_interes' => $tasa,
                    'dias' => $diasPorPago,
                    'plazo' => $plazo,
                    'manejo' => $manejo,
                    'seguro' => $microseguro,
                    'capital' => $capital,
                    'iva' => $iva,
                    'intereses' => $interesesCalculado,
                ];
            }
        }

        // Intentar insertar todos los registros de una sola vez
        try {
            $datosAInsertarFiltrados = array_filter($datosAInsertar, function ($fila) {
                return $fila['cuota'] != 0;
            });
            foreach ($datosAInsertar as $dato) {
                DB::table('debeser')
                    ->where('id_cliente', $dato['id_cliente'])
                    ->delete();
            }
            DB::table('debeser')->insert($datosAInsertarFiltrados);

            foreach ($datosSaldoprestamo as $saldo) {
                // Intentamos hacer update
                $affected = DB::table('saldoprestamo')
                    ->where('id_cliente', $saldo['id_cliente'])
                    ->update($saldo);

                $accion = '';

                if ($affected) {
                    $accion = 'update';
                } else {
                    // Si no se actualizó, se inserta
                    DB::table('saldoprestamo')->insert($saldo);
                    $accion = 'insert';
                }

                // Registrar en la bitácora
                Bitacora::create([
                    'usuario' => Auth::user()->name, // Asegúrate de tener esta variable
                    'tabla_afectada' => 'saldoprestamo',
                    'accion' => strtoupper($accion),
                    'datos' => json_encode($saldo),
                    'fecha' => Carbon::now(), // usa Carbon para fecha y hora actual
                    'id_asesor' => Auth::user()->id, // Asegúrate de tener esta variable
                ]);
            }

            // Generar el PDF
            $pdf = PDF::loadView('PDF.desembolsoPrestamoGrupal', ['prestamos' => $datosParaPDF])
                ->setPaper('a4', 'portrait')
                ->setOptions(['defaultFont' => 'sans-serif']);

            $pdfContent = $pdf->output();
            $pdfBase64 = base64_encode($pdfContent);

            return response()->json([
                'status' => 'success',
                'message' => 'Datos insertados correctamente en la tabla debeSer',
                'pdf' => $pdfBase64
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hubo un problema al insertar los datos',
            ], 500);
        }
    }


    public function obtenerSaldoPrestamo($codigo)
    {

        $prestamo = saldoprestamo::where('id_cliente', $codigo)
            ->orderByDesc('id') // o ->latest('id')
            ->first();

        if ($prestamo) {
            return response()->json([
                'monto' => $prestamo->MONTO,
            ]);
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



    public function eliminarDesembolso($codigoCliente)
    {
        DB::beginTransaction();

        try {
            // Buscar el último desembolso
            $cliente = Saldoprestamo::where('id_cliente', $codigoCliente)
                ->latest('id')
                ->first();

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No se encontró el desembolso para este cliente'
                ], 404);
            }

            // Eliminar el registro de saldoprestamo
            $cliente->delete();

            // Eliminar el último registro de debeser
            DB::table('debeser')
                ->where('id_cliente', $codigoCliente)
                ->where('created_at', function ($query) use ($codigoCliente) {
                    $query->select(DB::raw('MAX(created_at)'))
                        ->from('debeser')
                        ->where('id_cliente', $codigoCliente);
                })
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Eliminación exitosa'
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
                'MANEJO' => $request->input('colector'),
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
            ];
            // Usando Eloquent para crear o actualizar el préstamo
            SaldoPrestamo::updateOrCreate(
                ['id_cliente' => $id_cliente],
                $datos
            );
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

            $existePrestamoDebeser = DB::table('debeser')
                ->where('id_cliente', $id_cliente)
                ->exists();

            if ($existePrestamoDebeser) {
                DB::table('debeser')
                    ->where('id_cliente', $id_cliente)
                    ->delete();
            }

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
            // DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Hubo un error al guardar los datos.',
            ], 500);
        }
    }
}
