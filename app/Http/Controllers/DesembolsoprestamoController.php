<?php

namespace App\Http\Controllers;

use App\Models\Aprobacion;
use App\Models\Centros_Grupos_Clientes;
use App\Models\Clientes;
use App\Models\Colector;
use App\Models\debeser;
use App\Models\Linea;
use App\Models\saldoprestamo;
use App\Models\SpecialPassword;
use App\Models\Sucursales;
use App\Models\Supervisores;
use App\Models\Tipopago;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
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
        $clientes = Clientes::all();
        $sucursales = Sucursales::all();
        $linea = Linea::all();
        $supervisor = Supervisores::all();
        $colector = Colector::all();
        $aprobaciones = Aprobacion::all();
        $tipopago = Tipopago::all();

        if ($rol !== 'contador') {
            return redirect()->route('home')->with('error', 'No tienes acceso a esta sección.');
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
            'tipopago'
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

                $primeraFila = false;

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
            DB::table('debeser')->insert($datosAInsertar);
            DB::table('saldoprestamo')->insert($datosSaldoprestamo);

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
    
}
