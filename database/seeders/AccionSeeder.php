<?php

namespace Database\Seeders;

use App\Models\Accion;
use Illuminate\Database\Seeder;

class AccionSeeder extends Seeder
{
    public function run(): void
    {
        $acciones = [
            ['nombre' => 'Llamada', 'icono' => 'phone', 'orden' => 1, 'activo' => 1],
            ['nombre' => 'Redes', 'icono' => 'share-2', 'orden' => 2, 'activo' => 1],
            ['nombre' => 'Video conf', 'icono' => 'video', 'orden' => 3, 'activo' => 1],
            ['nombre' => 'Visita ext', 'icono' => 'map-pin', 'orden' => 4, 'activo' => 1],
            ['nombre' => 'Visita emp', 'icono' => 'building-2', 'orden' => 5, 'activo' => 1],
            ['nombre' => 'Solic baja', 'icono' => 'user-x', 'orden' => 6, 'activo' => 1],
        ];

        foreach ($acciones as $accion) {
            Accion::query()->updateOrCreate(
                ['nombre' => $accion['nombre']],
                [
                    'descr' => null,
                    'icono' => $accion['icono'],
                    'color' => null,
                    'orden' => $accion['orden'],
                    'activo' => $accion['activo'],
                ]
            );
        }
    }
}
