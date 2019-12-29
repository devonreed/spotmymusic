<?php

namespace App\Models;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Band
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Show[] $shows
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property boolean $ignore
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Band whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Band whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Band whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Band whereIgnore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Band whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Band whereSoundcloudUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Band whereUpdatedAt($value)
 * @property string|null $soundcloud_user_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Band whereSoundcloudUserId($value)
 * @property int|null $soundcloud_search_failed
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Band whereSoundcloudSearchFailed($value)
 * @property array $top_track
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Band whereTopTrack($value)
 */
class Band extends Model
{
    protected $casts = [
        'ignore' => 'boolean',
        'soundcloud_search_failed' => 'boolean',
        'top_track' => 'array',
    ];

    /**
     * Defines relationship to Shows.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shows()
    {
        return $this->hasMany(Show::class);
    }

    /**
     * Finds or creates a band from a name
     *
     * @param $name
     * @return Band
     */
    public static function findOrCreate($name)
    {
        $band = self::where('name', $name)->first();
        if ($band) {
            return $band;
        }

        $band = new self();
        $band->name = $name;
        $band->save();
        return $band;
    }

    /**
     * Determines if a show is already in the database
     *
     * @param $venue
     * @return bool
     */
    public function isDuplicateShow($venueId, Carbon $showDate)
    {
        return $this->shows()->where('venue_id', $venueId)->where('show_date', $showDate->toDateString())->count() !== 0;
    }
}
