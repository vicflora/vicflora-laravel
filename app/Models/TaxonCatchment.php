<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxonCatchment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mapper.taxon_concept_catchments_view';

    /**
     * @return BelongsTo
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'taxon_concept_id', 'guid');
    }

    /**
     * @return BelongsTo
     */
    public function catchment(): BelongsTo
    {
        return $this->belongsTo(Catchment::class, 'area_id', 'id');
    }
}
