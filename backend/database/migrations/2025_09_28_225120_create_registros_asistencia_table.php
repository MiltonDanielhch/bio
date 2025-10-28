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
        Schema::create('registros_asistencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->foreignId('dispositivo_id')->constrained('dispositivos')->cascadeOnDelete();
            $table->enum('tipo_marcaje', ['entrada', 'salida', 'entrada_almuerzo', 'salida_almuerzo', 'general']);
            $table->date('fecha_local');
            $table->time('hora_local');
            $table->timestamp('fecha_hora');
            $table->enum('tipo_verificacion', ['huella', 'rostro', 'tarjeta', 'manual', 'clave']);
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->decimal('precision_ubicacion', 5, 2)->nullable();
            $table->decimal('confianza_verificacion', 5, 2)->nullable();
            $table->boolean('procesado')->default(false);
            $table->foreignId('incidencia_id')->nullable()->constrained('incidencias');
            $table->enum('estado_validacion', ['pendiente', 'validado', 'duplicado', 'error'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['empleado_id', 'fecha_hora']);
            $table->index(['dispositivo_id', 'fecha_hora']);
            $table->index(['fecha_local', 'empleado_id']);
            $table->index(['fecha_hora']);
            $table->index(['estado_validacion', 'procesado']);
            $table->index(['fecha_local', 'tipo_marcaje']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_asistencia');
    }
};
