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
        Schema::create('historial_prestamos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_cliente')->nullable();
            $table->decimal('monto', 11, 2)->nullable();
            $table->decimal('cuota', 9, 6)->nullable();
            $table->decimal('plazo', 3, 0)->nullable();
            $table->decimal('interes', 9, 6)->nullable();
            $table->decimal('manejo', 9, 6)->nullable();
            $table->date('fecha_apertura')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->bigInteger('asesor')->nullable();
            $table->bigInteger('centro')->nullable();
            $table->bigInteger('grupo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_prestamos');
    }
};
