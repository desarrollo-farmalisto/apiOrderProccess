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
        DB::statement('ALTER TABLE catalogos MODIFY precio_venta DECIMAL(15,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE catalogos MODIFY precion_con_iva DECIMAL(15,2) NOT NULL DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE catalogos MODIFY precio_venta DOUBLE NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE catalogos MODIFY precion_con_iva DOUBLE NOT NULL DEFAULT 0');
    }
};
