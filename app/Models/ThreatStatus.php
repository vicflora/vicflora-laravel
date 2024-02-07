<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreatStatus extends Model
{
    /**
     * The table that is associated with the model
     *
     * @var string
     */
    protected $table = 'taxon_concept_threat_statuses';

    /**
     * Get the TaxonConcept the ThreatStatus belongs to
     *
     * @return BelongsTo
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class);
    }

    /**
     * Get the ConservationList the ThreatStatus belongs to
     *
     * @return BelongsTo
     */
    public function conservationList(): BelongsTo
    {
        return $this->belongsTo(ConservationList::class);
    }

    /**
     * Get the IucnCategory the ThreatStatus belongs to
     *
     * @return BelongsTo
     */
    public function iucnCategory(): BelongsTo
    {
        return $this->belongsTo(IucnCategory::class);
    }

}
