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
        Schema::create('dispositivo_empleado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->foreignId('dispositivo_id')->constrained('dispositivos')->cascadeOnDelete();
            $table->integer('zk_user_id')->comment('ID asignado en el dispositivo');
            $table->string('privilegio')->default('usuario');
            $table->string('tarjeta_id')->nullable();
            $table->enum('estado', ['activo', 'eliminado_en_device'])->default('activo');
            $table->enum('estado_sincronizacion', ['pendiente', 'sincronizado', 'error'])->default('pendiente');
            $table->timestamp('ultima_sincronizacion')->nullable();
            $table->timestamps();

            // ÍNDICES CON NOMBRES MÁS CORTOS
            $table->unique(['empleado_id', 'dispositivo_id'], 'idx_emp_dispositivo');
            $table->unique(['dispositivo_id', 'zk_user_id'], 'idx_dispositivo_zkuser');
            $table->index(['dispositivo_id', 'estado_sincronizacion'], 'idx_dispositivo_estado');
            $table->index(['estado_sincronizacion', 'ultima_sincronizacion'], 'idx_sincro_ultima');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispositivo_empleado');
    }
};
