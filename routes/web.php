<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CentroController;
use App\Http\Controllers\GruposController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('guest','prevent.back.history')->group(function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::post('/loggear', [AuthController::class, 'loggear'])->name('loggear');
});


Route::middleware('auth','prevent.back.history')->group(function () {
    Route::get('/home', [AuthController::class, 'home'])->name('home');
    Route::get('/caja', [AuthController::class, 'caja'])->name('caja');
    Route::get('/clientes', [AuthController::class, 'contador'])->name('contador');
    Route::get('/grupos',[AuthController::class, 'grupos'])->name('grupos');
    Route::get('/asesores',[AuthController::class, 'mantenimientoAsesores'])->name('mantenimientoAsesores');
    Route::get('/reversiones',[AuthController::class, 'reverliquidacion'])->name('reverliquidacion');
    Route::post('/centros/guardar',[CentroController::class, 'store'])->name('centros.store');
    Route::post('7grupos/guardar',[GruposController::class, 'savegroup'])->name('grupos.savegroup');
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
