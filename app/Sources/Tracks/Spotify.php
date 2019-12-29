<?php

namespace App\Sources\Tracks;

use App\Models\Band;
use Carbon\Carbon;

class Spotify
{
    const SEARCH_API_URL = 'https://api.spotify.com/v1/search?q={search}&type=artist';
    const TRACK_API_URL = 'https://api.spotify.com/v1/artists/{id}/top-tracks?country=US';
    const SINGLE_TRACK_API_URL = 'https://api.spotify.com/v1/tracks/{id}';
    const SEARCH_URL = 'https://api.spotify.com/v1/search?type=track&q={query}';

    const CREATE_PLAYLIST_API_URL = 'https://api.spotify.com/v1/me/playlists';
    const PLAYLIST_API_URL = 'https://api.spotify.com/v1/playlists/{playlistId}/tracks';
    const USER_URL = 'https://api.spotify.com/v1/me';
    const REPRESENTATIVE_URL = 'https://api.spotify.com/v1/me/top/tracks?time_range=long_term&limit=50';


    private $token;

    public function __construct($refreshToken = null)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        if (!$refreshToken) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=refresh_token&refresh_token=' . $refreshToken);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode(env('SPOTIFY_CLIENT_ID').':'.env('SPOTIFY_CLIENT_SECRET'))));
        $result = curl_exec($ch);
        curl_close($ch);
        $obj = json_decode($result);
        $this->token = $obj->access_token;
    }

    public function getToken()
    {
        return $this->token;
    }

    /**
     * Saves the most played track for a band to the database
     *
     * @param Band $band
     */
    public function importTopTrack(Band $band)
    {
        if ($band->ignore) {
            return;
        }

        // Use the existing top track if it isn't too stale
        if ($band->top_spotify_track && $band->updated_at > (new Carbon())->subDays(30)) {
            return;
        }
        $band->spotify_search_failed = 0;
        $band->save();

        $spotifyId = $this->getSpotifyId($band);
        if (!$spotifyId) {
            return;
        }

        $topTrack = $this->getTopTrack($spotifyId);
        if (!$topTrack) {
            return;
        }

        $band->top_spotify_track = $topTrack->id;
        $band->save();
    }


    /**
     * Gets the top tracks for a user
     *
     * @param $spotifyId
     * @return obj
     */
    protected function getTopTrack($spotifyId)
    {
        $searchUrl = str_replace('{id}', $spotifyId, self::TRACK_API_URL);

        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $searchUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->token));
        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result);
        if (!empty($obj->tracks)) {
            return $obj->tracks[0];
        }
    }

    protected function searchForTrack($artist, $song)
    {
        $searchUrl = str_replace('{query}', urlencode("$artist $song"), self::SEARCH_URL);

        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $searchUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->token));
        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result);
        if (!empty($obj->tracks) && !empty($obj->tracks->items)) {
            foreach ($obj->tracks->items as $track) {
                if ($track->name === $song && $track->artists[0]->name === $artist) {
                    return $track;
                }
            }
        }
    }

    protected function getSpotifyId(Band $band)
    {
        if (!$band->spotify_id && !$band->spotify_search_failed) {
            $this->searchForBandId($band);
        }

        return $band->spotify_id;
    }

    /**
     * Search Spotify for a band. Adds the Spotify id to the band
     * if found.
     *
     * @param Band $band
     */
    protected function searchForBandId(Band $band)
    {
        $searchUrl = str_replace('{search}', urlencode($band->name), self::SEARCH_API_URL);

        try {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $searchUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->token));
            $result = curl_exec($ch);
            curl_close($ch);

            $obj = json_decode($result);
            if (!empty($obj->artists->items)) {
                $artist = $obj->artists->items[0];
                $band->spotify_id = $artist->id;
            } else {
                //$band->spotify_search_failed = true;
            }
        } catch (\Exception $e) {
            //$band->spotify_search_failed = true;
        }
            
        $band->save();
    }

    /**
     * Gets the user
     *
     * @return obj
     */
    public function getUser()
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, self::USER_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->token));
        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result);
        return $obj;
    }

    public function getMostRepresentativeSong()
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, self::REPRESENTATIVE_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->token));
        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result);

        $minPopularity = 101;
        $currentItem = null;

        $ignore = ['You Be My Heart', 'Long, Endless Parade', 'The Bigtop (Original Motion Picture Soundtrack)'];

        foreach ($obj->items as $ogItem) {
            if (in_array($ogItem->album->name, $ignore)) {
                continue;
            }
            $item = $this->searchForTrack($ogItem->artists[0]->name, $ogItem->name);
            if ($item && $item->popularity < $minPopularity) {
                $minPopularity = $item->popularity;
                $currentItem = $ogItem;
            }
        }
        return $currentItem;
    }

    public function createPlaylist($spotifyUserId)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, self::CREATE_PLAYLIST_API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['name' => 'NYC Today', 'public' => true]));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->token));
        $result = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($result);
        return $obj;
    }

    public function replacePlaylist($spotifyUserId, $spotifyPlaylistId, array $ids)
    {
        $searchUrl = str_replace('{userId}', $spotifyUserId, self::PLAYLIST_API_URL);
        $searchUrl = str_replace('{playlistId}', $spotifyPlaylistId, $searchUrl);

        $counter = 0;
        while ($counter === 0 || $counter < count($ids)) {
            $slice = array_slice($ids, $counter, 100);
            foreach ($slice as $index => $id) {
                $slice[$index] = 'spotify:track:' . $id;
            }
            $data = json_encode(['uris' => $slice]);
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $searchUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ' . $this->token,
                'Content-Type: application/json',
            ));
            if ($counter === 0) {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            }
            $result = curl_exec($ch);
            curl_close($ch);

            $counter += 100;
        }
    }
}
