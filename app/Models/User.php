<?php

namespace App\Models;

use Crypt;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\User
 */
class User extends Model
{
    protected $fillable = ['spotify_user_id'];

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
}
