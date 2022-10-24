<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $taxon_concept_id
 * @property string $scientific_name
 * @property integer $park_id
 * @property string $park_name
 * @property string $park_short_name
 * @property string $occurrence_status
 * @property string $establishment_means
 * @property string $degreeOfEstablishment
 * @property string $geom
 * 
 * @property TaxonConcept $taxonConcept
 * @property ParkReserve $parkReserve
 */
class TaxonParkReserve extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mapper.taxon_concept_park_reserves';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parkReserve(): BelongsTo
    {
        return $this->belongsTo(ParkReserve::class, 'park_reserve_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'taxon_concept_id', 
                'guid');
    }

}
