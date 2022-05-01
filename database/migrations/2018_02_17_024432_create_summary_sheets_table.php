<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSummarySheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summary_sheets', function (Blueprint $table) {
            $table->increments('id');

            $table->string('numserie', 15);
            $table->date('fecha');
            $table->float('total', 8, 2);

            $table->boolean('estado')->nullable();

            $table->string('np1', 15)->nullable();
            $table->string('np2', 15)->nullable();
            $table->string('np3', 15)->nullable();
            $table->string('np4', 15)->nullable();

            $table->float('mp1', 8, 2)->nullable();
            $table->float('mp2', 8, 2)->nullable();
            $table->float('mp3', 8, 2)->nullable();
            $table->float('mp4', 8, 2)->nullable();

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
        Schema::dropIfExists('summary_sheets');
    }
}
