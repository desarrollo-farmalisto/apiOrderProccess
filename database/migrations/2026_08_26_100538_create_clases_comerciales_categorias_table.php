<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clases_comerciales_categorias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('clase_comercial_id')->constrained('clases_comerciales')->comment('ID de la clase comercial');
            $table->foreignId('categoria_id')->constrained('categorias')->comment('ID de la categoría');
            
            $table->enum('status', [0,1])->default(1)->comment('Estado de la relación: 0 = Inactivo, 1 = Activo');
            $table->text('observacion')->nullable()->comment('Observaciones sobre la relación');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clases_comerciales_categorias');
    }
};
