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
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->string('nombre_horario');
            $table->time('hora_entrada');
            $table->time('hora_salida');
            $table->time('hora_entrada_almuerzo')->nullable();
            $table->time('hora_salida_almuerzo')->nullable();
            $table->integer('tolerancia_entrada')->default(5);
            $table->integer('tolerancia_salida')->default(5);
            $table->json('dias_laborales');
            $table->boolean('flexible')->default(false);
            $table->boolean('nocturno')->default(false);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['empresa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
