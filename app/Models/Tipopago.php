<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipopago extends Model
{
    protected $table = 'tipopago';
    protected $fillable = ['id', 'nombre'];
}
