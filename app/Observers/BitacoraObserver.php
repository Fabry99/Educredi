<?php

namespace App\Observers;

use App\Models\Bitacora;
use Illuminate\Support\Facades\Auth;

class BitacoraObserver
{
    public function created($centros)
    {
        /**
     * Maneja el evento de creación de un modelo.
     *
     * @param  \App\Models\Centros  $centros
     * @return void
     */
        Bitacora::create([
            'usuario' => Auth::user()->name,  // Nombre del usuario autenticado
            'tabla_afectada' => 'centros',
            'accion' => 'INSERT',
            'datos' => json_encode($centros), // Los datos insertados
            'fecha' => now(),
        ]);
    }
    /**
     * Maneja el evento de actualización de un modelo.
     *
     * @param  \App\Models\Centros  $centros
     * @return void
     */
    public function updated($centros)
    {
        Bitacora::create([
            'usuario' => Auth::user()->name,
            'tabla_afectada' => 'centros',
            'accion' => 'UPDATE',
            'datos' => json_encode($centros->getChanges()),  // Datos modificados
            'fecha' => now(),
        ]);
    }
    /**
     * Maneja el evento de eliminación de un modelo.
     *
     * @param  \App\Models\Centros  $centros
     * @return void
     */
    public function deleted($centros)
    {
        Bitacora::create([
            'usuario' => Auth::user()->name,
            'tabla_afectada' => 'centros',
            'accion' => 'DELETE',
            'datos' => json_encode($centros), // Datos eliminados
            'fecha' => now(),
        ]);
    }
}
