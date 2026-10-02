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
                'nombre' => "Datafono",
                'estado' => 1
            ],
            [
                'netsuite_id' => 4,
                'nombre' => "Efectivo",
                'estado' => 1
            ],
            [
                'netsuite_id' => 8,
                'nombre' => "Mercado Libre",
                'estado' => 1
            ],
            [
                'netsuite_id' => 9,
                'nombre' => "Bold",
                'estado' => 1
            ],
            [
                'netsuite_id' => 10,
                'nombre' => "Ya Te Ayudo",
                'estado' => 1
            ],
            [
                'netsuite_id' => 11,
                'nombre' => "PSE",
                'estado' => 1
            ],
        ]);
    }
}
