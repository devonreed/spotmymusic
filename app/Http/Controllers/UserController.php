<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Venue;
use App\Sources\Tracks\Spotify;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function saveVenues(Request $request)
    {
        if ($id = $request->session()->get('id')) {
            $user = User::where('spotify_user_id', $id)->first();
            $ids = $request->get('venue_ids');
            $user->venues()->sync($ids);
        }
    }
}
