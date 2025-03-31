<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipios extends Model
{
    protected $table = 'municipios';
    protected $fillable = ['id', 'nombre', 'id_departamentos'];

    public function departamento()
    {
        return $this->belongsTo(Departamentos::class, 'id_departamento');
    }
    public function clientes()
    {
        return $this->hasMany(Clientes::class, 'id_municipio');
    }
}
