<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    protected $table = 'clientes';
    protected $fillable = [
        'id',
        'nombre',
        'apellido',
        'direccion',
        'genero',
        'fecha_nacimiento',
        'sector',
        'direc_trabajo',
        'telefono_casa',
        'telefono_oficina',
        'ing_economico',
        'egre_economico',
        'otros_ing',
        'dui',
        'lugar_expe',
        'estado_civil',
        'nit',
        'nombre_conyugue',
        'id_departamento',
        'id_municipio',
        'lugar_nacimiento',
        'persona_dependiente',
        'fecha_expedicion',
        'nacionalidad',
        'act_economica',
        'ocupacion',
        'puede_firmar',
        'id_centro',
        'id_grupo',
        'created_at',
        'updated_at',
        'nrc',
    ];
    public function municipio()
    {
        return $this->belongsTo(Municipios::class, 'id_municipio');
    }
    public function departamento()
    {
        return $this->belongsTo(Departamentos::class, 'id_departamento');
    }
    public function centro()
    {
        return $this->belongsTo(Centros::class, 'id_centro');
    }
    public function grupo()
    {
        return $this->belongsTo(Grupos::class, 'id_grupo');
    }
}
