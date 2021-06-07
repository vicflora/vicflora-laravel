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
 * @property OccurrenceStatus $occurrenceStatus
 * @property EstablishmentMeans $establishmentMeans
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function occurrenceStatus(): BelongsTo
    {
        return $this->belongsTo(OccurrenceStatus::class, 'occurrence_status', 
                'name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function establishmentMeans(): BelongsTo
    {
        return $this->belongsTo(EstablishmentMeans::class, 
                'establishment_means', 'name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function degreeOfEstablishment(): BelongsTo
    {
        return $this->belongsTo(DegreeOfEstablishment::class, 
                'degree_of_establishment', 'name');
    }
}
