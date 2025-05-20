<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asesores extends Model
{
    protected $table = 'asesores';
    protected $fillable = ['id','nombre','id_sucursal','created_at','updated_at'];

    public function sucursales(){
        return $this->belongsTo(Sucursales::class, 'id_sucursal');
    }
}
