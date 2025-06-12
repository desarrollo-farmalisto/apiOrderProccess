<?php

namespace App\Console\Commands;

use App\Models\InventarioAxa;
use App\Services\GenerateLogs;
use App\Services\Netsuite;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExtraccionInventarioAxa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extraccion:inventarioAxa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracicion de la informacion de inventario de axa';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $error = 0;
        $mensaje_error = '';

        //establecer variables
        $fecha_inicio_log = Date('Y-m-d\TH:i:s');
        $valores_actualizados = 0;
        $valores_creados = 0;
        $cantidad_busqueda = 0;

        try {

            //primera consulta a netsuite, consultar cantidades
            $resultado_cantidad = Netsuite::get(2097, 'customsearch_ecomerx_invent_galenica_t_2', 0, 1, '', 'local');
            //dd($resultado_cantidad);
            if(intval($resultado_cantidad) > 0){

                //vaciar tabla
                DB::table('inventario_axas')->truncate();

                $start = 0;
                $end = 0;

                $cantidad_for = ceil($resultado_cantidad/1000)*1000;
                $cantidad_dividir = intval($cantidad_for / 1000);
                for ($i=0; $i < $cantidad_dividir ; $i++) {

                    $start = ($i == 0) ? 0 : $start + 1000;
                    $end = $end + 1000;
                    $start= str_pad($start, mb_strlen($end), "0", STR_PAD_LEFT);

                    $resultado = Netsuite::get(Netsuite::resolveId(0,2473), null, $start, $end, '', 'local');
                    $data = json_decode($resultado, true);
                    //dd($data);
                    if(isset($data['error'])){
                        $error = 1;
                        $mensaje_error = json_encode($data);
                    } else {
                        if($data["message"] == 'error'){
                            $error = 1;
                            $mensaje_error = $data["response"];
                        } else {

                            $cantidad_busqueda = $cantidad_busqueda + count($data["response"]);
                            $array_insertar = array();

                            //recorrer datos
                            foreach ($data["response"] as $key => $value) {
        
                                $producto_netsuite_id               = $value["values"]["internalid"][0]["text"];
                                $producto                           = $value["values"]["itemid"];
                                $ean                                = $value["values"]["upccode"];
                                $id_ubicacion                       = $value["values"]["inventoryLocation.internalid"][0]["text"];
                                $ubicacion                          = $value["values"]["inventoryLocation.name"];
                                $disponible                         = $value["values"]["locationquantityavailable"];
                                $es_producto_numerado_por_lote      = $value["values"]["islotitem"];

                                $es_lote = ($es_producto_numerado_por_lote == false) ? 0 : 1;

                                $array_insertar[] = array(
                                    'producto_netsuite_id' => $producto_netsuite_id,
                                    'producto' => $producto,
                                    'ean' => $ean,
                                    'id_ubicacion' => $id_ubicacion,
                                    'ubicacion' => $ubicacion,
                                    'disponible' => $disponible,
                                    'es_producto_numerado_por_lote' => $es_lote,
                                    'created_at' => Date('Y-m-d\TH:i:s')
                                );

                            }

                            //insertar en la tabla
                            InventarioAxa::insert($array_insertar);

                        }
                    }
                }

            } else {
                $error = 1;
                $mensaje_error = 'Se presento un error al consultar la busqueda! No se pudo extraer nada.';
            }

        } catch (\Throwable $e) {
            $error = 1;
            $mensaje_error = $e;
        }

        //por ultimo, generamos log
        GenerateLogs::generateLogScheduleTask("EXTRACCION INVENTARIO AXA", "inventario axa", $error, $cantidad_busqueda, $valores_creados, $valores_actualizados, $fecha_inicio_log, $mensaje_error);
    }
}
