<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Linea extends Model
{
    protected $table = 'linea';
    protected $fillable = [
        'id',
        'nombre',
        'cod_garantia',
        'cobro_comision',
        'tasa_interes',
        'tasa_interes_mora',
        'tasa_interes_legal',
        'comision_banco',
        'tasa_seguro',
        'tasa_fdd',
        'tasa_fdg',
        'simul'

    ];
}
