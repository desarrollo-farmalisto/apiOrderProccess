<?php

namespace App\Helper;

class Helper
{

    public static function arreglarFecha($vfecha)
    {
        $fch = explode("/", $vfecha);
        $tfecha = $fch[2] . "/" . $fch[1] . "/" . $fch[0];
        return $tfecha;
    }

    public static function checkFechaRango($date_start, $date_end, $date_now)
    {
        $date_start = strtotime($date_start);
        $date_end = strtotime($date_end);
        $date_now = strtotime($date_now);
        if (($date_now >= $date_start) && ($date_now <= $date_end))
            return 0;
        return 1;
    }

    public static function getMetodoPago($data)
    {
        $array = self::getDataMetodoPago();
        $retorno = $array[$data];
        return $retorno;
    }

    public static function getDataMetodoPago()
    {
        $data = array(
            1 => "Contado",
            2 => "Credito"
        );
        return $data;
    }

    public static function getEstadoPedido($data)
    {
        $array = self::getDataEstadoPedido();
        $retorno = $array[$data];
        return $retorno;
    }

    public static function getDataEstadoPedido()
    {
        $data = array(
            1 => "Pendiente",
            2 => "Cancelado",
            3 => "En Proceso",
            4 => "Completado",
            5 => "Error NS"
        );
        return $data;
    }

    public static function getEstadoOrdenes($estado = null)
    {
        $data = array(
            1 => "Aprobación pendiente",
            2 => "Ejecución de la orden pendiente",
            3 => "Pendiente de recibir",
            4 => "Mercancía recibida",
        );

        // Si no se proporciona estado o es null, devuelve el array completo
        if ($estado === null) {
            return $data;
        }

        // Si el estado existe en el array, devuelve ese estado
        if (isset($data[$estado])) {
            return $data[$estado];
        }

        // Si no existe, devuelve "A" por defecto
        return "A";
    }

    public static function getComandoExtraccion($data)
    {
        $array = self::getDataComandosExtracciones();
        $retorno = $array[$data];
        return $retorno;
    }

    public static function getDataComandosExtracciones()
    {
        $data = array(
            1 => "extraccion:clientes",
            2 => "extraccion:direcciones-clientes",
            3 => "extraccion:listas-precios",
            4 => "extraccion:ubicaciones",
            5 => "extraccion:inventario",
            6 => "extraccion:inventario-kits",
            7 => "extraccion:catalogo",
            8 => "extraccion:kits",
            9 => "extraccion:promociones",
            10 => "extraccion:usuarios",
            11 => "extraccion:medicos",
            12 => "extraccion:detalles-inventarios"
        );
        return $data;
    }
}
