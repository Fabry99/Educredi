<?php

namespace App\Http\Controllers;

use App\Models\Asesores;
use App\Models\Bitacora;
use App\Models\Centros;
use App\Models\Centros_Grupos_Clientes;
use App\Models\Clientes;
use App\Models\Grupos;
use App\Models\HistorialPagos;
use App\Models\HistorialPrestamos;
use App\Models\saldoprestamo;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferenciacarteraController extends Controller
{

    public function transferenciadecartera()
    {
        $rol = Auth::user()->rol;
        $centro = Centros::all();
        $asesor = Asesores::all();
        $grupos = Grupos::all();
        // Verificar si el rol es 'contador'
        if ($rol !== 'contador' && $rol !== 'administrador') {
            // Si no es contador, redirigir o mostrar un mensaje de error
            return redirect()->back()->with('error', 'No tienes acceso a esta sección.');
        }

        // Si el rol es 'contador', cargar la vista
        return view('modules.dashboard.transferencia', compact('rol', 'centro', 'asesor', 'grupos'));
    }

    public function obtenerGrupos($id_centro)
    {
        $grupos = Grupos::where('id_centros', $id_centro)
            ->get();



        return response()->json($grupos);
    }


    public function obtenerDatosTabla($id_asesor, $id_grupo, $id_centro)
    {


        $DatosClientes = saldoprestamo::with('clientes') // Asegúrate de que la relación esté definida
            ->where('centro', $id_centro)
            ->where('groupsolid', $id_grupo)
            ->where('ASESOR', $id_asesor)
            ->select('id', 'MONTO', 'id_cliente', 'FECHAAPERTURA')
            ->get();

        $totalFilas = $DatosClientes->count();
        $totalMonto = $DatosClientes->sum('MONTO');

        // Estructura de respuesta con nombres incluidos
        $respuesta = [
            'datos' => $DatosClientes->map(function ($dato) {
                return [
                    'id' => $dato->id,
                    'monto' => $dato->MONTO,
                    'fecha_apertura' => $dato->FECHAAPERTURA,
                    'cliente_id' => $dato->id_cliente,
                    'cliente_nombre' => optional($dato->clientes)->nombre
                        . ' ' . optional($dato->clientes)->apellido ?? 'Sin nombre',
                ];
            }),
            'total_registros' => $totalFilas,
            'total_monto' => $totalMonto,
        ];


        return response()->json($respuesta);
    }

    public function transferirCartera(Request $request)
    {

        $request->validate([
            'prestamos' => 'required|array|min:1',
            'id_asesorReceptor' => 'required|integer',
        ]);

        DB::beginTransaction();

        try {
            $asesorEmisor = $request->input('nombre_asesoremisor');
            $nombre_centro = $request->input('nombre_centro');
            $nombre_grupo = $request->input('nombre_grupo');
            $asesorReceptor = $request->input('asesorreceptor');
            $motivo = $request->input('comentarios', null);

            $idsPrestamos = collect($request->input('prestamos'))->pluck('id')->toArray();

            $prestamos = saldoprestamo::whereIn('id', $idsPrestamos)->get();

            foreach ($prestamos as $prestamo) {

                $idCliente = $prestamo->id_cliente;
                $fechaApertura = $prestamo->FECHAAPERTURA ?? null;
                $fechaVencimiento = $prestamo->FECHAVENCIMIENTO ?? null;
                $groupSolid = $prestamo->groupsolid ?? null;

             

                // Actualizar asesor en saldoprestamo
                $prestamo->asesor = $request->id_asesorReceptor;
                $prestamo->save();

                // Buscar en historialprestamos
                $historial = HistorialPrestamos::where('id_cliente', $idCliente)
                    ->where('fecha_apertura', $fechaApertura)
                    ->where('fecha_vencimiento', $fechaVencimiento)
                    ->where('grupo', $groupSolid)
                    ->first();

                if ($historial) {

                    $historial->asesor = $request->id_asesorReceptor;
                    $historial->save();

                } else {
                }
            }

            // Bitácora
            $textoBitacora = "";
            $textoBitacora .= "Asesor que transfiere: {$asesorEmisor}\n";
            $textoBitacora .= "Centro: {$nombre_centro}\n";
            $textoBitacora .= "Grupo: {$nombre_grupo}\n";
            $textoBitacora .= "Asesor que recibe: {$asesorReceptor}\n";
            $textoBitacora .= "Clientes transferidos:\n";

            foreach ($prestamos as $p) {
                $cliente = Clientes::find($p->id_cliente);
                $nombre = $cliente ? "{$cliente->nombre} {$cliente->apellido}" : "Desconocido";

                $textoBitacora .= "- {$nombre} | Monto: $" . number_format($p->MONTO, 2) . " | Cuota: $" . number_format($p->CUOTA, 2) . "\n";
            }

            $textoBitacora .= "-------------------------\n";

            Bitacora::create([
                'usuario' => Auth::user()->name,
                'tabla_afectada' => 'Historial Prestamos',
                'accion' => 'TRANSFERENCIA DE CARTERA',
                'datos' => $textoBitacora,
                'fecha' => Carbon::now(),
                'id_asesor' => Auth::user()->id,
                'comentarios' => $motivo
            ]);

            DB::commit();

            return response()->json(['mensaje' => 'Transferencia completada']);
        } catch (\Exception $e) {
            DB::rollBack();

        }
    }
}
