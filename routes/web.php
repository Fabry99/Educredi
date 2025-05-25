<?php

use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\AsesoresController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CentroController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\DesembolsoprestamoController;
use App\Http\Controllers\DesemsolsoprestamoController;
use App\Http\Controllers\GruposController;
use App\Http\Controllers\MovimientocajaController;
use App\Http\Controllers\pdfController;
use App\Http\Controllers\TransferenciacarteraController;
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
    Route::get('/caja', [MovimientocajaController::class, 'caja'])->name('caja');
    Route::get('/mov_caja',[AuthController::class, 'mov_caja'])->name('mov_caja');
    // Rutas Contador
    Route::get('/clientes', [AuthController::class, 'contador'])->name('contador');
    Route::get('/grupos', [AuthController::class, 'grupos'])->name('grupos');
    Route::get('/asesores', [AsesoresController::class, 'mantenimientoAsesores'])->name('mantenimientoAsesores');
    Route::get('/reversiones', [AuthController::class, 'reverliquidacion'])->name('reverliquidacion');
    Route::get('/prestamos', [DesembolsoprestamoController::class,  'creditos'])->name('creditos');
    Route::get('/cambiardatos', [AuthController::class, 'cambiardatos'])->name('cambiardatos');
    Route::get('/transferenciadecartera', [TransferenciacarteraController::class, 'transferenciadecartera'])->name('transferenciadecartera');
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
    Route::post('/guardarprestamogrupal', [DesembolsoprestamoController::class,'almacenarPrestamos']);
    Route::post('/guardarprestamoindividual', [DesembolsoprestamoController::class,'almacenarPrestamoIndividual']);

    Route::get('/consulta/reversion/{codigo}', [DesembolsoprestamoController::class, 'obtenerSaldoPrestamo']);
    Route::POST('/validar/password', [DesembolsoprestamoController::class, 'validarPassword']);
    Route::delete('/eliminar/desembolsoprestamo/{codigoCliente}', [DesembolsoprestamoController::class, 'eliminarDesembolso']);

    //Rutas Para Mantenimiento de Asesores
    Route::post('/asesores/insert/', [AsesoresController::class, 'InsertarAsesor'])->name('asesor.insert');
    Route::put('/update/asesor/{id}', [AsesoresController::class, 'updateAsesor']); 

    //Rutas Para Transferencia de cartera
    Route::get('/trasferencia/obtenergrupos/{id_centro}',[TransferenciacarteraController::class, 'obtenerGrupos']); 
    Route::get('/transferencia/obtenerPrestamos/{id_asesor}/{id_grupo}/{id_centro}',[TransferenciacarteraController::class,'obtenerDatosTabla']);
    Route::post('/transferencia/transferircartera', [TransferenciacarteraController::class, 'transferirCartera']);

    //Rutas para manejo de caja
    Route::post('/caja/obtenerPrestamos',[MovimientocajaController::class, 'obtenerPrestamos']);
    // Route::get('/generar-pdf', [pdfController::class, 'generarPdf']);
    Route::get('/generar-pdf', [DesembolsoprestamoController::class, 'generarPDFPrestamoGrupal'])->name('generar.pdf');
    Route::post('/caja/obtenerconteocuotas',[MovimientocajaController::class, 'obtenerConteoCuotas']);
        

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
