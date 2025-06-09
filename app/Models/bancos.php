<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bancos extends Model
{
    protected $table = 'bancos';
    protected $fillable =['id', 'nombre_banco','created_at','updated_at','cuenta_banco','ctabanco'];
}
