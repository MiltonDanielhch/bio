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
        Schema::create('logs_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('accion');
            $table->text('descripcion');
            $table->json('datos_antes')->nullable();
            $table->json('datos_despues')->nullable();
            $table->string('tabla_afectada')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users');
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['tabla_afectada', 'created_at']);
            $table->index(['usuario_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_sistema');
    }
};
