<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departamentos', function (Blueprint $table) {
            $table->foreign('jefe_empleado_id')
                  ->references('id')
                  ->on('empleados')
                  ->nullOnDelete();
        });

        Schema::table('empleados', function (Blueprint $table) {
            $table->foreign('departamento_id')
                  ->references('id')
                  ->on('departamentos')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('departamentos', function (Blueprint $table) {
            $table->dropForeign(['jefe_empleado_id']);
        });
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropForeign(['departamento_id']);
        });
    }
};
