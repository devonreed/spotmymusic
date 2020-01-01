<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

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
            $spotify_ids = $user->exportPlaylist();
            $this->info('Spotify playlist for ' . $user->spotify_user_id . ' refreshed ' . $user->spotify_playlist_id . ' with ' . serialize($spotify_ids));
        }
        
    }
}
