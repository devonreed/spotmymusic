<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVenueLinks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('venues')->insert([
            'name' => 'Rockwood Music Hall',
            'url' => 'https://www.eventbrite.com/o/rockwood-music-hall-17577648220'
        ]);

        DB::table('venues')
            ->where('name', 'Rockwood Stage 2')
            ->delete();
        DB::table('venues')
            ->where('name', 'Rockwood Stage 3')
            ->delete();
        DB::table('venues')
            ->where('name', 'Bowery Electric Map Room')
            ->delete();

        DB::table('venues')
            ->where('name', 'Pianos')
            ->update(['url' => 'https://www.eventbrite.com/o/pianos-nyc-18377916155']);
        DB::table('venues')
            ->where('name', 'Arlene\'s Grocery')
            ->update(['url' => 'https://www.eventbrite.com/o/arlenes-grocery-18921813974']);
        DB::table('venues')
            ->where('name', 'Bowery Electric')
            ->update(['url' => 'https://www.eventbrite.com/o/the-bowery-electric-19832938511']);   
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
