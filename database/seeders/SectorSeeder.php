<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sectores = [
            'Restaurantes',
            'Fruver – Carnicería',
            'Minimercados',
            'Papelerías',
            'Parqueaderos',
            'Divisas',
            'Droguerías',
            'Distribuidoras',
            'Tienda de tecnología',
            'Ferreterías',
            'Tienda de ropa y calzado',
            'Taller de servicio técnico',
            'Servicios',
        ];

        foreach ($sectores as $nombre) {
            Sector::query()->updateOrCreate(['nombre' => $nombre]);
        }
    }
}
