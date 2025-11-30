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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            // $table->foreignId('departamento_id')->constrained('departamentos')->cascadeOnDelete();
            $table->unsignedBigInteger('departamento_id');
            $table->string('codigo_empleado', 50);
            $table->string('dni', 20);
            $table->string('nombres');
            $table->string('apellidos');
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['M', 'F', 'Otro'])->nullable();
            $table->string('email')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->text('direccion')->nullable();
            $table->date('fecha_contratacion');
            $table->enum('tipo_contrato', ['indefinido', 'plazo_fijo', 'servicios']);
            $table->enum('estado', ['activo', 'inactivo', 'vacaciones', 'licencia'])->default('activo');
            $table->string('foto_perfil')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Unicidad por empresa
            $table->unique(['empresa_id', 'codigo_empleado']);
            $table->unique(['empresa_id', 'dni']);

            $table->index(['empresa_id', 'estado']);
            $table->index(['departamento_id', 'estado']);
            $table->index(['fecha_contratacion']);
            $table->index(['estado', 'fecha_contratacion']);
            $table->index(['nombres', 'apellidos']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
