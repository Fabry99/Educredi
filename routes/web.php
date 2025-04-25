<?php

use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CentroController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\DesembolsoprestamoController;
use App\Http\Controllers\DesemsolsoprestamoController;
use App\Http\Controllers\GruposController;
use App\Http\Controllers\UserController;
use App\Models\Grupos;
use App\Models\Municipios;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest', 'prevent.back.history')->group(function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::post('/loggear', [AuthController::class, 'loggear'])->name('loggear');
    Route::get('/prueba', function () {
        $tasa_interes = 115;
        $monto = 200;
        $diasEntreCuotas = 14;
        $cantidadCuotas = 12;
        $tasa_iva = 0.13;
        $FechaApertura = 

        //1-Formula tasa diaria
        $tasaDiaria = ($tasa_interes / 360) / 100;
        //1-Formula para calcular el manejo
        $manejo = (10 / $cantidadCuotas);

        //2-Formula para calcular intereses
        $intereses = ($monto * $tasaDiaria * $diasEntreCuotas);

        //3- Formula para calcular el porcentaje del monto
        $porcentajemonto = ($monto * (2) / 100);
        //3- Formula para calcular el seguro diario
        $segurodiario = ($porcentajemonto / 365);
        //3- Formula para calcular el micro seguro
        $microseguro = ($segurodiario * $diasEntreCuotas) * (1 + $tasa_iva);

        //4- Formula para calcular el IVA
        $iva = ($intereses * $tasa_iva);

        //5- Formula para calcular la tasa diaria para la cuota
        $tasadiariaparacuota = ($tasa_interes / 365) / 100;
        //5- Formula para calcular la tasa por periodo
        $tasaporperiodo = ($tasadiariaparacuota * $diasEntreCuotas);
        //5- Formula para calcular para calcular la cuota
        $valorcuota = ($monto * $tasaporperiodo * (1 + $tasaporperiodo) ** $cantidadCuotas) / ((1 + $tasaporperiodo) ** $cantidadCuotas - 1);
        $cuota = ($valorcuota + $iva + $manejo - $microseguro);

        //6- Formula para calcular el valor del capital
        $capital = ($cuota - $intereses - $manejo - $microseguro - $iva);

        //7- Formula para calcular el seguro
        $seguro = ($intereses + $capital + $iva);

        echo 'manejo:', $manejo;
        echo "<br>";
        echo  'tasa diaria:', $tasaDiaria;
        echo "<br>";
        echo 'intereses:', $intereses;
        echo "<br>";
        echo "porcentaje monto: " . $porcentajemonto;
        echo "<br>";
        echo "seguro diario:" . $segurodiario;
        echo "<br>";
        echo "microseguro:" . $microseguro;
        echo "<br>";
        echo "iva:" . $iva;
        echo "<br>";
        echo "tasa diaria para cuota:" . $tasadiariaparacuota;
        echo "<br>";
        echo "tasa por periodo:" . $tasaporperiodo;
        echo "<br>";
        echo "cuota primaria:" . $valorcuota;
        echo "<br>";
        echo "cuota: " . $cuota;
        echo "<br>";
        echo "capital: " . $capital;
        echo "<br>";
        echo "seguro: " . $seguro;

        //     //Datos de entrada dinamicos
        // $tasa_interes = 115; // anual en %
        // $monto = 200;
        // $diasEntreCuotas = 14;
        // $cantidadCuotas = 12;
        // $tasa_iva = 0.13;
        // $iva = 0;
        // $saldo = $monto;

        // //Formula para calcular el manejo
        // $manejo = 10 / $cantidadCuotas;
        // //Formula para calcular la tasa diaria
        // $tasaDiaria   = ($tasa_interes / 100) / 360;


        // $intereses = $saldo * $tasaDiaria * $diasEntreCuotas;
        // $iva       = $intereses * $tasa_iva;


        // //Formula para calcular el micro seguro
        // $porcentajemonto = $monto * (0.02);
        // $segurodiario = $porcentajemonto / 365;
        // $microseguro = $segurodiario * $diasEntreCuotas * (1 + $tasa_iva);


        // $tasadiariaparacuota = ($tasa_interes / 100) / 365;
        // $tasaporperiodo = $tasadiariaparacuota * $diasEntreCuotas;
        // $cuota = ($monto * $tasaporperiodo * (1 + $tasaporperiodo) ** $cantidadCuotas) / ((1 + $tasaporperiodo) ** $cantidadCuotas - 1);

        // $valor_cuota = ($cuota + $iva + $manejo -$microseguro);
        // $capital   = ceil(($valor_cuota - $intereses - $manejo - $microseguro - $iva) * 100) / 100;

        // $seguro = ($intereses + $iva + $capital);
        // $fechaInicio = Carbon::create(2025, 1, 1);
        // $fechaFinal = Carbon::create(2025, 6, 18);



        // // Salida
        // echo "<h3>Calendario de Cuotas</h3>";
        // echo "<table border='1' cellpadding='6' cellspacing='0'>";
        // echo "<tr>
        //     <th># Cuota</th>
        //     <th>Fecha</th>
        //     <th>Valor Cuota</th>
        //     <th>Monto</th>
        //     <th>Tasa Interes</th>
        //     <th>Intereses</th>
        //     <th>Manejo</th>
        //     <th>Seguro</th>
        //     <th>Capital</th>
        //     <th>IVA</th>
        //   </tr>";

        // $contador = 1;
        // $esPrimera = true;

        // while ($fechaInicio->lte($fechaFinal) && $saldo > 0) {
        //     echo "<tr>";
        //     echo "<td>$contador</td>";
        //     echo "<td>" . $fechaInicio->toDateString() . "</td>";

        //     if ($esPrimera) {
        //         // Primera cuota: referencia del préstamo
        //         echo "<td>$" . number_format($valor_cuota, 2) . "</td>";
        //         echo "<td>$" . number_format($saldo, 2) . "</td>";
        //         echo "<td>" . number_format($tasa_interes, 2) . "</td>";
        //         echo "<td>-</td>"; // intereses
        //         echo "<td>$" . number_format($manejo, 2) . "</td>";
        //         echo "<td>$" . number_format($seguro, 2) . "</td>";
        //         echo "<td>-</td>"; // capital
        //         echo "<td>-</td>"; // iva

        //         $esPrimera = false;
        //     } else {
        //         // Cuotas posteriores

        //         $capital   = ceil(($valor_cuota - $intereses - $manejo - $microseguro - $iva) * 100) / 100;

        //         if ($capital > $saldo) {
        //             $capital = $saldo;
        //         }

        //         // Descontar capital antes de mostrar el nuevo saldo
        //         $saldo -= $capital;

        //         echo "<td>$" . number_format($valor_cuota, 2) . "</td>";
        //         echo "<td>$" . number_format($saldo, 2) . "</td>";
        //         echo "<td>" . number_format($tasa_interes, 2) . "</td>";
        //         echo "<td>$" . number_format($intereses, 2) . "</td>";
        //         echo "<td>$" . number_format($manejo, 2) . "</td>";
        //         echo "<td>$" . number_format($microseguro, 2) . "</td>";
        //         echo "<td>$" . number_format($capital, 2) . "</td>";
        //         echo "<td>$" . number_format($iva, 2) . "</td>";
        //     }

        //     echo "</tr>";
        //     $fechaInicio->addDays($diasEntreCuotas);
        //     $contador++;
        // }

        // echo "</table>";

    })->name('prueba');
});


Route::middleware('auth', 'prevent.back.history')->group(function () {
    // Rutas Administrador
    Route::get('/home', [AdministradorController::class, 'home'])->name('home');
    Route::get('/bitacora', [AdministradorController::class, 'bitacora'])->name('bitacora');
    Route::get('/usuarios', [AdministradorController::class, 'usuarios'])->name('usuarios');
    Route::post('/usuarios/nuevousuario', [UserController::class, 'nuevousuario'])->name('usuarios.nuevousuario');
    Route::get('/admin/usurios/obtener-user/{id}', [UserController::class, 'obtenerUser'])->name('usuarios.obtenerUser');
    Route::put('/admin/usuarios-update/', [UserController::class, 'updateuser'])
        ->name('user.update');
    Route::get('/admin/clientes', [AdministradorController::class, 'clientesadmin'])->name('admin.clientes');



    // Rutas caja
    Route::get('/caja', [AuthController::class, 'caja'])->name('caja');

    // Rutas Contador
    Route::get('/clientes', [AuthController::class, 'contador'])->name('contador');
    Route::get('/grupos', [AuthController::class, 'grupos'])->name('grupos');
    Route::get('/asesores', [AuthController::class, 'mantenimientoAsesores'])->name('mantenimientoAsesores');
    Route::get('/reversiones', [AuthController::class, 'reverliquidacion'])->name('reverliquidacion');
    Route::get('/prestamos', [DesembolsoprestamoController::class,  'creditos'])->name('creditos');
    Route::get('/cambiardatos', [AuthController::class, 'cambiardatos'])->name('cambiardatos');
    Route::get('/transferenciadecartera', [AuthController::class, 'transferenciadecartera'])->name('transferenciadecartera');
    Route::post('/centros/guardar', [CentroController::class, 'store'])->name('centros.store');
    Route::post('7grupos/guardar', [GruposController::class, 'savegroup'])->name('grupos.savegroup');
    Route::get('/grupos-por-centro', [GruposController::class, 'obtenerGruposPorCentro']);

    Route::get('/grupos-centros/{id}', [GruposController::class, 'gruposcentros']);
    Route::get('/municipios/{id}', function ($id) {
        return response()->json(Municipios::where('id_departamento', $id)->get());
    });
    Route::post('clientes/guardar', [ClientesController::class, 'saveclient'])->name('clientes.saveclient');
    Route::get('/obtener-cliente/{id}', [ClientesController::class, 'obtenerCliente']);
    Route::get('/obtener-centros/{id}', [CentroController::class, 'obtenercentro']);


    Route::get('/grupos/{id}', function ($id) {
        return response()->json(Grupos::where('id_centros', $id)->get());
    });
    Route::put('clientes/update', [ClientesController::class, 'updateclient'])
        ->name('clientes.update');
    // Ruta para actualizar el centro
    Route::post('/actualizar-centro/{id}', [CentroController::class, 'actualizarCentro'])->name('centros.update');




    Route::get('/clientes-por-grupo/{grupoId}', [ClientesController::class, 'clientesPorGrupo']);
    Route::delete('/eliminarclientegrupo', [ClientesController::class, 'eliminarDelGrupo'])->name('eliminarclientegrupo');

    // Ruta para eliminar un centro

    Route::delete('/centros/eliminar/{id}', [CentroController::class, 'eliminar'])->name('centros.eliminar');
    Route::delete('/eliminar-grupo/{id}', [GruposController::class, 'eliminarGrupo']);

    //Rutas para desembolso de prestamos
    Route::get('/prestamos/obtener-centros-grupos-clientes/{id}', [DesembolsoprestamoController::class, 'obtenerCentrosGruposClientes']);
    Route::get('/prestamos/obtenergrupos-clientes/{centro_id}/{grupo_id}', [DesembolsoprestamoController::class, 'obtenergruposclientes']);
});


Route::middleware('auth')->get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->get('/redirect', function () {
    $user = Auth::user();

    switch ($user->rol) {
        case 'administrador':
            return redirect()->route('home');
        case 'caja':
            return redirect()->route('caja');
        case 'contador':
            return redirect()->route('contador');
        default:
            return redirect()->route('login');
    }
});
