<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientosPrestamos extends Model
{
    protected $table = 'movimientos_presta';
    protected $fillable =[
        'id',
        'id_cliente',
        'fecha',
        'comprobante',
        'tipo',
        'valor_cuota',
        'saldo',
        'int_apli',
        'int_mes',
        'int_acum',
        'int_mora',
        'tipo_interes',
        'capital',
        'saldo_anterior',
        'mes_act',
        'seguro',
        'no_caja',
        'val_mora',
        'P110401',
        'P110402',
        'Rec_mora',
        'numcaja',
        'int_dic',
        'abo_Cap',
        'cobrador',
        'procesad',
        'sucursal',
        'seg_inc',
        'intcte',
        'mora_pendiente',
        'aportsuc',
        'manejo',
        'cobranza',
        'notario',
        'apertura',
        'man_pendiente',
        'seg_pend',
        'aho_pend',
        'apo_pend',
        'juri_pend',
        'sdg_pend',
        'int_pend_pag',
        'int_pend_pagm',
        'int_pendm',
        'iva',
        'ctabanco',
        'fecha_conta',
        'exceso',
        'fecha_apertura',
        'fecha_vencimiento',
        'created_at',
        'updated_at',
        'dias'
    ];
}
