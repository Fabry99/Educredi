<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialPrestamos extends Model
{
    protected $table = 'historial_prestamos';

    protected $fillable =[
        'id',
        'id:cliente',
        'monto',
        'cuota',
        'plazo',
        'interes',
        'manejo',
        'fecha_apertura',
        'fecha_vencimiento',
        'asesor',
        'centro',
        'grupo',
        'created_at',
        'updated_at',
    ];
}
