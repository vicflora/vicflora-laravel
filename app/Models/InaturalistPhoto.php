<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InaturalistPhoto extends Model
{
    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = 'inaturalist.photos';


    public function observationPhotos(): HasMany
    {
        return $this->hasMany(InaturalistObservationPhoto::class);
    }


    public function observations(): BelongsToMany
    {
        return $this->belongsToMany(InaturalistObservation::class, 'inaturalist.observation_photos', 'photo_id', 'observation_id');
    }


    /**
     * Taxon Concepts this photo can be linked to
     *
     * @return BelongsToMany
     */
    public function taxonConcepts(): BelongsToMany
    {
        return $this->belongsToMany(TaxonConcept::class,
                'taxon_concept_inaturalist_photo', 'inaturalist_photo_id',
                'taxon_concept_id');
    }

    /**
     * Creative Commons license for the photo
     *
     * @return BelongsTo
     */
    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }


    public function getOriginalImageDimensionsAttribute()
    {
        return json_decode((string) $this->original_dimensions);
    }

    public function getPixelXDimensionAttribute(): int
    {
        return json_decode((string) $this->original_dimensions)->width;
    }

    public function getPixelYDimensionAttribute(): int
    {
        return json_decode((string) $this->original_dimensions)->height;
    }


    public function getThumbnailUrlAttribute(): string
    {
        return str_replace('/square', '/medium', $this->url);
    }

    public function getPreviewUrlAttribute(): string
    {
        return str_replace('/square', '/large', $this->url);
    }

    public function getOriginalImageUrlAttribute(): string
    {
        return str_replace('/square', '/original', $this->url);
    }

}
