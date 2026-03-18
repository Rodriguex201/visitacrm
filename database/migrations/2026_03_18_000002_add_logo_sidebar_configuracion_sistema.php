<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('configuraciones_sistema')->updateOrInsert(
            ['clave' => 'logo_sidebar'],
            [
                'valor' => '',
                'descripcion' => 'Ruta del logo principal mostrado en el sidebar',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('configuraciones_sistema')->where('clave', 'logo_sidebar')->delete();
    }
};
