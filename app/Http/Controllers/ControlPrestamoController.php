<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\HistorialPrestamos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ControlPrestamoController extends Controller
{
    public function controlprestamos()
    {
        $rol = Auth::user()->rol;
        if ($rol !== 'caja' && $rol !== 'administrador') {
            return redirect()->back()->with('error', 'No tienes acceso a esta sección.');
        }
        return view('modules.dashboard.controlprestamo', compact('rol'));
    }

    public function buscarClientes(Request $request)
    {
        $texto = $request->input('q');

        $clientes = Clientes::whereRaw("LOWER(CONCAT(nombre, ' ', apellido)) LIKE ?", ["%{$texto}%"])
            ->limit(10)
            ->get();

        return response()->json($clientes);
    }

    public function datosCliente(Request $request)
    {

        $id = $request->input('id');
        $cliente = DB::table('historial_prestamos')
            ->where('id_cliente', $id)
            ->get();

        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        return response()->json($cliente);
    }
    public function consultaAvanzada(Request $request)
    {

        $idCliente = $request->input('id_cliente');
        $fechaInicio = $request->input('inicio');
        $fechaFin = $request->input('fin');

        // Verificamos que todos los datos estén presentes
        if (!$idCliente || !$fechaInicio || !$fechaFin) {
            return response()->json(['error' => 'Datos incompletos'], 400);
        }
        $saldoprestamo = DB::table('saldoprestamo as sal')
            ->join('centros as ce', 'sal.centro', '=', 'ce.id')
            ->join('grupos as gr', 'sal.groupsolid', '=', 'gr.id')
            ->join('supervisores as sp', 'sal.supervisor', '=', 'sp.id')
            ->join('sucursales as sc', 'sal.SUCURSAL', '=', 'sc.id')
            ->join('garantias as gt', 'sal.GARANTIA', '=', 'gt.id')
            ->select(
                'sal.ULTIMA_FECHA_PAGADA',
                'sal.PLAZO',
                'sal.INTERES',
                'sal.SALDO',
                'sal.MANEJO',
                'sal.segu_d',
                'sal.CUOTA',
                'gr.nombre as nombre_grupo',
                'ce.nombre as nombre_centro',
                'sp.nombre as nombre_supervisor',
                'sc.nombre as nombre_sucursal',
                'gt.nombre as nombre_garantia'
            )
            ->where('sal.id_cliente', $idCliente)
            ->where('sal.FECHAAPERTURA', $fechaInicio)
            ->where('sal.FECHAVENCIMIENTO', $fechaFin)
            ->get();


        // Consulta a la base de datos (ejemplo con tabla "pagos")
        $resultados = DB::table('movimientos_presta')
            ->where('id_cliente', $idCliente)
            ->where('fecha_apertura', $fechaInicio)
            ->where('fecha_vencimiento', $fechaFin)
            ->get();

        
        return response()->json([
            'saldoprestamo' => $saldoprestamo,
            'movimientos' => $resultados
        ]);
    }
}
