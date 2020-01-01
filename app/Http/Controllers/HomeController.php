<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Venue;
use App\Sources\Tracks\Spotify;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function dashboard(Request $request)
    {
        $session = [
            'name' => null,
            'venues' => []
        ];

        if ($id = $request->session()->get('id')) {
            $user = User::where('spotify_user_id', $id)->first();
            if (!$user) {
                return view('home', $session)->render();
            }
            $session['name'] = $user->spotify_user_display_name;
            $session['playlist_id'] = $user->spotify_playlist_id;

            $venues = Venue::get(['name', 'id'])->toArray();

            $selections = $user->venues()->pluck('venue_id')->toArray();
            foreach ($venues as $index => $venue) {
                $venues[$index]['checked'] = in_array($venue['id'], $selections) ? 'checked' : '';
            }
            $session['venues'] = $venues;
        }

        return view('home', $session)->render();
    }

    public function song(Request $request)
    {
        $session = [
            'name' => null,
            'song' => null
        ];

        if ($id = $request->session()->get('id')) {
            $user = User::where('spotify_user_id', $id)->first();
            if (!$user) {
                return view('home', $session)->render();
            }
            $session['name'] = $user->spotify_user_display_name;
            $s = new Spotify($user->spotify_refresh_token);
            $session['song'] = $s->getMostRepresentativeSong();
        }

        return view('song', $session)->render();
    }
}
