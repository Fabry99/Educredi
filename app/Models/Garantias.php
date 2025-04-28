<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Garantias extends Model
{
    protected $table = 'garantias';
    protected $fillable =[
        'id',
        'nombre',
        'infored',
    ];
}
