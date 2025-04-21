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
        Schema::create('linea',function(Blueprint $table){
            $table->id();
            $table->string('nombre');
            $table->string('cod_garantia');
            $table->decimal('cobro_comision', 6,2);
            $table->decimal('tasa_interes', 6,2);
            $table->decimal('tasa_interes_mora', 6,2);
            $table->decimal('tasa_interes_legal', 6,2);
            $table->decimal('comision_banco', 6,2);
            $table->decimal('tasa_seguro', 8,4);
            $table->decimal('tasa_fdd', 6,2);
            $table->decimal('tasa_fdg', 6,2);
            $table->decimal('simul', 12,2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linea');
    }
};
