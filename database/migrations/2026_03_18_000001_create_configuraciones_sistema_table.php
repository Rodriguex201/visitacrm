<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuraciones_sistema', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->text('valor');
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        DB::table('configuraciones_sistema')->updateOrInsert(
            ['clave' => 'clave_eliminar_empresa'],
            [
                'valor' => 'Admin2026',
                'descripcion' => 'Clave usada para confirmar la eliminación de empresas',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuraciones_sistema');
    }
};
