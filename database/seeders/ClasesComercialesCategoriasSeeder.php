<?php

namespace Database\Seeders;

use App\Models\ClaseComercialCategoria;
use Illuminate\Database\Seeder;

class ClasesComercialesCategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $matrices = [
            ['clase_comercial_id' => 1, 'categoria_id' => 1],
            ['clase_comercial_id' => 1, 'categoria_id' => 2],
            ['clase_comercial_id' => 2, 'categoria_id' => 3],
            ['clase_comercial_id' => 3, 'categoria_id' => 4],
            ['clase_comercial_id' => 1, 'categoria_id' => 1],
            ['clase_comercial_id' => 1, 'categoria_id' => 5],
            ['clase_comercial_id' => 1, 'categoria_id' => 6],
            ['clase_comercial_id' => 1, 'categoria_id' => 7],
            ['clase_comercial_id' => 4, 'categoria_id' => 8],
            ['clase_comercial_id' => 1, 'categoria_id' => 9],
            ['clase_comercial_id' => 1, 'categoria_id' => 10],
            ['clase_comercial_id' => 1, 'categoria_id' => 11],
            ['clase_comercial_id' => 1, 'categoria_id' => 12],
            ['clase_comercial_id' => 1, 'categoria_id' => 13],
            ['clase_comercial_id' => 5, 'categoria_id' => 14],
            ['clase_comercial_id' => 5, 'categoria_id' => 15],
            ['clase_comercial_id' => 1, 'categoria_id' => 16],
        ];

        foreach ($matrices as $matriz) {
            ClaseComercialCategoria::updateOrInsert(
                [
                    'clase_comercial_id' => $matriz['clase_comercial_id'],
                    'categoria_id'       => $matriz['categoria_id'],
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]
            );
        }
    }
}
