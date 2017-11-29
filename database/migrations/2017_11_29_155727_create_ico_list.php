<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIcoList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('ico_list', function(Blueprint $table){
            $table->increments('id');
            $table->string('symbol');
            $table->string('name');
            $table->timestamps();
            $table->comments = 'ico列表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ico_list');
    }
}
