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
        'created_at',
        'updated_at',
        'nrc',
        'conteo_rotacion'
    ];
    public function municipio()
    {
        return $this->belongsTo(Municipios::class, 'id_municipio');
    }
    public function departamento()
    {
        return $this->belongsTo(Departamentos::class, 'id_departamento');
    }
    public function Centros_Grupos_Clientes()
    {
        return $this->hasMany(Centros_Grupos_Clientes::class, 'cliente_id');
    }

    public function saldoprestamo()
    {
        // return $this->hasOne(SaldoPrestamo::class, 'id_cliente')->latestOfMany('id');
            return $this->hasOne(SaldoPrestamo::class, 'id_cliente')
                ->orderBy('id', 'desc'); 
    }

    
}
