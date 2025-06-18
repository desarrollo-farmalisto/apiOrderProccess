<?php

namespace App\Console\Commands;

use App\Models\OrdenesTraslado;
use Illuminate\Console\Command;
use App\Services\Netsuite;
use Illuminate\Support\Facades\DB;

class UpdateStatusOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Actualizacion_estado_ordenes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar estado de las ordenes de traslado';

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
            $resultado_cantidad = Netsuite::get(2097, 'customsearch7007', 0, 1);
            if(intval($resultado_cantidad) > 0){

                //vaciar tabla
                DB::table('inventario_netsuites')->truncate();

                $start = 0;
                $end = 0;

                $cantidad_for = ceil($resultado_cantidad/1000)*1000;
                $cantidad_dividir = intval($cantidad_for / 1000);
                for ($i=0; $i < $cantidad_dividir ; $i++) {

                    $start = ($i == 0) ? 0 : $start + 1000;
                    $end = $end + 1000;
                    $start= str_pad($start, mb_strlen($end), "0", STR_PAD_LEFT);

                    $resultado = Netsuite::get(Netsuite::resolveId(0,2527), null, $start, $end);
                    //dd('resultado', $resultado);
                    $data = json_decode($resultado, true);
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

                                $numero_documento           = $value["values"]["tranid"];
                                $estado                     = $value["values"]["statusref"][0]["text"];

                                if($estado == 'Pending Approval')
                                {
                                    $estado = 'A';
                                }else if($estado == 'Pending Fulfillment')
                                {
                                    $estado = 'B';
                                }else if($estado == 'Pending Receipt')
                                {
                                    $estado = 'C';
                                }else if($estado == 'Received')
                                {
                                    $estado = 'D';
                                }
                                else if($estado == "Pending Receipt/Partially Fulfilled")
                                {
                                    $estado = 'E';
                                }

                                $validar = OrdenesTraslado::where('numero_orden_traslado', $numero_documento)->first();
                                if($validar)
                                {
                                    $validar->update([
                                        'estado_netsuite' => $estado,
                                    ]);
                                }



                            }

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

    }
}
