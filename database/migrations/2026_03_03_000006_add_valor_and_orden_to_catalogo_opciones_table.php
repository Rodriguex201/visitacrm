<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('catalogo_opciones')) {
            return;
        }

        Schema::table('catalogo_opciones', function (Blueprint $table) {
            if (! Schema::hasColumn('catalogo_opciones', 'valor')) {
                $table->decimal('valor', 12, 2)->nullable()->after('nombre');
            }

            if (! Schema::hasColumn('catalogo_opciones', 'orden')) {
                $table->integer('orden')->default(0)->after('valor');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('catalogo_opciones')) {
            return;
        }

        Schema::table('catalogo_opciones', function (Blueprint $table) {
            if (Schema::hasColumn('catalogo_opciones', 'orden')) {
                $table->dropColumn('orden');
            }

            if (Schema::hasColumn('catalogo_opciones', 'valor')) {
                $table->dropColumn('valor');
            }
        });
    }
};
