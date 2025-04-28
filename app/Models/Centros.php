<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Centros extends Model
{   

    protected $table = 'centros'; // Especificamos la tabla

    protected $fillable = ['id','nombre','id_asesor','created_at','updated_at']; 
    
    
    public function asesor()
    {
        return $this->belongsTo(User::class, 'id_asesor');
    }
    public function Centros_Grupos_Clientes()
    {
        return $this->hasMany(Centros_Grupos_Clientes::class, 'centro_id');
    }
    
    public function grupos(){
        return $this->hasMany(Grupos::class, 'id_centros');
    }
}
