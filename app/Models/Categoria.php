<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';
    protected $fillable = ['categoria', 'status'];
    protected $casts = ['status' => 'boolean'];

    public function clasesComerciales()
    {
        return $this->belongsToMany(ClaseComercial::class, 'clases_comerciales_categorias', 'categoria_id', 'clase_comercial_id')->wherePivot('status', 1);
    }
}
