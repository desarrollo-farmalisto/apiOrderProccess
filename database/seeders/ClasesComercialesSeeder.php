<?php

namespace Database\Seeders;

use App\Models\ClaseComercial;
use Illuminate\Database\Seeder;

class ClasesComercialesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clase = [
            '11010101 Pacientes Farmalisto',
            '11020103 Empresas Farmalisto',
            '11030104 Medicos Farmalisto IPS',
            '11050506 Servicios Admin Pag. Web Cafam',
            '11040305 Market Place ML Farmalisto'
        ];

        foreach ($clase as $clase) {
            ClaseComercial::updateOrInsert(['clase_comercial' => $clase, 'created_at' => now(), 'updated_at' => now()]);
        }
    }
}
