<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->string('user_id'); // El ID del usuario en el dispositivo
            $table->unsignedInteger('uid')->nullable(); // UID interno del dispositivo
            $table->dateTime('timestamp'); // Fecha y hora del registro
            $table->unsignedInteger('status'); // Estado (entrada, salida, etc.)
            $table->unsignedInteger('punch'); // Tipo de marcaje (huella, tarjeta, etc.)
            $table->timestamps(); // created_at y updated_at

            $table->index('user_id');
            $table->index('timestamp');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_records');
    }
};
