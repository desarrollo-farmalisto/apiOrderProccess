<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaseComercialCategoria extends Model
{
    use HasFactory;
    protected $table = 'clases_comerciales_categorias';
    protected $fillable = ['clase_comercial_id', 'categoria_id', 'observacion'];
    protected $casts = ['status' => 'boolean'];
}
