<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\ClaseComercial;
use App\Models\ClaseComercialCategoria;
use Illuminate\Database\Seeder;

class ClasesComercialesCategoriasSeeder extends Seeder
{
    public function run()
    {
        $matriz = [
            '- None -'                        => '11010101 Pacientes Farmalisto',
            'AUTOSERVICIOS'                   => '11010101 Pacientes Farmalisto',
            'CONSUMO SUBDISTRIBUIDOR'         => '11010101 Pacientes Farmalisto',
            'EMPLEADO - G'                    => '11020103 Empresas Farmalisto',
            'EMPRESARIAL - G'                 => '11030104 Medicos Farmalisto IPS',
            'Farmalisto - Página Web'         => '11010101 Pacientes Farmalisto',
            'Farmalisto -  Call Center'       => '11010101 Pacientes Farmalisto',
            'Farmalisto -  Whatsapp'          => '11010101 Pacientes Farmalisto',
            'Farmalisto CAFAM Desarrollo Web' => '11050506 Servicios Admin Pag. Web Cafam',
            'GALENICA WEB'                    => '11010101 Pacientes Farmalisto',
            'NO DEFINIDO'                     => '11010101 Pacientes Farmalisto',
            'OPERADOR LOGISTICO'              => '11010101 Pacientes Farmalisto',
            'PACIENTE - G'                    => '11010101 Pacientes Farmalisto',
            'PAGINA WEB GALENICA'             => '11010101 Pacientes Farmalisto',
            'UNIXA | MARKET PLACE'            => '11040305 Market Place ML Farmalisto',
            'UNIXA | MAXPHARMA'               => '11040305 Market Place ML Farmalisto',
            'UNIXA |CONSUMIDOR FINAL'         => '11010101 Pacientes Farmalisto',
        ];

        ClaseComercialCategoria::truncate();

        foreach ($matriz as $nombre_categoria => $nombre_clase) {
            $categoria = Categoria::where('categoria', $nombre_categoria)->first();
            $clase     = ClaseComercial::where('clase_comercial', $nombre_clase)->first();

            if (!$categoria || !$clase) continue;

            ClaseComercialCategoria::updateOrInsert(
                ['categoria_id' => $categoria->id, 'clase_comercial_id' => $clase->id],
                ['status' => '1', 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
