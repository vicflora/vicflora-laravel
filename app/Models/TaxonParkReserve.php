<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $taxon_id
 * @property string $scientific_name
 * @property integer $park_reserve_id
 * @property string $park_name
 * @property string $park_short_name
 * @property string $str_occurrence_status
 * @property string $str_establishment_means
 * @property string $geom
 */
class TaxonParkReserve extends Model
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
    protected $table = 'distribution_park_reserve_view';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parkReserve(): BelongsTo
    {
        return $this->belongsTo(ParkReserve::class, 'park_reserve_id', 'id');
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
