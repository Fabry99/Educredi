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
        Schema::create('clientes', function (Blueprint $table){
            $table->id();
            $table->string('nombre', 250);
            $table->string('apellido', 250);
            $table->string('direccion', 350);
            $table->enum('genero', ['masculino', 'femenino']);
            $table->date('fecha_nacimiento');
            $table->string('sector', 250);
            $table->string('direc_trabajo', 350);
            $table->string('telefono_casa', 20);
            $table->string('telefono_oficina', 20);
            $table->string('ing_economico', 20);
            $table->string('egre_economico', 20);
            $table->string('otros_ing', 20);
            $table->string('dui',20);
            $table->string('lugar_expe', 150);
            $table->enum('estado_civil', ['soltero', 'casado', 'divorciado', 'viudo']);
            $table->string('nit', 20);
            $table->string('nombre_conyugue', 300);
            $table->foreignId('id_departamento');
            $table->foreignId('id_municipio');
            $table->string('lugar_nacimiento', 250);
            $table->string('persona_dependiente', 50);
            $table->date('fecha_expedicion');
            $table->string('nacionalidad',150);
            $table->string('act_economica',250);
            $table->string('ocupacion', 250);
            $table->enum('puede_firmar', ['si', 'no']);
            $table->foreignId('id_centro');
            $table->foreignId('id_grupo');
            $table->integer('conteo_rotacion')->default(0); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
