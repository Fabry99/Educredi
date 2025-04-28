<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class debeser extends Model
{
    protected $table = 'debeser';
    protected $fillable =[
        'id',
        'id_clientes',
        'fecha',
        'cuota',
        'saldo',
        'tasa_interes',
        'dias',
        'plazo',
        'manejo',
        'seguro',
        'simultan',
        'aportac',
        'capital',
        'iva',
        'intereses',
        'created_at',
        'updated_at'
    ];
}
