<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InaturalistObservation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inaturalist.observations';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'place_ids' => 'array',
    ];

    /**
     * iNaturalist Taxon the observation belongs to
     *
     * @return BelongsTo
     */
    public function taxon(): BelongsTo
    {
        return $this->belongsTo(InaturalistTaxon::class);
    }

    /**
     * iNaturalist user who made the observation
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(InaturalistUser::class);
    }

    /**
     * Photos for the observation
     *
     * @return HasMany
     */
    public function observationPhotos(): HasMany
    {
        return $this->hasMany(InaturalistObservationPhoto::class);
    }


    public function getGeometryAttribute()
    {
        return json_decode((string) $this->geojson);
    }

    public function getObservedOnDateTimeDetailsAttribute()
    {
        return json_decode((string) $this->observed_on_details);
    }


    public function getObservationUrlAttribute(): string
    {
        return 'https://inaturalist.ala.org.au/observations/' . $this->id;
    }
}
