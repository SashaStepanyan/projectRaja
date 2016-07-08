<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('job');
            $table->string('email')->unique();
            $table->string('city');
            $table->integer('country_id')->unsigned();
            $table->foreign('country_id')->references('id')->on('country');
            $table->enum('key_contact', ['yes', 'no']);
            $table->string('tags');
            $table->text('notes');
            $table->tinyInteger('status');
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
        Schema::drop('persons');
    }
}
