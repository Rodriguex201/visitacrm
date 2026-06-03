<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'telefono')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('telefono')->nullable()->after('name');
            });
        }

        if (! Schema::hasColumn('users', 'tipo_usuario')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('tipo_usuario', ['freelance', 'vinculado', 'administracion'])
                    ->default('freelance')
                    ->after('password');
            });
        }
    }

    public function down(): void
    {
        // (Opcional) No hacemos rollback automático para evitar romper login/producción
        // Si quieres rollback, dime y lo habilitamos con checks.
    }
};
