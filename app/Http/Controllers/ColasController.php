<?php

namespace App\Http\Controllers;

use App\Models\Jobs;
use Illuminate\Http\Request;

class ColasController extends Controller
{

    public function getEnProceso()
    {
        $datos = array();

        $informacion = Jobs::all();
        if(count($informacion) > 0){
            foreach ($informacion as $key => $value) {

                //convertiendo para extraer informacion
                $info_pedido = json_decode($value->payload)->data->command;
                $cm = unserialize($info_pedido);

                $datos[] = array(
                    "pedido_id" => $cm->pedido_id,
                    "proceso_id" => $value->id,
                    "prioridad" => $value->queue,
                    "estado" => $value->attempts,
                    "estado_texto" => ($value->attempts == 1) ? 'EN PROCESO' : 'EN ESPERA'
                );

            }
        }

        return response()->json(array(
            "data" => $datos
        ));
    }

}
