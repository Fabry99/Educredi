<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 250);
            $table->timestamps();
        });

        // Insertar datos iniciales
        DB::table('departamentos')->insert([
            ['nombre' => 'Ahuachapan'],
            ['nombre' => 'Cabañas'],
            ['nombre' => 'Chalatenango'],
            ['nombre' => 'Cuscatlan'],
            ['nombre' => 'La Libertad'],
            ['nombre' => 'La Paz'],
            ['nombre' => 'La Union'],
            ['nombre' => 'Morazan'],
            ['nombre' => 'San Miguel'],
            ['nombre' => 'San Salvador'],
            ['nombre' => 'Santa Ana'],
            ['nombre' => 'San Vicente'],
            ['nombre' => 'Sonsonate'],
            ['nombre' => 'Usulutan'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};
