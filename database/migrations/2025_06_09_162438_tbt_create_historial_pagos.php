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
        Schema::create('historial_pagos', function(Blueprint $table){
            $table->id();
            $table->string('comprobante')->nullable();
            $table->bigInteger('id_cliente')->nullable();
            $table->decimal('monto',10,2)->nullable();
            $table->decimal('saldo',10,2)->nullable();
            $table->decimal('cuota',10,2)->nullable();
            $table->date('fecha_pago')->nullable();
            $table->decimal('intereses',10,2)->nullable();
            $table->decimal('intereses_mora',10,2)->nullable();
            $table->decimal('manejo',10,2)->nullable();
            $table->decimal('seguro',10,2)->nullable();
            $table->decimal('iva',10,2)->nullable();
            $table->decimal('capital',10,2)->nullable();
            $table->decimal('frecuencia',10,2)->nullable();
            $table->date('fecha_apertura')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->bigInteger('id_centro')->nullable();
            $table->bigInteger('id_grupo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_pagos');
    }
};
