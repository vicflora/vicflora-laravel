<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InaturalistObservationPhoto extends Model
{

    /**
     * The table associated with the model
     *
     * @var string
     */
    protected $table = 'inaturalist.observation_photos';

    /**
     * The observation
     *
     * @return BelongsTo
     */
    public function observation(): BelongsTo
    {
        return $this->belongsTo(InaturalistObservation::class);
    }

    /**
     * The photo
     *
     * @return BelongsTo
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(InaturalistPhoto::class);
    }
}
