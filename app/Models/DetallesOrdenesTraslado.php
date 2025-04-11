<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallesOrdenesTraslado extends Model
{
    use HasFactory;

    protected $fillable = [
        'orden_traslado_id',
        'item_id',
        'ean',
        'titulo',
        'cantidad_solicitado',
        'cantidad recibido'
    ];

    public function ordenes(){
        return $this->belongsTo(OrdenesTraslado::class, 'orden_id');
    }
}
