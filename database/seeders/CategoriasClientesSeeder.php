<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriasClientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categorias = [
            'Autoservicios',
            'Consumo Subdistribuidor',
            'Empleado-G',
            'Empresarial-G',
            'Farmalisto-Pagina Web',
            'Farmalisto-Call Center',
            'Farmalisto-WhatsApp',
            'Farmalisto Cafam Desarrollo Web',
            'Galenica Web',
            'No Definido',
            'Operador Logistico',
            'Paciente-G',
            'Pagina Web Galenica',
            'Unixa | Market Place',
            'Unixa | Maxpharma',
            'Unixa | Consumidor Final',
        ];

        foreach ($categorias as $categoria) {
            Categoria::updateOrInsert(['categoria' => $categoria, 'created_at' => now(), 'updated_at' => now()]);
        }
    }
}
