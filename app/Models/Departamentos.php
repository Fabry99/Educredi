<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamentos extends Model
{
    protected $table = 'departamentos';
    protected $fillable = ['id', 'nombre'];

    public function municipios()
    {
        return $this->hasMany(Municipios::class, 'id_departamento');
    }
    public function clientes()
    {
        return $this->hasMany(Clientes::class, 'id_departamento');
    }
}
