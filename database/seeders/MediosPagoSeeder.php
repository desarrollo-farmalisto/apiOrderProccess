<?php

namespace Database\Seeders;

use App\Models\MediosPagos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MediosPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MediosPagos::insert([
            [
                'netsuite_id' => 1,
                'nombre' => "DATAFONO",
                'estado' => 1
            ],
            [
                'netsuite_id' => 4,
                'nombre' => "EFECTIVO",
                'estado' => 1
            ],
            [
                'netsuite_id' => 10,
                'nombre' => "TARJETA DE CREDITO",
                'estado' => 1
            ]
        ]);
    }
}
