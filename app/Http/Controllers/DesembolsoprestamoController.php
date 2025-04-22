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
        return view('modules.dashboard.desembolso', compact('rol', 'clientes', 'sucursales', 'linea', 'supervisor', 'colector',
         'aprobaciones', 'tipopago'));
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
}
