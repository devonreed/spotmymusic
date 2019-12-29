<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVenues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('venues')->insert([
            'name' => 'Pianos',
            'url' => 'https://www.ticketweb.com/venue/pianos-nyc-new-york-ny/18935'
        ]);
        DB::table('venues')->insert([
            'name' => 'Arlene\'s Grocery',
            'url' => 'https://www.ticketfly.com/venue/2545-arlenes-grocery/'
        ]);
        DB::table('venues')->insert([
            'name' => 'Rockwood Stage 2',
            'url' => 'https://www.ticketfly.com/venue/11239-rockwood-music-hall-stage-2/'
        ]);
        DB::table('venues')->insert([
            'name' => 'Rockwood Stage 3',
            'url' => 'https://www.ticketfly.com/venue/11241-rockwood-music-hall-stage-3/'
        ]);
        DB::table('venues')->insert([
            'name' => 'Bowery Electric Map Room',
            'url' => 'https://www.ticketfly.com/venue/25779-the-map-room-at-the-bowery-electric/'
        ]);
        DB::table('venues')->insert([
            'name' => 'Mercury Lounge',
            'url' => 'https://www.ohmyrockness.com/venues/mercury-lounge'
        ]);
        DB::table('venues')->insert([
            'name' => 'Berlin',
            'url' => 'https://www.ohmyrockness.com/venues/berlin'
        ]);
        DB::table('venues')->insert([
            'name' => 'Bowery Electric',
            'url' => 'https://www.ohmyrockness.com/venues/the-bowery-electric'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // TODO
    }
}
