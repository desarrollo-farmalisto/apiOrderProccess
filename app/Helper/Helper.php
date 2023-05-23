<?php

namespace App\Helper;

class Helper {

    public static function arreglarFecha($vfecha)
    {
        $fch=explode("/",$vfecha);
        $tfecha=$fch[2]."/".$fch[1]."/".$fch[0];
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

}
