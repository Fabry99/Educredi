<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Centros_Grupos_Clientes extends Model
{
    protected $table = 'centros_grupos_clientes';
    protected $fillable = ['id','centro_id','grupo_id','cliente_id','created_at','updated_at'];
    public function grupos(){
        return $this->belongsTo(Grupos::class, 'grupo_id');
    }
    public function clientes(){
        return $this->belongsTo(Clientes::class, 'cliente_id');
    }
    public function centros(){
        return $this->belongsTo(Centros::class, 'centro_id');
    }
}
