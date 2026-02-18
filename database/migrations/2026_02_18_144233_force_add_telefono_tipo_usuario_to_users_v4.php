<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $db = DB::connection()->getDatabaseName();

        $hasTelefono = DB::selectOne(
            "SELECT COUNT(*) AS c
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'users' AND COLUMN_NAME = 'telefono'",
            [$db]
        )->c;

        if ((int)$hasTelefono === 0) {
            DB::statement("ALTER TABLE `users` ADD COLUMN `telefono` VARCHAR(255) NULL AFTER `name`");
        }

        $hasTipo = DB::selectOne(
            "SELECT COUNT(*) AS c
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'users' AND COLUMN_NAME = 'tipo_usuario'",
            [$db]
        )->c;

        if ((int)$hasTipo === 0) {
            DB::statement(
                "ALTER TABLE `users`
                 ADD COLUMN `tipo_usuario` ENUM('freelance','vinculado','administracion')
                 NOT NULL DEFAULT 'freelance'
                 AFTER `password`"
            );
        }
    }

    public function down(): void
    {
        // (Opcional) No hacemos rollback automático para evitar romper login/producción
        // Si quieres rollback, dime y lo habilitamos con checks.
    }
};
