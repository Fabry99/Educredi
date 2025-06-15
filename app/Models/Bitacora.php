<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use HasFactory;
    protected $table = 'bitacora';
    public $timestamps = false;
    protected $fillable = [
        'usuario',
        'tabla_afectada',
        'accion',
        'datos',
        'fecha',
        'id_asesor',
        'comentarios'
    ];


    public function user(){
        return $this->belongsTo(User::class, 'id_asesor');
    }
}
