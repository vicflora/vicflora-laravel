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
 * @property string $lga_pid
 * @property string $lga_name
 * @property string $abbreviated_name
 * @property string $occurrence_status
 * @property string $establishment_means
 * @property string $degree_of_establishment
 * @property string $geom
 * 
 * @property TaxonConcept $taxonConcept
 * @property LocalGovernmentArea $localGovernmentArea
 */
class TaxonLocalGovernmentArea extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mapper.taxon_local_government_areas_view';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'taxon_concept_id', 
                'guid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function localGovernmentArea(): BelongsTo
    {
        return $this->belongsTo(LocalGovernmentArea::class, 
                'local_government_area_id', 'id');
    }
}
