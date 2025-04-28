<?php

namespace App\Observers;

use App\Models\Bitacora;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BitacoraObserver
{
    public function created($model)
    {
        Bitacora::create([
            'usuario' => Auth::user()->name,  // Nombre del usuario autenticado
            'tabla_afectada' => $model->getTable(),  // Dinámicamente se toma el nombre de la tabla del modelo
            'accion' => 'INSERT',
            'datos' => json_encode($model), // Los datos insertados
            'fecha' => Carbon::now()->format('Y-m-d H:i:s'),  // Formato estándar de fecha y hora
            'id_asesor' => Auth::user()->id,
        ]);
    }

    // Método para la actualización de cualquier modelo
    public function updated($model)
    {
        Bitacora::create([
            'usuario' => Auth::user()->name,
            'tabla_afectada' => $model->getTable(),
            'accion' => 'UPDATE',
            'datos' => json_encode($model->getChanges()),  // Los datos modificados
            'fecha' => Carbon::now()->format('Y-m-d H:i:s'),  // Formato estándar de fecha y hora
            'id_asesor' => Auth::user()->id,
        ]);
    }

    // Método para la eliminación de cualquier modelo
    public function deleted($model)
    {
        Bitacora::create([
            'usuario' => Auth::user()->name,
            'tabla_afectada' => $model->getTable(),
            'accion' => 'DELETE',
            'datos' => json_encode($model),  // Los datos eliminados
            'fecha' => Carbon::now()->format('Y-m-d H:i:s'),  // Formato estándar de fecha y hora
            'id_asesor' => Auth::user()->id,
        ]);
    }
}
