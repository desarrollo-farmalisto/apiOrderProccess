<?php

namespace App\Http\Controllers;

use App\Models\Jobs;
use App\Models\Pedidos;
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

    public function getPendientes($fecha)
    {
        $datos = array();
        $pedidos_id_jobs = array();

        $informacion = Jobs::all();
        if(count($informacion) > 0){
            foreach ($informacion as $key => $value_job) {

                //convertiendo para extraer informacion
                $info_pedido = json_decode($value_job->payload)->data->command;
                $cm = unserialize($info_pedido);

                $job_pedido = intval($cm->pedido_id);
                $pedidos_id_jobs[] = $job_pedido;

            }
        }

        //consultando
        $pedidos = Pedidos::where('fecha', $fecha)->where('estado', 3)->whereNotIn('id', $pedidos_id_jobs)->get();
        if(count($pedidos) > 0){
            foreach ($pedidos as $key => $value) {

                $datos[] = array(
                    "pedido_id" => $value->id,
                    "address" => ($value->direccion != null) ? $value->direccion->nombre_sucursal : '',
                    "total_items" => $value->cantidad_items,
                    "cantidad_solicitado" => $value->cantidad_items_solicitado,
                    "total" => ($value->total > 0.0) ? '$'.number_format($value->total, 2) : '0',
                    "estado_texto" => 'PENDIENTE VOLVER A ENVIAR'
                );
            }
        }

        return response()->json(array(
            'data' => $datos,
        ));
    }
}
