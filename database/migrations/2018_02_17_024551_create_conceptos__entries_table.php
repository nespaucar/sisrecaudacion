<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConceptosEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conceptos__entries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cantidad')->unsigned();
            $table->float('p_real', 8, 2);
            $table->string('descripcion', 150);
            $table->float('importe', 8, 2);

            $table->integer('entry_id')->unsigned();
            $table->foreign('entry_id')->references('id')->on('entries')->onDelete('cascade');

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
        Schema::dropIfExists('conceptos__entries');
    }
}
