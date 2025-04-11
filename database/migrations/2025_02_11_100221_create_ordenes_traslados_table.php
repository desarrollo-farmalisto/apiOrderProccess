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
        Schema::create('ordenes_traslados', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("orden_id")->nullable();
            $table->string("tipo_orden");
            $table->string("desde_compania");
            $table->string("para_compania");
            $table->string("desde_ubicacion");
            $table->string("para_ubicacion");
            $table->bigInteger("empleado");
            $table->text("comentarios")->nullable();
            $table->bigInteger("centro_costo");
            $table->string('netsuite_id')->nullable();
            $table->string('numero_orden_traslado')->nullable();
            $table->string('fecha_netsuite')->nullable();
            $table->integer("estado")->default(0);
            $table->string('estado_netsuite')->nullable();
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
        Schema::dropIfExists('ordenes_traslados');
    }
};
