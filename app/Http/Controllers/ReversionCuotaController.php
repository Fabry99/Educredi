<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\SpecialPassword;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ReversionCuotaController extends Controller
{
    public function reversionCuota()
    {
        $rol = Auth::user()->rol;
        if ($rol !== 'caja' && $rol !== 'administrador') {
            return redirect()->back()->with('error', 'No tienes acceso a esta sección.');
        }
        return view('modules.dashboard.reversioncuotacaja', compact('rol'));
    }

    public function consultarCuotas(Request $request)
    {
        $fecha = $request->input('fecha');
        $comprobante = $request->input('comprobante');

        $consultaHistorial = DB::table('movimientos_presta as mov')
            ->join('clientes as cli', 'mov.id_cliente', '=', 'cli.id')
            ->select(
                'mov.id',
                'mov.id_cliente',
                'mov.fecha',
                'mov.valor_cuota',
                'cli.nombre',
                'cli.apellido'
            )
            ->where('fecha', $fecha)
            ->where('comprobante', $comprobante)
            ->get();


        return response()->json($consultaHistorial);
    }
    public function eliminarcuota(Request $request)
    {
        $fecha = $request->input('fecha');
        $comprobante = $request->input('comprobante');
        $motivo = $request->input('motivo');

        try {
            DB::transaction(function () use ($fecha, $comprobante, $motivo) {

                $movimientos = DB::table('movimientos_presta')
                    ->where('comprobante', $comprobante)
                    ->where('fecha', $fecha)
                    ->get();

                if ($movimientos->isEmpty()) {
                    throw new \Exception('No se encontraron datos para eliminar');
                }

                $clientesProcesados = [];
                $datosParaBitacora = [];

                foreach ($movimientos as $mov) {

                    // Actualizar saldo
                    $saldoPrestamo = DB::table('saldoprestamo')
                        ->where('id_cliente', $mov->id_cliente)
                        ->where('FECHAAPERTURA', $mov->fecha_apertura)
                        ->where('FECHAVENCIMIENTO', $mov->fecha_vencimiento)
                        ->where('centro', $mov->id_centro)
                        ->where('groupsolid', $mov->id_grupo)
                        ->first();

                    if ($saldoPrestamo) {
                        $nuevoSaldo = $saldoPrestamo->SALDO + $mov->valor_cuota;

                        DB::table('saldoprestamo')
                            ->where('id', $saldoPrestamo->id)
                            ->update(['saldo' => $nuevoSaldo]);
                    }

                    // Obtener nombres de cliente, centro y grupo
                    $infoCompleta = DB::table('saldoprestamo as s')
                        ->join('clientes as c', 'c.id', '=', 's.id_cliente')
                        ->join('centros as ce', 'ce.id', '=', 's.centro')
                        ->join('grupos as g', 'g.id', '=', 's.groupsolid')
                        ->where('s.id_cliente', $mov->id_cliente)
                        ->where('s.FECHAAPERTURA', $mov->fecha_apertura)
                        ->where('s.FECHAVENCIMIENTO', $mov->fecha_vencimiento)
                        ->where('s.centro', $mov->id_centro)
                        ->where('s.groupsolid', $mov->id_grupo)
                        ->select(
                            'c.nombre as cliente_nombre',
                            'c.apellido as cliente_apellido',
                            'ce.nombre as centro_nombre',
                            'g.nombre as grupo_nombre'
                        )
                        ->first();

                    // Guardar datos para bitácora
                    $datosParaBitacora[] = [
                        'id_cliente' => $mov->id_cliente,
                        'nombre_cliente' => $infoCompleta->cliente_nombre ?? 'Desconocido',
                        'apellido_cliente' => $infoCompleta->cliente_apellido ?? 'Desconocido',
                        'comprobante' => $mov->comprobante,
                        'valor_cuota' => $mov->valor_cuota,
                        'id_centro' => $mov->id_centro,
                        'nombre_centro' => $infoCompleta->centro_nombre ?? 'Desconocido',
                        'id_grupo' => $mov->id_grupo,
                        'nombre_grupo' => $infoCompleta->grupo_nombre ?? 'Desconocido',
                        'fecha' => $mov->fecha,
                    ];

                    // Guardar combinación para actualizar fecha más adelante
                    $clientesProcesados[] = [
                        'id_cliente' => $mov->id_cliente,
                        'fecha_apertura' => $mov->fecha_apertura,
                        'fecha_vencimiento' => $mov->fecha_vencimiento,
                        'id_centro' => $mov->id_centro,
                        'id_grupo' => $mov->id_grupo,
                    ];
                }

                // Eliminar los movimientos
                DB::table('movimientos_presta')
                    ->where('comprobante', $comprobante)
                    ->where('fecha', $fecha)
                    ->delete();

                // Eliminar duplicados
                $clientesUnicos = collect($clientesProcesados)->unique();

                // Actualizar fecha más reciente
                foreach ($clientesUnicos as $datos) {
                    $ultimaFecha = DB::table('movimientos_presta')
                        ->where('id_cliente', $datos['id_cliente'])
                        ->where('fecha_apertura', $datos['fecha_apertura'])
                        ->where('fecha_vencimiento', $datos['fecha_vencimiento'])
                        ->where('id_centro', $datos['id_centro'])
                        ->where('id_grupo', $datos['id_grupo'])
                        ->orderByDesc('fecha')
                        ->value('fecha');

                    DB::table('saldoprestamo')
                        ->where('id_cliente', $datos['id_cliente'])
                        ->where('FECHAAPERTURA', $datos['fecha_apertura'])
                        ->where('FECHAVENCIMIENTO', $datos['fecha_vencimiento'])
                        ->where('centro', $datos['id_centro'])
                        ->where('groupsolid', $datos['id_grupo'])
                        ->update([
                            'ULTIMA_FECHA_PAGADA' => $ultimaFecha ?: null
                        ]);
                }

                // Armar texto para bitácora
                $textoBitacora = "";
                foreach ($datosParaBitacora as $dato) {
                    $fechaFormateada = Carbon::parse($dato['fecha'])->format('d:m:y');

                    $textoBitacora .= "Nombre:{$dato['nombre_cliente']} {$dato['apellido_cliente']}\n";
                    $textoBitacora .= "Comprobante: {$dato['comprobante']}\n";
                    $textoBitacora .= "Valor cuota: {$dato['valor_cuota']}\n";
                    $textoBitacora .= "Centro: {$dato['nombre_centro']}\n";
                    $textoBitacora .= "Grupo: {$dato['nombre_grupo']}\n";
                    $textoBitacora .= "Fecha: {$fechaFormateada}\n";
                    $textoBitacora .= "-------------------------\n";
                }

                // Guardar en bitácora
                Bitacora::create([
                    'usuario' => Auth::user()->name,
                    'tabla_afectada' => 'Historial Cuotas',
                    'accion' => 'REVERSIÓN CUOTA',
                    'datos' => $textoBitacora,
                    'fecha' => Carbon::now(),
                    'id_asesor' => Auth::user()->id,
                    'comentarios' => $motivo
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Todos los movimientos fueron eliminados y actualizados correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar movimientos: ' . $e->getMessage()
            ], 500);
        }
    }




    public function validarPassword(Request $request)
    {
        // Validación del input
        $request->validate([
            'password' => 'required|string',
        ]);
        // Obtener la contraseña ingresada
        $password = $request->input('password');
        // Obtener el primer registro de la tabla SpecialPassword
        $SpecialPassword = SpecialPassword::first();

        if (!$SpecialPassword) {
            return response()->json(['valida' => false, 'mensaje' => 'No se encontró contraseña configurada']);
        }
        // Verificar si la contraseña es correcta
        $verificacion = Hash::check($password, $SpecialPassword->password);
        if ($verificacion) {
            return response()->json(['valida' => true]);
        }

        return response()->json(['valida' => false, 'mensaje' => 'Contraseña incorrecta']);
    }
}
