<?php

namespace Database\Seeders;

use App\Models\Transportadoras;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransportadoraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Transportadoras::insert([
            [
                'netsuite_id' => 9,
                'nombre' => "Otro",
                'estado' => 1
            ],
        ]);
    }
}
