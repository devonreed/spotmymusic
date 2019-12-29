<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Venue
 */
class Venue extends Model
{
    public $timestamps = true;

    public function venues()
    {
        return $this->belongsToMany('App\Models\User', 'user_venues', 'venue_id', 'user_id');
    }

    public static function getAllForDomain($domain)
    {
        return self::where('url', 'LIKE', '%'.$domain.'%')->get();
    }
}
