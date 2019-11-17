<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Sources\Tracks\Spotify;
use Illuminate\Http\Request;

class HomeController extends Controller
{
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
