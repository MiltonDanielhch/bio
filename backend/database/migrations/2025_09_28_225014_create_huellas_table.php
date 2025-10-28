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
        Schema::create('huellas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnDelete();
            $table->integer('zk_user_id');
            $table->binary('template_huella');
            $table->unsignedTinyInteger('numero_dedo')->nullable();
            $table->enum('calidad', ['alta', 'media', 'baja'])->default('media');
            $table->enum('formato_template', ['iso', 'ansi', 'zk'])->default('zk');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();

            $table->unique(['empleado_id', 'numero_dedo']);
            $table->index(['empleado_id']);
            $table->index(['zk_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('huellas');
    }
};
