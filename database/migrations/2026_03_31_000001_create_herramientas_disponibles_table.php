<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('herramientas_disponibles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->nullable()->unique();
            $table->string('descripcion')->nullable();
            $table->text('url');
            $table->string('icono')->nullable();
            $table->string('color_fondo', 20)->nullable();
            $table->string('color_texto', 20)->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->boolean('abrir_en_nueva_pestana')->default(true);
            $table->timestamps();

            $table->index(['activo', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('herramientas_disponibles');
    }
};
