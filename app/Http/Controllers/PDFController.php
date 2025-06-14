<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Log;


class PDFController extends Controller
{


    public function estadoCuenta(Request $request)
    {
        $datosCuotas = $request->input('datosCuotas', []);
        $datosPagos = $request->input('datosPagos', []);
        $fechaSeleccionada = $request->input('fechaSeleccionada', '');
        $valorPonerseAlDia = $request->input('valorPonerseAlDia', '');
        $monto = $request->input('monto', '');
        $nombreCentro = $request->input('nombreCentro', '');
        $nombreGrupo = $request->input('nombreGrupo', '');
        $pdf = Pdf::loadView('pdf.estadoCuenta', [
            'datosCuotas' => $datosCuotas,
            'datosPagos' => $datosPagos,
            'fechaSeleccionada' => $fechaSeleccionada,
            'valorPonerseAlDia' => $valorPonerseAlDia,
            'monto' => $monto,
            'nombreCentro' => $nombreCentro,
            'nombreGrupo' => $nombreGrupo,
        ]);

        return $pdf->stream('estado_cuenta.pdf'); // Para ver en el navegador
        // return $pdf->download('estado_cuenta.pdf'); // Si quieres descargar
    }
}
