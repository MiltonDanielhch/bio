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
        Schema::create('dispositivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->constrained('sucursales')->cascadeOnDelete();
            $table->string('nombre_dispositivo');
            $table->enum('tipo', ['huella', 'facial', 'huella_facial', 'tarjeta']);
            $table->string('numero_serie')->unique();
            $table->string('direccion_ip', 45)->nullable();
            $table->unsignedSmallInteger('puerto')->default(4370);
            $table->unsignedSmallInteger('password')->default(0);
            $table->string('ubicacion')->nullable();
            $table->enum('estado', ['activo', 'inactivo', 'mantenimiento'])->default('activo');
            $table->timestamp('ultima_conexion')->nullable();
            $table->string('version_firmware')->nullable();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('ultimo_user_id')->default(0)->comment('Último ID de usuario sincronizado');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['direccion_ip']);
            $table->index(['sucursal_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispositivos');
    }
};
