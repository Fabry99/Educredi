<?php

namespace App\Http\Controllers;

use App\Exports\ReporteINFOREDExport;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportesController extends Controller
{

    public function ReporteINFORED(Request $request)
    {
        // Validación de los datos de entrada
        $validated = $request->validate([
            'nombre_archivo' => 'nullable|string|max:100',
            'fechadesde' => 'required|date',
            'fechaHasta' => 'required|date|after_or_equal:fechadesde',
            'Asesor' => 'nullable|integer'
        ]);

        try {

            $nombrearchivo = $validated['nombre_archivo'] ?? 'ReporteINFORED';
            $fechadesde = $validated['fechadesde'];
            $fechahasta = $validated['fechaHasta'];
            $id_asesor = $validated['Asesor'] ?? null;

            $fechaActual = Carbon::now();
            $anio = $fechaActual->year;
            $mes = $fechaActual->month;
            $dia = $fechaActual->day;

            // Consulta principal
            $datos = DB::table('historial_prestamos as hp')
                ->join('clientes as cl', 'hp.id_cliente', '=', 'cl.id')
                ->join('centros as cr', 'hp.centro', '=', 'cr.id')
                ->join('grupos as gr', 'hp.grupo', '=', 'gr.id')
                ->join('asesores as ss', 'hp.asesor', '=', 'ss.id')
                ->join('linea as ln', 'hp.linea', '=', 'ln.id')
                ->join('saldoprestamo as sl', function ($join) {
                    $join->on('sl.id_cliente', '=', 'cl.id')
                        ->whereColumn('sl.FECHAAPERTURA', 'hp.fecha_apertura')
                        ->whereColumn('sl.FECHAVENCIMIENTO', 'hp.fecha_vencimiento');
                })
                ->join('garantias as gn', 'sl.GARANTIA', '=', 'gn.id')
                ->leftJoin('movimientos_presta as mp', function ($join) {
                    $join->on('mp.id_cliente', '=', 'cl.id')
                        ->whereColumn('mp.fecha_apertura', 'hp.fecha_apertura')
                        ->whereColumn('mp.fecha_vencimiento', 'hp.fecha_vencimiento');
                })
                ->leftJoin('debeser as bd', function ($join) {
                    $join->on('bd.id_cliente', '=', 'cl.id')
                        ->whereColumn('bd.fecha_apertura', 'hp.fecha_apertura')
                        ->whereColumn('bd.fecha_vencimiento', 'hp.fecha_vencimiento');
                })
                ->whereDate('hp.created_at', '>=', $fechadesde)  // Cambiado a whereDate
                ->whereDate('hp.created_at', '<=', $fechahasta)
                ->when(!empty($id_asesor), function ($query) use ($id_asesor) {
                    return $query->where('hp.asesor', $id_asesor);
                })
                ->select(
                    'cl.nombre as nombre_cliente',
                    'cl.apellido as apellido_cliente',
                    'cl.fecha_nacimiento',
                    'cl.dui',
                    'cl.nit',
                    'cl.genero',
                    'cl.conteo_rotacion',

                    'cr.nombre as nombre_centro',
                    'gr.nombre as nombre_grupo',
                    'ss.nombre as nombre_asesor',

                    'hp.id as idprestamo',
                    'hp.monto',
                    'hp.cuota',
                    'hp.plazo',
                    'hp.interes',
                    'hp.fecha_apertura',
                    'hp.fecha_vencimiento',
                    'hp.asesor',
                    'hp.centro',
                    'hp.grupo',
                    'hp.linea',

                    'ln.nombre as nombre_linea',

                    'sl.SALDO',
                    'sl.ULTIMA_FECHA_PAGADA',
                    'sl.PLAZO as plazo_saldo',
                    'sl.TIP_PAGO',
                    'sl.DIAS',

                    'gn.infored',

                    DB::raw('COUNT(DISTINCT mp.id) as total_movimientos'),
                    DB::raw('COUNT(DISTINCT bd.id) as total_debeser')
                )
                ->groupBy(
                    'cl.nombre',
                    'cl.apellido',
                    'cl.fecha_nacimiento',
                    'cl.dui',
                    'cl.nit',
                    'cl.genero',
                    'cl.conteo_rotacion',
                    'cr.nombre',
                    'gr.nombre',
                    'ss.nombre',
                    'hp.id',
                    'hp.monto',
                    'hp.cuota',
                    'hp.plazo',
                    'hp.interes',
                    'hp.fecha_apertura',
                    'hp.fecha_vencimiento',
                    'hp.asesor',
                    'hp.centro',
                    'hp.grupo',
                    'hp.linea',
                    'ln.nombre',
                    'sl.SALDO',
                    'sl.ULTIMA_FECHA_PAGADA',
                    'sl.PLAZO',
                    'sl.TIP_PAGO',
                    'sl.DIAS',
                    'gn.infored'
                )
                ->get();


            // Transformación de datos
            $datosTransformados = $datos->map(function ($item) use ($anio, $mes, $dia) {
                $fechaCan = null;

                if ((int) $item->total_movimientos >= (int) $item->total_debeser) {
                    $fechaCan = Carbon::parse($item->ULTIMA_FECHA_PAGADA)->format('d/m/Y');
                }

                return [
                    'anio' => $anio,
                    'mes' => $mes,
                    'nombre' => $item->nombre_cliente . ' ' . $item->apellido_cliente,
                    'tipo_per' => '1',
                    'num_ptmo' => $item->idprestamo,
                    'inst' => '',
                    'fec_otor' => Carbon::parse($item->fecha_apertura)->format('d/m/Y'),
                    'monto' => '$' . number_format($item->monto, 2),
                    'plazo' => $item->plazo_saldo,
                    'saldo' => '$' . number_format($item->SALDO, 2),
                    'mora' => '$' . '0.00',
                    'forma_pago' => $item->TIP_PAGO,
                    'tipo_rel' => '1',
                    'linea_cre' => $item->nombre_linea,
                    'dias' => $item->DIAS,
                    'ult_pago' => Carbon::parse($item->ULTIMA_FECHA_PAGADA)->format('d/m/Y'),
                    'tipo_gar' => $item->infored,
                    'tipo_mon' => '2',
                    'valcuota' => '$' . number_format($item->cuota, 2),
                    'dia' => $dia,
                    'fechanac' => Carbon::parse($item->fecha_nacimiento)->format('d/m/Y'),
                    'dui' => $item->dui,
                    'nit' => $item->nit,
                    'fecha_can' => $fechaCan,
                    'fecha_ven' => Carbon::parse($item->fecha_vencimiento)->format('d/m/Y'),
                    'ncuotascre' => '0',
                    'ncuotasmor' => '0',
                    'calif_act' => '',
                    'actividad_eco' => '',
                    'sexo' => $item->genero,
                    'estcredito' => $item->conteo_rotacion,
                    'grupos' => "{$item->nombre_centro}/{$item->nombre_grupo}/{$item->nombre_asesor}",
                ];
            });

            // Crear directorio temporal si no existe
            // Nombre del archivo con timestamp para evitar colisiones
            $nombrearchivo = $validated['nombre_archivo'] ?? 'ReporteINFORED';

            // Solo agregar la extensión .xlsx sin timestamp
            $nombreArchivoDescarga = $nombrearchivo . '.xlsx';
            return Excel::download(
                new ReporteINFOREDExport($datosTransformados),
                $nombreArchivoDescarga
            );
        } catch (\Exception $e) {

            // Si es una petición AJAX, responder con JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al generar el reporte: ' . $e->getMessage()
                ], 500);
            }

            // Para peticiones normales, redirigir con error
            return back()->with('error', 'Error al generar el reporte: ' . $e->getMessage());
        }
    }

    public function downloadTempFile($filename)
    {
        $path = storage_path('app/temp/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        return response()->download($path)->deleteFileAfterSend(true);
    }
}
