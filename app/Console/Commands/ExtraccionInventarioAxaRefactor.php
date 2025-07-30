<?php

namespace App\Console\Commands;

use App\Models\InventarioAxaTmp;
use App\Services\Netsuite;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExtraccionInventarioAxaRefactor extends Command
{
    protected $signature = 'extraccion:inventarioAxa';
    protected $description = 'Extracicion de la informacion de inventario de axa';

    public function handle()
    {
        try {
            $start = 0;
            $end = 1000;
            $data = [];
            DB::table('inventario_axa_tmp')->truncate();

            do {
                $start = str_pad($start, mb_strlen($end), "0", STR_PAD_LEFT);
                dump($start, $end);

                $resultado = Netsuite::get(Netsuite::resolveId(2517, 2473), null, $start, $end, '', 'local');
                $data = json_decode($resultado, true);

                if (isset($data['error'])) {
                    throw new Exception("Error en estraccion de inventario axa", 1);
                }

                $data = $data['response'];
                $start += 1000;
                $end += 1000;
                $array_insertar = [];
                foreach ($data as $key => $value) {
                    $es_producto_numerado_por_lote      = $value["values"]["islotitem"];
                    $array_insertar[] = [
                        'producto_netsuite_id' =>               $value["values"]["internalid"][0]["text"],
                        'producto' => $value["values"]["itemid"],
                        'ean' =>  $value["values"]["upccode"],
                        'id_ubicacion' => $value["values"]["inventoryLocation.internalid"][0]["text"],
                        'ubicacion' => $value["values"]["inventoryLocation.name"],
                        'disponible' => $value["values"]["locationquantityavailable"],
                        'es_producto_numerado_por_lote' => ($es_producto_numerado_por_lote == false) ? 0 : 1,
                        'created_at' => Date('Y-m-d\TH:i:s')
                    ];
                }
                $count = count($array_insertar);
                if ($count > 0) {
                    InventarioAxaTmp::insert($array_insertar);
                    dump('Insert: ' . $count);
                }
            } while ($data);

            DB::table('inventario_axas')->truncate();
            DB::statement("
            INSERT INTO inventario_axas (
                producto_netsuite_id,
                producto,
                ean,
                id_ubicacion,
                ubicacion,
                disponible,
                es_producto_numerado_por_lote,
                created_at,
                updated_at
            )
            SELECT 
                producto_netsuite_id,
                producto,
                ean,
                id_ubicacion,
                ubicacion,
                disponible,
                es_producto_numerado_por_lote,
                created_at,
                updated_at
            FROM inventario_axa_tmp
        ");
        } catch (\Throwable $e) {
            dump($e->getMessage());
        }
    }
}
