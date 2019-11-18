<?php

namespace App\Sources\Tracks;

class Spotify
{
    const SEARCH_API_URL = 'https://api.spotify.com/v1/search?q={search}&type=artist';

    const TRACK_API_URL = 'https://api.spotify.com/v1/artists/{id}/top-tracks?country=US';

    const SINGLE_TRACK_API_URL = 'https://api.spotify.com/v1/tracks/{id}';

    const PLAYLIST_API_URL = 'https://api.spotify.com/v1/users/{userId}/playlists/{playlistId}/tracks';

    const USER_URL = 'https://api.spotify.com/v1/me';

    const REPRESENTATIVE_URL = 'https://api.spotify.com/v1/me/top/tracks?time_range=long_term&limit=50';

    const SEARCH_URL = 'https://api.spotify.com/v1/search?type=track&q={query}';

    private $token;

    public function __construct($refreshToken)
    {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=refresh_token&refresh_token=' . $refreshToken);
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

    public function replacePlaylist($spotifyUserId, $spotifyPlaylistId, array $ids)
    {
        $searchUrl = str_replace('{userId}', $spotifyUserId, self::PLAYLIST_API_URL);
        $searchUrl = str_replace('{playlistId}', $spotifyPlaylistId, $searchUrl);

        $counter = 0;
        while ($counter < count($ids)) {
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
