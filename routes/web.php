<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@dashboard');
Route::post('/venues', 'UserController@saveVenues');

Route::get('refresh', function () {
    if (env('APP_ENV') !== 'production') {
        \Illuminate\Support\Facades\Artisan::call('playlist:generate');
        return response('OK', 200);
    } else {
        return response('Nope', 403);
    }
});


Route::get('export', function () {
    if (env('APP_ENV') !== 'production') {
        \Illuminate\Support\Facades\Artisan::call('playlist:export');
        return response('OK', 200);
    } else {
        return response('Nope', 403);
    }
});

Route::get('/mysong', 'HomeController@song');

Route::get('logout', 'Auth\SpotifyController@logout');
Route::get('login/spotify', 'Auth\SpotifyController@redirectToProvider');
Route::get('login/spotify/callback', 'Auth\SpotifyController@handleProviderCallback');