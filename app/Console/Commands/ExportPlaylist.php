<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Sources\Tracks\Spotify;

class ExportPlaylist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'playlist:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Writes the playlist data to a csv';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Sending to Spotify');
        $users = User::whereNotNull('spotify_playlist_id')->get();
        foreach ($users as $user) {
            $userVenues = $user->venues()->pluck('venue_id')->all();
            $items = DB::table('shows')
                ->where('shows.show_date', Carbon::now()->toDateString())
                ->join('bands', 'bands.id', '=', 'shows.band_id')
                ->whereNotNull('bands.top_spotify_track')
                ->whereIn('shows.venue_id', $userVenues)
                ->get();

            $spotify_ids = $items->pluck('top_spotify_track');    
            $spotify = new Spotify($user->spotify_refresh_token);
            $spotify->replacePlaylist($user->spotify_user_id, $user->spotify_playlist_id, $spotify_ids->all());
            $this->info('Spotify playlist refreshed');
        }
        
    }
}
