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
        Schema::create('debeser', function(Blueprint $table){
            $table->id();
            $table->foreignId('id_cliente')->constrained('clientes')->onDelete('cascade');
            $table->date('fecha');
            $table->decimal('cuota',11,2);
            $table->decimal('saldo',11,2);
            $table->decimal('tasa_interes',7,2);
            $table->decimal('dias',5,0);
            $table->decimal('plazo',5,0);
            $table->decimal('manejo',10,2);
            $table->decimal('seguro',10,2);
            $table->decimal('simultan',10,2);
            $table->decimal('aportac',10,2);
            $table->decimal('capital',10,2);
            $table->decimal('iva',12,2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debeser');
    }
};
