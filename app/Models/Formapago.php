<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formapago extends Model
{
    protected $table = 'formapago';
    protected $fillable = ['id', 'nombre_formapago', 'created_at', 'updated_at'];
}
