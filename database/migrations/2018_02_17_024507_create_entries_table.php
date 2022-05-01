<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('numrecibo')->unique();
            $table->date('fecha');
            $table->float('monto', 8, 2);
            $table->boolean('estado');
            $table->boolean('anulado');

            $table->integer('tasa_id');

            $table->integer('costcenter_id')->unsigned();
            $table->foreign('costcenter_id')->references('id')->on('cost_centers')->onDelete('cascade');

            $table->integer('cliente_id')->unsigned();
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');

            $table->integer('summary_sheet_id')->unsigned();
            $table->foreign('summary_sheet_id')->references('id')->on('summary_sheets')->onDelete('cascade');

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
        Schema::dropIfExists('entries');
    }
}
