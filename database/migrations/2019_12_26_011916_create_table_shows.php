<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableShows extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shows', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('band_id');
            $table->unsignedInteger('venue_id');
            $table->date('show_date');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('band_id')->references('id')->on('bands');
            $table->foreign('venue_id')->references('id')->on('venues');

            $table->index('band_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('shows');
    }
}
