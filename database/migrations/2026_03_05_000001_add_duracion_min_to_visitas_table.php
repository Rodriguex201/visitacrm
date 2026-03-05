<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('visitas', 'duracion_min')) {
            Schema::table('visitas', function (Blueprint $table) {
                $table->unsignedSmallInteger('duracion_min')->default(60)->after('fecha_hora');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('visitas', 'duracion_min')) {
            Schema::table('visitas', function (Blueprint $table) {
                $table->dropColumn('duracion_min');
            });
        }
    }
};
