<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaseComercial extends Model
{
    use HasFactory;

    protected $table = 'clases_comerciales';
    protected $fillable = ['clase_comercial', 'status'];
    protected $casts = ['status' => 'boolean'];


    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'clases_comerciales_categorias', 'clase_comercial_id', 'categoria_id')->wherePivot('status', 1);
    }
}
