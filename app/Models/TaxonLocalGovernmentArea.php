<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $taxon_id
 * @property string $scientific_name
 * @property integer $local_government_area_id
 * @property string $lga_pid
 * @property string $lga_name
 * @property string $abb_name
 * @property string $str_occurrence_status
 * @property string $str_establishment_means
 * @property string $geom
 * 
 */
class TaxonLocalGovernmentArea extends Model
{

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
    protected $table = 'distribution_local_government_area_view';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function localGovernmentArea(): BelongsTo
    {
        return $this->belongsTo(LocalGovernmentArea::class, 'local_government_area_id', 'id');
    }

    /**
     * @return \App\Models\TaxonConcept
     */
    public function getTaxonConceptAttribute()
    {
        return TaxonConcept::where('guid', $this->taxon_id)->first();
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
