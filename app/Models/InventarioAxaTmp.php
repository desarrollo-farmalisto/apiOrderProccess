<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioAxaTmp extends Model
{
    use HasFactory;

    protected $table='inventario_axa_tmp';
    protected $fillable = [
        'producto_netsuite_id',
        'producto',
        'ean',
        'id_ubicacion',
        'ubicacion',
        'disponible',
        'es_producto_numerado_por_lote'
    ];
}
