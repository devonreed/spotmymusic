<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Show
 *
 * @property-read \App\Models\Band $band
 * @mixin \Eloquent
 * @property int $id
 * @property int $band_id
 * @property string $venue
 * @property \Carbon\Carbon $show_date
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereBandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereShowDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Show whereVenue($value)
 */
class Show extends Model
{
    protected $casts = [
        'show_date' => 'date',
    ];

    protected $fillable = ['venue_id', 'show_date'];

    /**
     * Defines relationship to Band
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function band()
    {
        return $this->belongsTo(Band::class);
    }
}
