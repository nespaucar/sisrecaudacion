<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion');
            $table->float('p_actual', 8, 2);
            $table->float('igv', 8, 2);
            $table->integer('concepto_id')->unsigned();
            $table->foreign('concepto_id')->references('id')->on('conceptos')->onDelete('cascade');
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
        Schema::dropIfExists('tasas');
    }
}
