<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $taxon_concept_id
 * @property integer $bioregion_id
 * @property string $bioregion_name
 * @property string $bioregion_code
 * @property string $occurrence_status
 * @property string $establishment_means
 * @property string $geom
 * @property TaxonConcept $taxonConcept
 * @property Bioregion $bioregion
 */
class TaxonBioregion extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mapper.taxon_bioregions';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bioregion(): BelongsTo
    {
        return $this->belongsTo(Bioregion::class, 'bioregion_id', 'id');
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
