<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Integer $id
 * @property String $taxon_guid
 * @property Integer $bioregion_id
 * @property String $str_occurrence_status
 * @property String $str_establishment_means
 * @property TaxonConcept $taxonConcept
 * @property Bioregion $bioregion
 * @property OccurrenceStatus $occurrenceStatus
 * @property EstablishmentMeans $establishmentMeans
 */
class TaxonBioregion extends Model
{
    use HasFactory;

    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'mapper';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vicflora.taxon_bioregions';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bioregion(): BelongsTo
    {
        return $this->belongsTo(Bioregion::class, 'bioregion_id', 'bioregion_id');
    }


    /**
     * @return \App\Models\TaxonConcept
     */
    public function getTaxonConceptAttribute()
    {
        return TaxonConcept::where('guid', $this->taxon_guid)->first();
    }

    /**
     * @return \App\Models\OccurrenceStatus
     */
    public function getOccurrenceStatusAttribute()
    {
        return OccurrenceStatus::where('name', $this->str_occurrence_status ?: 'present')
                ->first();
    }

    /**
     * @return \App\Models\EstablishmentMeans
     */
    public function getEstablishmentMeansAttribute()
    {
        return EstablishmentMeans::where('name', $this->str_establishment_means ?: 'native')
                ->first();
    }
}
