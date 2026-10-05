<?php

namespace App\Console\Commands;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Services\GenerateLogs;
use App\Services\Netsuite;
use Illuminate\Console\Command;

class ExtraccionClientes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'extraccion:clientes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracción de la información de los clientes para Galenica, desde el ERP Netsuite. Esta tarea se ejecuta cada 1hora en horario laboral.';

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

            // $resultado_cantidad = Netsuite::get(2097, 'customsearch_ecomerx_clientes_bi_galen', 0, 1, '', 'local');
            $resultado_cantidad = Netsuite::get(2097, 'customsearch_ecomerx_clientes_bi_galen', 0, 1);
            \Log::info('Clientes conteo raw: ' . $resultado_cantidad);
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

                    // $resultado = Netsuite::get(2169, null, $start, $end, '', 'local');
                    $resultado = Netsuite::get(2169, null, $start, $end);
                    \Log::info('Clientes 2169 raw [' . $start . '-' . $end . ']: ' . substr($resultado, 0, 500));
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

                                $netsuite_id                = $value["id"];
                                $tipo                       = $value["values"]["formulatext"];
                                $nombre                     = $value["values"]["custentity_ks_nombre_comercial"];
                                $nombre_compania            = $value["values"]["companyname"];
                                $documento                  = $value["values"]["custentity_ks_documento"];
                                $correo_electronico         = $value["values"]["email"];
                                $telefono                   = $value["values"]["phone"];
                                $cupo                       = empty($value["values"]["creditlimit"]) ? 0 : $value["values"]["creditlimit"];
                                $cupo_feria                 = empty($value["values"]["custentity_nso_cupo_feria"]) ? 0 : $value["custentity_nso_cupo_feria"]["altphone"];
                                $categoria_id               = empty($value["values"]["category"]) ? null : $value["values"]["category"][0]["value"];
                                $categoria                  = empty($value["values"]["category"]) ? null : $value["values"]["category"][0]["text"];
                                $mp_credito                 = $value["values"]["formulatext_2"];
                                $mp_contado                 = $value["values"]["formulatext_1"];
                                $lista_precio_id            = empty($value["values"]["pricelevel"]) ? null : $value["values"]["pricelevel"][0]["value"];
                                $isinactive                 = $value["values"]["isinactive"];

                                $isinactive = ($isinactive == false) ? 0 : 1;

                                if (!empty($categoria_id)) {
                                    Categoria::updateOrCreate(['categoria' => $categoria], ['categoria_netsuite_id' => $categoria_id]);
                                }

                                $sql_validar = Cliente::select('id')->where('netsuite_id', $netsuite_id)->get();
                                if(count($sql_validar) > 0){

                                    $array_actualizar[] = array(
                                        'netsuite_id' => $netsuite_id,
                                        'tipo' => $tipo,
                                        'nombre' => $nombre,
                                        'nombre_compania' => $nombre_compania,
                                        'documento' => $documento,
                                        'correo_electronico' => $correo_electronico,
                                        'telefono' => $telefono,
                                        'cupo' => $cupo,
                                        'cupo_feria' => $cupo_feria,
                                        'categoria_id' => $categoria_id,
                                        'categoria' => $categoria,
                                        'mp_credito' => $mp_credito,
                                        'mp_contado' => $mp_contado,
                                        'lista_precio_id' => $lista_precio_id,
                                        'isinactive' => $isinactive,
                                        'updated_at' => Date('Y-m-d\TH:i:s')
                                    );
                                    $valores_actualizados++;

                                } else {

                                    $array_crear[] = array(
                                        'netsuite_id' => (int)$netsuite_id,
                                        'tipo' => $tipo,
                                        'nombre' => $nombre,
                                        'nombre_compania' => $nombre_compania,
                                        'documento' => $documento,
                                        'correo_electronico' => $correo_electronico,
                                        'telefono' => $telefono,
                                        'cupo' => $cupo,
                                        'cupo_feria' => $cupo_feria,
                                        'categoria_id' => $categoria_id,
                                        'categoria' => $categoria,
                                        'mp_credito' => $mp_credito,
                                        'mp_contado' => $mp_contado,
                                        'lista_precio_id' => $lista_precio_id,
                                        'isinactive' => $isinactive,
                                        'created_at' => Date('Y-m-d\TH:i:s')
                                    );
                                    $valores_creados++;

                                }

                            }

                            if(count($array_crear) > 0){
                                Cliente::insert($array_crear);
                            }

                            if(count($array_actualizar) > 0){
                                Cliente::massUpdate(values: $array_actualizar, uniqueBy: ['netsuite_id']);
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
            $mensaje_error = $e->getMessage();
        }

        GenerateLogs::generateLogScheduleTask("EXTRACCION CLIENTES", "clientes", $error, $cantidad_busqueda, $valores_creados, $valores_actualizados, $fecha_inicio_log, $mensaje_error);
    }
}