<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenesTraslado extends Model
{
    use HasFactory;

    protected $fillable = [
        'orden_id',
        'tipo_orden',
        'desde_compania',
        'para_compania',
        'desde_ubicacion',
        'para_ubicacion',
        'empleado',
        'comentarios',
        'centro_costo',
        'netsuite_id',
        'numero_orden_traslado',
        'fecha_netsuite',
        'estado',
        'estado_netsuite'
    ];

    public function detalles(){
        return $this->hasMany(DetallesOrdenesTraslado::class, 'orden_traslado_id');
    }

    public function ubicacion() {
        return $this->belongsTo(Ubicacion::class, 'desde_ubicacion', 'netsuite_id');
    }

    public function ubicacionPara() {
        return $this->belongsTo(Ubicacion::class, 'para_ubicacion', 'netsuite_id');
    }

    public function ubicacionAxa(){
        return $this->belongsTo(InventarioAxa::class, 'desde_ubicacion' , 'id_ubicacion');
    }

}
