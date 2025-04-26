<?php

namespace App\Http\Controllers;

use App\Models\Aprobacion;
use App\Models\Centros_Grupos_Clientes;
use App\Models\Clientes;
use App\Models\Colector;
use App\Models\Linea;
use App\Models\Sucursales;
use App\Models\Supervisores;
use App\Models\Tipopago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        // Registrar en el log la información recibida para depuración
        Log::info('Datos recibidos:', $request->all());

        // Array para almacenar todos los datos a insertar
        $datosAInsertar = [];

        
        // Preparar los datos a insertar
        foreach ($request->prestamos as $prestamo) {
            // Obtener los datos necesarios del préstamo
            $fechaApertura = $prestamo['detalleCalculo']['fechaapertura']; // Fecha de apertura
            $fechaVencimiento = $prestamo['detalleCalculo']['fechavencimiento']; // Fecha de vencimiento
            $fechaDebeSer = $prestamo['detalleCalculo']['fechaDebeSer'];
            $diasPorPago = $prestamo['detalleCalculo']['parametros']['diasporpago']; // Días por pago
            $idCliente = $prestamo['id']; // ID del cliente
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

                
                // 'fecha_apertura' => $fechaActual->toDateString(), // <- reemplaza si es necesario
                // 'monto_prestamo' => $monto,                  // <- reemplaza con el nombre real
                // 'tasa' => $tasa,                             // <- reemplaza con el nombre real
                // 'plazo_dias' => $plazo,                      // <- reemplaza con el nombre real
                // 'dias_por_pago' => $diasPorPago,             // <- reemplaza con el nombre real
                // 'gasto_manejo' => $manejo,                   // <- reemplaza con el nombre real
                // 'seguro_inicial' => $seguro,                 // <- reemplaza con el nombre real
                // 'capital_inicial' => 0,                      // <- ajusta si tu tabla lo necesita
                // 'iva_inicial' => 0,                          // <- ajusta si tu tabla lo necesita
                // 'interes_total' => $interes,                 // <- ajusta si tu tabla lo necesita
            ];
            // Log para depurar los datos antes de la inserción
            Log::info('Datos a insertar:', $datosAInsertar);

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
        } catch (\Exception $e) {
            // Si ocurre un error, registrar el error y devolver un mensaje
            Log::error('Error al insertar en la tabla debeSer:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Hubo un problema al insertar los datos',
            ], 500);
        }

        // Responder con éxito
        return response()->json([
            'status' => 'success',
            'message' => 'Datos insertados correctamente en la tabla debeSer',
        ]);
    }
}
