<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo', 10)->nullable()->unique();
            $table->string('nombres', 200);
            $table->integer('tipo');
            //arreglo 3
            $table->string('apellidop', 50)->nullable();
            $table->string('apellidom', 50)->nullable();
            $table->string('dni', 11)->nullable();
            $table->integer('escuela_id')->unsigned();
            $table->foreign('escuela_id')->references('id')->on('escuelas')->onDelete('cascade');
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
        Schema::dropIfExists('clientes');
    }
}
