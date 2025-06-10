<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialPagos extends Model
{
    protected $table = 'historial_pagos';

    protected $fillable = [
        'id',
        'comprobante',
        'id_cliente',
        'monto',
        'saldo',
        'cuota',
        'fecha_pago',
        'intereses',
        'intereses_mora',
        'manejo',
        'seguro',
        'iva',
        'capital',
        'frecuencia',
        'fecha_apertura',
        'fecha_vencimiento',
        'id_centro',
        'id_grupo',
        'created_at',
        'updated_at',
    ];
}
