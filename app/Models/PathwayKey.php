<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PathwayKey extends Model
{
    /**
     * Taxon Concept that the key belongs to
     *
     * @return BelongsTo
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class);
    }

    /**
     * Agent who created the key
     *
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Agent who last updated the key
     *
     * @return BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

}
