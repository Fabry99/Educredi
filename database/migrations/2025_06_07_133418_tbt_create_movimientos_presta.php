<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movimientos_presta', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_cliente')->nullable();
            $table->date('fecha')->nullable();
            $table->string('comprobante')->nullable();
            $table->decimal('tipo', 1, 0)->nullable();
            $table->decimal('valor_cuota', 11, 2)->nullable();
            $table->decimal('saldo', 11, 2)->nullable();
            $table->decimal('int_apli', 8, 2)->nullable();
            $table->decimal('int_mes', 8, 2)->nullable();
            $table->decimal('int_acum', 8, 2)->nullable();
            $table->decimal('int_mora', 8, 2)->nullable();
            $table->decimal('tipo_interes', 7, 2)->nullable();
            $table->decimal('capital_atrasado', 9, 2)->nullable();
            $table->decimal('saldo_anterior', 9, 2)->nullable();
            $table->string('mes_act')->nullable();
            $table->decimal('seguro', 10, 2)->nullable();
            $table->decimal('no_caja', 2, 0)->nullable();
            $table->decimal('val_mora', 10, 2)->nullable();
            $table->decimal('P110401', 11, 2)->nullable();
            $table->decimal('P110402', 11, 2)->nullable();
            $table->decimal('Rec_mora', 10, 2)->nullable();
            $table->decimal('numcaja', 2, 0)->nullable();
            $table->decimal('int_dic', 10, 2)->nullable();
            $table->decimal('abo_Cap', 10, 2)->nullable();
            $table->string('cobrador')->nullable();
            $table->string('procesad')->nullable();
            $table->bigInteger('sucursal')->nullable();
            $table->decimal('seg_inc', 12, 2)->nullable();
            $table->decimal('intcte', 10, 2)->nullable();
            $table->decimal('mora_pendiente', 10, 2)->nullable();
            $table->decimal('aportsusc', 10, 2)->nullable();
            $table->decimal('manejo', 10, 2)->nullable();
            $table->decimal('cobranza', 10, 2)->nullable();
            $table->decimal('notario', 10, 2)->nullable();
            $table->date('apertura')->nullable();
            $table->decimal('man_pendiente', 12, 2)->nullable();
            $table->decimal('seg_pend', 12, 2)->nullable();
            $table->decimal('aho_pend', 12, 2)->nullable();
            $table->decimal('apo_pend', 12, 2)->nullable();
            $table->decimal('juri_pend', 12, 2)->nullable();
            $table->decimal('sgd_pend', 12, 2)->nullable();
            $table->decimal('int_pend_pag', 10, 2)->nullable();
            $table->decimal('int_pend_pagm', 10, 2)->nullable();
            $table->decimal('int_pendm', 10, 2)->nullable();
            $table->decimal('iva', 12, 2)->nullable();
            $table->string('ctabanco')->nullable();
            $table->date('fecha_conta')->nullable();
            $table->decimal('exceso',12,2)->nullable();
            $table->date('fecha_apertura')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_presta');
    }
};
