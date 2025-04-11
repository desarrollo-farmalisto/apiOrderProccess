<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Type\Integer;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalles_ordenes_traslados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("orden_traslado_id");
            $table->string('item_id');
            $table->string('ean');
            $table->string('titulo');
            $table->integer('cantidad_solicitado');
            $table->integer('cantidad_recibido')->nullable();
            $table->timestamps();

            $table->foreign('orden_traslado_id')->references('id')->on('ordenes_traslados')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalles_ordenes_traslados');
    }
};
