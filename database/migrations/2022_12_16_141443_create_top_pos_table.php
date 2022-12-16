<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopPosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('top_pos', function (Blueprint $table) {
            $table->id();
            $table->date('date')->index('dates');
            $table->integer('category');
            $table->integer('parentCategory')->nullable();
            $table->integer('position');
        });
    }




    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('top_pos');
    }
}
