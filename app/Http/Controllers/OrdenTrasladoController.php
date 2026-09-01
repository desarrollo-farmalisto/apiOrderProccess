<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\DetallesOrdenesTraslado;
use App\Models\InventarioAxa;
use App\Models\LogsNetsuites;
use App\Models\OrdenesTraslado;
use App\Services\Netsuite;
use Illuminate\Support\Facades\DB;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrdenTrasladoController extends Controller
{
    public function index($type = null)
    {

        try {
            if (is_null($type)) {
                return response()->json([
                    'error' => 'El parámetro tipo_orden es obligatorio.',
                    'code' => 400
                ]);
            }
            $orders = OrdenesTraslado::where('tipo_orden', $type)->with('ubicacion', 'ubicacionPara', 'ubicacionAxa')->get();

            return response()->json([
                'data' => $orders
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'data' => $th->getMessage(),
                'type' => $type,
            ]);
        }
    }


    public function guardar(Request $request)
    {
        try {

            $productos = $request->productos;
            if (is_array($productos) && count($productos) > 0) {
                $insertar = [
                    'orden_id' => $request->orden_id,
                    'tipo_orden' => $request->tipo_orden,
                    'canal' => $request->canal,
                    'desde_compania' => $request->desde_compania,
                    'para_compania' => $request->para_compania,
                    'desde_ubicacion' => $request->desde_ubicacion,
                    'para_ubicacion' => $request->para_ubicacion,
                    'empleado' => 14161,
                    'comentarios' => $request->comentarios,
                    'centro_costo' => 1,
                    'estado' => 1
                ];

                if ($result = OrdenesTraslado::create($insertar)) {
                    $id_orden = $result->id;
                    foreach ($productos as $producto) {
                        $detalle = [
                            'orden_traslado_id' => $id_orden,
                            'item_id' => $producto['netsuite_id'],
                            'ean' => $producto['ean'],
                            'titulo' => $producto['titulo'],
                            'cantidad_solicitado' => $producto['cantidad_solicitado'],
                        ];
                        DetallesOrdenesTraslado::create($detalle);
                    }

                    $enviar_resultado = $this->enviarNetsuite($id_orden);
                    
                    Log::info('netsuite');
                    Log::info($enviar_resultado);

                    if ($enviar_resultado['estado']) {
                        return response()->json([
                            'error' => false,
                            'id_orden' => $id_orden,
                            'mensaje' => $enviar_resultado['mensaje']
                        ]);
                    } else {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error al crear el traslado en Netsuite.'
                        ]);
                    }
                } else {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Error al crear la orden de traslado.'
                    ]);
                }
            } else {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'No hay productos para agregar a la orden de traslado.'
                ]);
            }
        } catch (\Throwable $th) {
            Log::error("error ordenes de traslado: " . $th->getMessage());
            return response()->json([
                'error' => true,
                'mensaje' => $th->getMessage(),
            ]);
        }
    }

    public function enviarNetsuite($id)
    {

        $traslado = OrdenesTraslado::find($id);

        //items
        $items = array();
        if (count($traslado->detalles) > 0) {
            foreach ($traslado->detalles as $key => $value) {
                $items[] = array(
                    "id" => $value->item_id,
                    "cantidad" => $value->cantidad_solicitado
                );
            }
        }

        // Vendedor 
        $socio = null;
        if(auth()->user()){
            $socio = auth()->user()->name;
        }

        //crear json
        $array_json = array(
            "tipo_orden" => $traslado->tipo_orden,
            "desde_compania" => ($traslado->desde_compania == "ECOMERX/GALENICA") ? 8 : 6,
            "para_compania" => 8,
            "desde_ubicacion" => $traslado->desde_ubicacion,
            "para_ubicacion" => $traslado->para_ubicacion,
            "empleado" => $traslado->empleado,
            "comentarios" => $traslado->comentarios . " " . $socio,
            "centro_costo" => $traslado->centro_costo,
            "estado" => "A",
            "items" => $items
        );

        $json = json_encode($array_json);
        //$resultado_service = Netsuite::post(2140, $json, 'local');
        $resultado_service = Netsuite::post(2159, $json);
        $decodificado = json_decode($resultado_service, true);
        if ($decodificado["message"] == 'success') {

            $id_netsuite = $decodificado["response"]["id"];
            $num_orden = null;
            $estado_ns = null;
            $items = null;

            //consultar informacion del traslado
            //$resultado_consulta = Netsuite::get(2141, $id_netsuite, 0, 1, '', 'local');
            $resultado_consulta = Netsuite::get(2160, $id_netsuite, 0, 1);
            $decodificado_consulta = json_decode($resultado_consulta, true);
            if ($decodificado_consulta["message"] == 'success') {
                $num_orden = $decodificado_consulta["response"]["num"];
                $estado_ns = $decodificado_consulta["response"]["estado"];
                $items = $decodificado_consulta["response"]["items"];
            }

            //validando los items
            if ($items != null) {
                if (count($items) > 0) {
                    foreach ($items as $key => $value) {
                        $act_items = array(
                            'cantidad_recibido' => $value["recibido"]
                        );
                        DetallesOrdenesTraslado::where("orden_traslado_id", $traslado->id)
                            ->where('item_id', $value["id"])->update($act_items);
                    }
                }
            }

            $actualizar = array(
                'netsuite_id' => $id_netsuite,
                'numero_orden_traslado' => $num_orden,
                'fecha_netsuite' => date('Y-m-d h:i:s a'),
                'estado' => 2,
                'estado_netsuite' => $estado_ns
            );
            OrdenesTraslado::findOrFail($traslado->id)->update($actualizar);

            //generar log
            $mensaje = 'CREACION ORDEN TRASLADO #' . $traslado->id . ', Mensaje: Orden de Traslado creada correctamente, generado con el ID NETSUITE #' . $id_netsuite . ' y NUMERO ORDEN #' . $num_orden;
            $agg_info = array(
                'tipo' => 'ORDEN TRASLADO',
                'respuesta_id' => $traslado->id,
                'fecha' => date('Y-m-d'),
                'error' => 'Si',
                'mensaje' => $mensaje
            );
            LogsNetsuites::create($agg_info);
            $response = array(
                'estado' => true,
                'mensaje' => 'Se creo la orden netsuite con exito',
            );
            return $response;
        } else {

            $actualizar = array(
                'estado' => 3
            );
            OrdenesTraslado::findOrFail($traslado->id)->update($actualizar);

            //generar log
            $mensaje = 'ERROR CREACION ORDEN TRASLADO #' . $traslado->id . ', Mensaje: ' . $decodificado["response"];
            $agg_info = array(
                'tipo' => 'ORDEN TRASLADO',
                'respuesta_id' => $traslado->id,
                'fecha' => date('Y-m-d'),
                'error' => 'Si',
                'mensaje' => $mensaje
            );
            LogsNetsuites::create($agg_info);

            $response = array(
                'estado' => false,
                'mensaje' => 'Error al crear orden de traslado',
            );
            return $response;
        }
    }

    public function productos($id)
    {
        $datos = DB::select("SELECT
                    MIN(c.id) AS id, c.producto_netsuite_id,
                    MAX(c.producto) AS producto,
                    MAX(c.ean) AS ean,
                    MAX(c.linea) AS linea,
                    MAX(c.embalaje) AS embalaje,
                    MAX(c.precio_venta) AS precio_venta,
                    MAX(c.iva) AS iva,
                    MAX(c.precion_con_iva) AS precion_con_iva,
                    MAX(c.tipo) AS tipo, MAX(c.es_lote) AS es_lote,
                    MAX(i.disponible) AS disponible
                FROM catalogos AS c
                LEFT JOIN inventarios AS i ON c.producto_netsuite_id = i.producto_netsuite_id
                WHERE c.isinactive = 0
                AND c.precion_con_iva > 0
                AND i.id_ubicacion = $id
                AND c.tipo LIKE '%nventario%'
                GROUP BY c.producto_netsuite_id;
        ");

        $retorno = collect($datos)->map(function ($value) {
            return [
                'id' => $value->id,
                'product_id' => $value->producto_netsuite_id,
                'producto' => $value->producto,
                'ean' => $value->ean,
                'linea' => $value->linea,
                'embalaje' => $value->embalaje,
                'tipo' => $value->tipo,
                'es_lote' => $value->es_lote,
                'inventario' => $value->disponible ?? 0,
                'precio_unitario' => "$" . number_format($value->precio_venta, 0, ",", "."),
                'precio_unitario_calculable' => $value->precio_venta,
                'iva' => $value->iva,
                'precio' => "$" . number_format($value->precion_con_iva, 0, ",", "."),
                'precio_calculable' => $value->precion_con_iva
            ];
        });

        return response()->json([
            "data" => $retorno
        ]);
    }

    public function ubicacion()
    {
        $ubicaciones = Ubicacion::select('netsuite_id AS value', 'nombre AS label', 'ciudad')
            ->where('isinactive', 0)
            ->orderBy('id')
            ->get()
            ->map(function ($ubicacion) {
                $ubicacion->label = explode(' : ', $ubicacion->label)[1] ?? '';
                return $ubicacion;
            });

        return response()->json([
            "ubicaciones" => $ubicaciones
        ]);
    }

    public function ubicacionAxa()
    {
        $ubicacionAxa = InventarioAxa::select('ubicacion', 'id_ubicacion')
            ->distinct()
            ->get();

        return response()->json([
            "ubicacionesaxa" => $ubicacionAxa
        ]);
    }

    public function Productosaxa($id)
    {
        $productoAxa =  DB::select("SELECT
                                    DISTINCT i.producto_netsuite_id ,
                                    i.producto ,
                                    i.ean,
                                    i.disponible

                                 FROM  inventario_axas i
                                    WHERE i.id_ubicacion =  $id;");

        $retornar = collect($productoAxa)->map(function ($value) {
            return [
                'id' => $value->producto_netsuite_id,
                'product_id' => $value->producto_netsuite_id,
                'producto' => $value->producto,
                'ean' => $value->ean,
                // 'linea' => $value->linea,
                // //'embalaje' => $value->embalaje,
                // 'tipo' => $value->tipo,
                // 'es_lote' => $value->es_lote,
                'inventario' => $value->disponible ?? 0,
                // 'precio_unitario' => "$" . number_format($value->precio_venta, 0, ",", "."),
                // 'precio_unitario_calculable' => $value->precio_venta,
                //  'iva' => $value->iva,
                //  'precio' => "$" . number_format($value->precion_con_iva, 0, ",", "."),
                'precio_calculable' => 0
            ];
        });

        return response()->json([
            'data' => $retornar
        ]);
    }
    public function ubicacionesConInventario($producto_id, $tipo)
    {
        try {
            if ($tipo == 'BODEGA') {
                //dd($producto_id);
                $ubicaciones = DB::select("
                SELECT DISTINCT u.netsuite_id AS value,
                u.nombre AS label, i.disponible
                FROM ubicacions u
                JOIN inventarios i ON i.id_ubicacion = u.netsuite_id
                WHERE i.producto_netsuite_id = ?
                AND i.disponible > 0
                AND u.isinactive = 0
                ", [$producto_id]);
                ///dd($ubicaciones);


            } else {
                $ubicaciones = DB::select("
                SELECT DISTINCT i.id_ubicacion AS value,
                i.ubicacion AS label, i.disponible
                FROM inventario_axas i
                WHERE i.producto_netsuite_id = ?
                AND i.disponible > 0
                ", [$producto_id]);
                ///dd($ubicaciones);

            }
            return response()->json([
                "ubicaciones" => $ubicaciones,
                "producto_id" => $producto_id
            ]);
            // $formattedUbicaciones = collect($ubicaciones)->map(function ($ubicacion) {
            //     return [
            //         'label' => explode(' : ', $ubicacion->label)[1] ?? '',
            //         'value' => $ubicacion->value,
            //         'inventario' => $ubicacion->inventario,
            //         'faltante' => $ubicacion->faltante
            //     ];
            // });

            // return response()->json([
            //     "ubicaciones" => $formattedUbicaciones,
            //     "producto_id" => $producto_id
            // ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'mensaje' => $th->getMessage(),
            ]);
        }
    }

    public function guardarPedido(Request $request)
    {
        try {
            $productos = $request->productos;
            if (is_array($productos) && count($productos) > 0) {

                $productos_por_ubicacion = [];


                foreach ($productos as $producto) {
                    $ubicacion = $producto['desde_ubicacion'] ?? $request->desde_ubicacion;
                    if (!isset($productos_por_ubicacion[$ubicacion])) {
                        $productos_por_ubicacion[$ubicacion] = [];
                    }
                    $productos_por_ubicacion[$ubicacion][] = $producto;
                }

                $resultados = [];
                $cantidad = count($productos_por_ubicacion);



                foreach ($productos_por_ubicacion as $ubicacion => $productos_ubicacion) {
                    /* return response()->json([
                        'error' => true,
                        'mensaje' => $ubicacion,
                    ]); */
                    $insertar = [
                        'tipo_orden' => $request->tipo_orden,
                        'canal' => $request->canal,
                        'desde_compania' => $request->desde_compania,
                        'para_compania' => $request->para_compania,
                        'desde_ubicacion' => $ubicacion,
                        'para_ubicacion' => $request->para_ubicacion,
                        'empleado' => 14161,
                        'comentarios' => $request->comentarios,
                        'centro_costo' => 1,
                        'estado' => 1
                    ];

                    if ($result = OrdenesTraslado::create($insertar)) {
                        $id_orden = $result->id;

                        foreach ($productos_ubicacion as $producto) {
                            /* return response()->json([
                                'error' => true,
                                'mensaje' => $producto,
                            ]); */
                            $detalle = [
                                'orden_traslado_id' => $id_orden,
                                'item_id' => $producto['producto_id'],
                                'ean' => $producto['ean'],
                                'titulo' => $producto['producto'],
                                'cantidad_solicitado' => $producto['cantidad'],
                            ];
                            DetallesOrdenesTraslado::create($detalle);
                        }

                        $enviar_resultado = $this->enviarNetsuite($id_orden);
                        $resultados[] = [
                            'orden_id' => $id_orden,
                            'ubicacion' => $ubicacion,
                            'estado' => $enviar_resultado['estado'],
                            'mensaje' => $enviar_resultado['mensaje']
                        ];
                    } else {
                        return response()->json([
                            'error' => true,
                            'mensaje' => 'Error al crear la orden de traslado para la ubicación ' . $ubicacion
                        ]);
                    }
                }

                // Verificar si todas las órdenes se crearon correctamente
                $errores = array_filter($resultados, function ($r) {
                    return !$r['estado'];
                });

                if (empty($errores)) {
                    return response()->json([
                        'error' => false,
                        'mensaje' => 'Se crearon ' . count($resultados) . ' órdenes de traslado correctamente.',
                        'resultados' => $resultados
                    ]);
                } else {
                    return response()->json([
                        'error' => true,
                        'mensaje' => 'Algunas órdenes de traslado no pudieron ser creadas en Netsuite.',
                        'resultados' => $resultados
                    ]);
                }
            } else {
                return response()->json([
                    'error' => true,
                    'mensaje' => 'No hay productos para agregar a la orden de traslado.'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'mensaje' => $th->getMessage(),
                'linea' => $th->getLine(),
            ]);
        }
    }
}
