<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConceptosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conceptos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion', 90);
            $table->integer('financialclassifier_id')->unsigned();
            $table->foreign('financialclassifier_id')->references('id')->on('financialclassifiers')->onDelete('cascade');
            $table->integer('budgetclassifier_id')->unsigned();
            $table->foreign('budgetclassifier_id')->references('id')->on('budgetclassifiers')->onDelete('cascade');
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
        Schema::dropIfExists('conceptos');
    }
}
