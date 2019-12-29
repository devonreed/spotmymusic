<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreVenues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('venues')->insert([
            'name' => 'Webster Hall',
            'url' => 'https://www.ohmyrockness.com/venues/webster-hall'
        ]);
        DB::table('venues')->insert([
            'name' => 'Elsewhere',
            'url' => 'https://www.ohmyrockness.com/venues/elsewhere'
        ]);
        DB::table('venues')->insert([
            'name' => 'Bowery Ballroom',
            'url' => 'https://www.ohmyrockness.com/venues/bowery-ballroom'
        ]);
        DB::table('venues')->insert([
            'name' => 'Brooklyn Steel',
            'url' => 'https://www.ohmyrockness.com/venues/brooklyn-steel--2'
        ]);
        DB::table('venues')->insert([
            'name' => 'Rough Trade',
            'url' => 'https://www.ohmyrockness.com/venues/rough-trade'
        ]);
        DB::table('venues')->insert([
            'name' => 'Le Poisson Rouge',
            'url' => 'https://www.ohmyrockness.com/venues/le-poisson-rouge'
        ]);
        DB::table('venues')->insert([
            'name' => 'Music Hall of Williamsburg',
            'url' => 'https://www.ohmyrockness.com/venues/music-hall-of-williamsburg'
        ]);
        DB::table('venues')->insert([
            'name' => 'Beacon Theatre',
            'url' => 'https://www.ohmyrockness.com/venues/beacon-theatre'
        ]);
        DB::table('venues')->insert([
            'name' => 'Gramercy Theatre',
            'url' => 'https://www.ohmyrockness.com/venues/gramercy-theatre'
        ]);
        DB::table('venues')->insert([
            'name' => 'Baby\'s All Right',
            'url' => 'https://www.ohmyrockness.com/venues/baby-s-all-right'
        ]);
        DB::table('venues')->insert([
            'name' => 'Knitting Factory',
            'url' => 'https://www.ohmyrockness.com/venues/knitting-factory-brooklyn'
        ]);
        DB::table('venues')->insert([
            'name' => 'Saint Vitus',
            'url' => 'https://www.ohmyrockness.com/venues/saint-vitus-bar'
        ]);
        DB::table('venues')->insert([
            'name' => 'Union Pool',
            'url' => 'https://www.ohmyrockness.com/venues/union-pool'
        ]);
        DB::table('venues')->insert([
            'name' => 'Trans-Pecos',
            'url' => 'https://www.ohmyrockness.com/venues/trans-pecos'
        ]);
        DB::table('venues')->insert([
            'name' => 'Bushwick Public House',
            'url' => 'https://www.songkick.com/venues/2911698-bushwick-public-house'
        ]);
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
