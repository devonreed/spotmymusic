<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Sources\Tracks\Spotify;
use Illuminate\Http\Request;
use Socialite;

class SpotifyController extends Controller
{
    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->session()->forget('id');
        $next = $request->input('next') ?? '';
        
        return redirect('/' . $next);
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider(Request $request)
    {
        $app = $request->input('app') ?? 'playlist';
        $next = $request->input('next') ?? '';

        $request->session()->put('app', $app);
        $request->session()->put('next', $next);

        $permissions = [
            'mysong' => 'user-top-read',
            'playlist' => 'playlist-modify', 'playlist-modify-public', 'playlist-modify-private',
        ];        
        
        return Socialite::driver('spotify')
            ->with(['show_dialog' => false])
            ->setScopes($permissions[$app])
            ->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request)
    {
        $input = $request->all();
        if (array_key_exists('error', $input)) {
            return redirect('/');
        }

        $response = Socialite::driver('spotify')->getAccessTokenResponse($input['code']);

        $s = new Spotify($response['refresh_token']);
        $user = $s->getUser();

        // get user by id

        $u = User::firstOrNew(['spotify_user_id' => $user->id]);

        $u->spotify_refresh_token = $response['refresh_token'];
        $u->spotify_user_id = $user->id;
        $u->spotify_user_display_name = $user->display_name;
        $u->save();

        $app = $request->session()->get('app');
        if ($app === 'playlist' && !$u->spotify_playlist_id) {
            $s = new Spotify($u->spotify_refresh_token);
            $playlist = $s->createPlaylist($u->spotify_user_id);
            $u->spotify_playlist_id = $playlist->id;
            $u->save();
        }

        $request->session()->put('id', $u->spotify_user_id);

        $next = $request->session()->get('next');
        return redirect('/' . $next);
    }
}