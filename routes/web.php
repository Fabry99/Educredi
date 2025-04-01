<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CentroController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\GruposController;
use App\Models\Grupos;
use App\Models\Municipios;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest', 'prevent.back.history')->group(function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::post('/loggear', [AuthController::class, 'loggear'])->name('loggear');
});


Route::middleware('auth', 'prevent.back.history')->group(function () {
    Route::get('/home', [AuthController::class, 'home'])->name('home');
    Route::get('/caja', [AuthController::class, 'caja'])->name('caja');
    Route::get('/clientes', [AuthController::class, 'contador'])->name('contador');
    Route::get('/grupos', [AuthController::class, 'grupos'])->name('grupos');
    Route::get('/asesores', [AuthController::class, 'mantenimientoAsesores'])->name('mantenimientoAsesores');
    Route::get('/reversiones', [AuthController::class, 'reverliquidacion'])->name('reverliquidacion');
    Route::post('/centros/guardar', [CentroController::class, 'store'])->name('centros.store');
    Route::post('7grupos/guardar', [GruposController::class, 'savegroup'])->name('grupos.savegroup');
    Route::get('/grupos-por-centro/{id}', function ($id) {
        $grupos = Grupos::where('id_centros', $id)->get();
        return response()->json($grupos);
    });
    Route::get('/grupos-por-centro/{id}', function ($id) {
        // Recuperar los grupos del centro y contar la cantidad de clientes por grupo
        $grupos = Grupos::where('id_centros', $id)
            ->withCount('clientes')  // Añadir el conteo de clientes por grupo
            ->get();

        // Devolver la respuesta en formato JSON
        return response()->json($grupos);
    });
    Route::get('/municipios/{id}', function ($id) {
        return response()->json(Municipios::where('id_departamento', $id)->get());
    });
    Route::post('clientes/guardar', [ClientesController::class, 'saveclient'])->name('clientes.saveclient');
    Route::get('/obtener-cliente/{id}', [ClientesController::class, 'obtenerCliente']);

    Route::get('/grupos/{id}', function ($id) {
        return response()->json(Grupos::where('id_centros', $id)->get());
    });
    // routes/web.php
    Route::put('clientes/update', [ClientesController::class, 'updateclient'])
        ->name('clientes.update');

    Route::get('/clientes-por-grupo/{grupoId}', [ClientesController::class, 'clientesPorGrupo']);
    // Ruta para eliminar un cliente del grupo (actualizar su id_grupo a null)
    Route::put('/eliminarclientegrupo/{id}', [ClientesController::class, 'eliminarDelGrupo'])->name('eliminarclientegrupo');
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
