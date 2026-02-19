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
        Schema::table('empresas', function (Blueprint $table) {
            if (!Schema::hasColumn('empresas', 'sector_id')) {
                $table->foreignId('sector_id')
                    ->nullable()
                    ->after('direccion')
                    ->constrained('sectores')
                    ->nullOnDelete();
            }
        });

        if (Schema::hasColumn('empresas', 'sector')) {
            Schema::table('empresas', function (Blueprint $table) {
                $table->dropColumn('sector');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('empresas', 'sector_id')) {
            Schema::table('empresas', function (Blueprint $table) {
                $table->dropConstrainedForeignId('sector_id');
            });
        }

        if (!Schema::hasColumn('empresas', 'sector')) {
            Schema::table('empresas', function (Blueprint $table) {
                $table->string('sector')->nullable()->after('direccion');
            });
        }
    }
};
