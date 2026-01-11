<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property string $occurrence_status
 * @property string $establishment_means
 * @property string $geom
 * @property TaxonConcept $taxonConcept
 * @property Bioregion $bioregion
 */
class TaxonIbra7Subregion extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mapper.taxon_concept_ibra7_subregions_view';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ibra7Subregion(): BelongsTo
    {
        return $this->belongsTo(Ibra7Subregion::class, 'ibra7_subregion_id', 'id');
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
