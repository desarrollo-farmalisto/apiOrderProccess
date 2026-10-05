<?php

namespace App\Console\Commands;

use App\Models\DireccionesCliente;
use App\Services\GenerateLogs;
use App\Services\Netsuite;
use Illuminate\Console\Command;

class ExtraccionDireccionesClientes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extraccion:direcciones-clientes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracción de la información de las direcciones para los clientes de Farmalisto, desde el ERP Netsuite. Esta tarea se ejecuta cada 1hora en horario laboral.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $error = 0;
        $mensaje_error = '';

        $fecha_inicio_log = Date('Y-m-d\TH:i:s');
        $valores_actualizados = 0;
        $valores_creados = 0;
        $cantidad_busqueda = 0;

        try {

            // $resultado_cantidad = Netsuite::get(2097, 'customsearch_galenica_direccion_cliente', 0, 1, '', 'local');
            $resultado_cantidad = Netsuite::get(2097, 'customsearch_galenica_direccion_cliente', 0, 1);
            \Illuminate\Support\Facades\Log::info('Direcciones sandbox - resultado_cantidad raw: ' . $resultado_cantidad);
            $resultado_cantidad_decoded = json_decode($resultado_cantidad, true);
            $cantidad = is_array($resultado_cantidad_decoded) ? ($resultado_cantidad_decoded['count'] ?? intval($resultado_cantidad)) : intval($resultado_cantidad);
            
            if($cantidad > 0){

                $start = 0;
                $end = 0;

                $cantidad_for = ceil($cantidad/1000)*1000;
                $cantidad_dividir = intval($cantidad_for / 1000);
                for ($i=0; $i < $cantidad_dividir ; $i++) {

                    $start = ($i == 0) ? 0 : $start + 1000;
                    $end = $end + 1000;
                    $start= str_pad($start, mb_strlen($end), "0", STR_PAD_LEFT);

                    // $resultado = Netsuite::get(2170, null, $start, $end, '', 'local');
                    $resultado = Netsuite::get(2170, null, $start, $end);
                    \Illuminate\Support\Facades\Log::info('Direcciones 2170 raw: ' . substr($resultado, 0, 500));
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
                            $array_crear = array();
                            $array_actualizar = array();

                            foreach ($data["response"] as $key => $value) {

                                $cliente_netsuite_id                = $value["id"];
                                $netsuite_id                        = $value["values"]["addressinternalid"];
                                $nombre_sucursal                    = $value["values"]["addresslabel"];
                                $direccion_completa                 = $value["values"]["address"];
                                $direccion_corta                    = $value["values"]["address1"];
                                $barrio                             = $value["values"]["address3"];
                                $pais                               = empty($value["values"]["country"]) ? null : $value["values"]["country"][0]["text"];
                                $departamento                       = $value["values"]["formulatext"];
                                $ciudad                             = $value["values"]["city"];
                                $isinactive                         = $value["values"]["isinactive"];

                                $isinactive = ($isinactive == false) ? 0 : 1;
                                $sql_validar = DireccionesCliente::select('id')->where('netsuite_id', $netsuite_id)->get();

                                if(count($sql_validar) > 0){

                                    $array_actualizar[] = array(
                                        'cliente_netsuite_id' => $cliente_netsuite_id,
                                        'netsuite_id' => $netsuite_id,
                                        'nombre_sucursal' => $nombre_sucursal,
                                        'direccion_completa' => $direccion_completa,
                                        'direccion_corta' => $direccion_corta,
                                        'barrio' => $barrio,
                                        'pais' => $pais,
                                        'departamento' => $departamento,
                                        'ciudad' => $ciudad,
                                        'isinactive' => $isinactive,
                                        'updated_at' => Date('Y-m-d\TH:i:s')
                                    );
                                    $valores_actualizados++;
                                } else {
                                    $array_crear[] = array(
                                        'cliente_netsuite_id' => $cliente_netsuite_id,
                                        'netsuite_id' => $netsuite_id,
                                        'nombre_sucursal' => $nombre_sucursal,
                                        'direccion_completa' => $direccion_completa,
                                        'direccion_corta' => $direccion_corta,
                                        'barrio' => $barrio,
                                        'pais' => $pais,
                                        'departamento' => $departamento,
                                        'ciudad' => $ciudad,
                                        'isinactive' => $isinactive,
                                        'created_at' => Date('Y-m-d\TH:i:s')
                                    );
                                    $valores_creados++;

                                }
                            }

                            if(count($array_crear) > 0){
                                DireccionesCliente::insert($array_crear);
                            }

                            if(count($array_actualizar) > 0){
                                DireccionesCliente::massUpdate(
                                    values: $array_actualizar,
                                    uniqueBy: ['netsuite_id']
                                );
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

        GenerateLogs::generateLogScheduleTask("EXTRACCION DIRECCIONES CLIENTES", "direcciones_clientes", $error, $cantidad_busqueda, $valores_creados, $valores_actualizados, $fecha_inicio_log, $mensaje_error);
    }
}
