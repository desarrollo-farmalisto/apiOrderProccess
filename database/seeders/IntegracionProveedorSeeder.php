<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IntegracionProveedorSeeder extends Seeder
{
    public function run()
    {
        DB::table('integracion_proveedors')->updateOrInsert(
            ['nombre' => 'ABBOTT'],
            ['image' => null, 'active' => 1, 'updated_at' => now(), 'created_at' => now()]
        );
    }
}
