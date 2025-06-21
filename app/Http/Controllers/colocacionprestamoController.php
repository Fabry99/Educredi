<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class colocacionprestamoController extends Controller
{
    public function obtenerinformacion()
    {
        $asesor = DB::table('asesores')
            ->get();

        $sucursal = DB::table('sucursales')
            ->get();
        $supervisor = DB::table('supervisores')
            ->get();
        $centro = DB::table('centros')
            ->get();
        return response()->json([
            $asesor,
            $sucursal,
            $supervisor,
            $centro
        ]);
    }
    public function obtenergrupo(Request $request)
    {
        $id_centro = $request->input('id');
        $grupos = DB::table('grupos')
            ->where('id_centros', $id_centro)
            ->get();

        return response()->json($grupos);
    }


    public function pdfcolocacion(Request $request)
    {
        // Inputs
        $sucursal = $request->input('sucursal');
        $supervisor = $request->input('supervisor');
        $asesor = $request->input('asesor');
        $centro = $request->input('centro');
        $grupo = $request->input('grupo');

        $fecha_desde = Carbon::parse($request->input('fecha_desde'))->startOfDay();
        $fecha_hasta = Carbon::parse($request->input('fecha_hasta'))->endOfDay();
        $saldo0 = $request->input('saldo0');

        $saldoprestamo = DB::table('saldoprestamo as sl')
            ->leftJoin('clientes as cl', 'sl.id_cliente', '=', 'cl.id')
            ->leftJoin('centros as cr', 'sl.centro', '=', 'cr.id')
            ->leftJoin('grupos as gr', 'sl.groupsolid', '=', 'gr.id')
            ->leftJoin('asesores as ss', 'sl.ASESOR', '=', 'ss.id')
            ->leftJoin('supervisores as sp', 'sl.supervisor', '=', 'sp.id')
            ->leftJoin('sucursales as sc', 'sl.SUCURSAL', '=', 'sc.id')
            ->whereBetween('sl.created_at', [$fecha_desde, $fecha_hasta])
            ->when(!empty($centro), fn($q) => $q->where('sl.centro', $centro))
            ->when(!empty($grupo), fn($q) => $q->where('sl.groupsolid', $grupo))
            ->when(!empty($asesor), fn($q) => $q->where('sl.ASESOR', $asesor))
            ->when(!empty($sucursal), fn($q) => $q->where('sl.SUCURSAL', $sucursal))
            ->when(!empty($supervisor), fn($q) => $q->where('sl.supervisor', $supervisor))
            ->select(
                'cl.nombre as nombre_cliente',
                'cl.apellido',
                'cr.nombre as nombre_centro',
                'gr.nombre as nombre_grupo',
                'ss.nombre as nombre_asesor',
                'sp.nombre as nombre_supervisor',
                'sc.nombre as nombre_sucursales',
                'sl.id',
                'sl.MONTO',
                'sl.SALDO',
                'sl.PLAZO',
                'sl.INTERES',
                'sl.centro',
                'sl.groupsolid',
                'sl.created_at'
            )
            ->get();


        $primerPrestamo = $saldoprestamo->first();

        // ✅ Agrupado por fecha y dentro de cada fecha por centro y grupo
        $agrupado = $saldoprestamo->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d');
        })->map(function ($items) {
            return $items->groupBy(function ($item) {
                return $item->centro . '_' . $item->groupsolid;
            });
        });


        // Totales por asesor y grupo
        $totalesPorAsesorGrupo = $saldoprestamo->groupBy(fn($item) => $item->nombre_asesor . '_' . $item->groupsolid)
            ->map(fn($items) => $items->sum('MONTO'))->toArray();

        // Totales y conteo por asesor del mes actual
        $inicioMes = Carbon::now()->startOfMonth()->toDateString();
        $finMes = Carbon::now()->endOfMonth()->toDateString();

        $totalesPorAsesor = [];
        $conteoPorAsesor = [];

        if (!empty($asesor)) {
            $totalDelMes = DB::table('saldoprestamo')
                ->whereBetween('created_at', [$inicioMes, $finMes])
                ->where('ASESOR', $asesor)
                ->select(DB::raw('SUM(MONTO) as total'), DB::raw('COUNT(*) as cantidad'))
                ->first();

            $asesorNombre = DB::table('asesores')->where('id', $asesor)->value('nombre');

            $totalesPorAsesor[$asesorNombre] = $totalDelMes->total ?? 0;
            $conteoPorAsesor[$asesorNombre] = $totalDelMes->cantidad ?? 0;
        } else {
            $totalDelMes = DB::table('saldoprestamo as sl')
                ->join('asesores as a', 'sl.ASESOR', '=', 'a.id')
                ->whereBetween('sl.created_at', [$inicioMes, $finMes])
                ->select(
                    'a.nombre as nombre_asesor',
                    DB::raw('SUM(sl.MONTO) as total'),
                    DB::raw('COUNT(*) as cantidad')
                )
                ->groupBy('sl.ASESOR', 'a.nombre')
                ->get();

            foreach ($totalDelMes as $item) {
                $totalesPorAsesor[$item->nombre_asesor] = floatval($item->total);
                $conteoPorAsesor[$item->nombre_asesor] = intval($item->cantidad);
            }
        }

        // PDF
        $pdf = Pdf::loadView('PDF.colocacionprestamos', [
            'prestamos' => $agrupado,
            'conteoPorAsesor' => $conteoPorAsesor,
            'totalesPorAsesorGrupo' => $totalesPorAsesorGrupo,
            'totalesPorAsesor' => $totalesPorAsesor,
            'sucursal' => $sucursal,
            'supervisor' => $supervisor,
            'asesor' => $asesor,
            'centro' => $centro,
            'grupo' => $grupo,
            'fecha_desde' => $fecha_desde,
            'fecha_hasta' => $fecha_hasta,
            'primerPrestamo' => $primerPrestamo,
            'saldo0' => $saldo0 === 'true' || $saldo0 === true,
        ])
            ->setPaper('a4', 'portrait')
            ->setOptions(['defaultFont' => 'sans-serif']);

        return response()->json([
            'status' => 'success',
            'message' => 'PDF generado correctamente',
            'pdf' => base64_encode($pdf->output())
        ]);
    }
}
