<?php

namespace App\Console\Commands;

use App\Models\Band;
use App\Models\Show;
use App\Sources\Shows\OhMyRockness;
use App\Sources\Shows\TicketFly;
use App\Sources\Shows\TicketWeb;
use App\Sources\Tracks\SoundCloud;
use App\Sources\Tracks\Spotify;
use Carbon\Carbon;
use Illuminate\Console\Command;
use DB;

class GeneratePlaylist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'playlist:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a spreadsheet with links to tracks';

    protected $showSources = [
        OhMyRockness::class,
        TicketFly::class,
        TicketWeb::class,
    ];

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
        $this->ensureDatabase();
        $this->importShows();
        $this->loadTracks();
        $this->exportCSV();
    }

    protected function ensureDatabase()
    {
        $dbFile = config('database.connections.sqlite.database');
        if (!file_exists($dbFile)) {
            $this->info('Creating database');
            touch($dbFile);
            $this->call('migrate');
        }
    }

    /**
     * Loads show and band data into the database
     */
    protected function importShows()
    {
        $this->info('Importing shows');
        foreach ($this->showSources as $source) {
            $this->info($source);
            $showSource = app()->make($source);
            $showSource->import();
        }
        $totalBands = Band::count();
        $this->info("Found $totalBands bands");
    }

    /**
     * Loads top track data into the database
     */
    protected function loadTracks()
    {
        $this->info('Importing tracks');
        $spot = new Spotify();

        $shows = Show::where('show_date', '>', (new Carbon())->subDays(30))->get();
        $bar = $this->output->createProgressBar(count($shows));
        foreach ($shows as $show) {
            $spot->importTopTrack($show->band);
            $bar->advance();
        }
        $bar->finish();
        $this->line('');
    }

    /**
     * Writes the data to a csv
     */
    protected function exportCSV()
    {
        $this->call('playlist:export');
    }
}
