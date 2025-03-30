<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupos extends Model
{
    protected $table = 'grupos';

    protected $fillable = ['id', 'nombre', 'id_centros', 'id_asesor', 'created_at', 'updated_at'];


    // En el modelo Grupo (Grupos.php)
    public function centro()
    {
        return $this->belongsTo(Centros::class, 'id_centros');
    }
}
