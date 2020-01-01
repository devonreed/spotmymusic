<?php

namespace App\Models;

use Crypt;
use DB;
use Carbon\Carbon;
use App\Sources\Tracks\Spotify;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\User
 */
class User extends Model
{
    protected $fillable = ['spotify_user_id'];

    public function venues()
    {
        return $this->belongsToMany('App\Models\Venue', 'user_venues', 'user_id', 'venue_id');
    }

    public function setSpotifyRefreshTokenAttribute($value)
    {
        $this->attributes['spotify_refresh_token'] = Crypt::encrypt($value);
    }

    public function getSpotifyRefreshTokenAttribute($value)
    {
        if (is_null($value)) {
            return $value;
        }
        return Crypt::decrypt($value);
    }

    public function exportPlaylist()
    {
        $userVenues = $this->venues()->pluck('venue_id')->all();

        if (empty($userVenues)) {
            $spotify_ids = [];
        } else {
            $items = DB::table('shows')
            ->where('shows.show_date', Carbon::now()->toDateString())
            ->join('bands', 'bands.id', '=', 'shows.band_id')
            ->whereNotNull('bands.top_spotify_track')
            ->whereIn('shows.venue_id', $userVenues)
            ->get();

            $spotify_ids = $items->pluck('top_spotify_track');
            $spotify_ids = $spotify_ids->all();
        }
        $spotify = new Spotify($this->spotify_refresh_token);
        $spotify->replacePlaylist($this->spotify_user_id, $this->spotify_playlist_id, $spotify_ids);

        return $spotify_ids;
    }
}
