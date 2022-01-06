<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $taxon_concept_id
 * @property string $occurrence_id
 * @property string $catalog_number
 * @property float $latitude
 * @property float $longitude
 * @property string $geom
 * @property string $geojson
 * @property string $data_source
 * @property string $taxon_id
 * @property string $accepted_name_usage_id
 * @property string $species_id
 * @property string $scientific_name
 * @property string $accepted_name
 * @property string $species_name
 * @property string $occurrence_status
 * @property string $occurrence_status_source
 * @property string $establishment_means
 * @property string $establishment_means_source
 * @property string $degree_of_establishment
 * @property string $degree_of_establishment_source
 */
class TaxonOccurrence extends Model
{
   /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mapper.taxon_occurrences';

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
    public function occurrence(): BelongsTo
    {
        return $this->belongsTo(Occurrence::class, 'occurrence_id', 'uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxon(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'taxon_id', 'guid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function species(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'species_id', 'guid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function acceptedNameUsage(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'accepted_name_usage_id',
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

    /**
     * @return string
     */
    public function getTypeAttribute(): string
    {
        return 'Feature';
    }

    /**
     * @return string
     */
    public function getFeatureTypeAttribute(): string
    {
        return 'MultiPolygon';
    }

    /**
     * @return array
     */
    public function getGeometryAttribute(): array
    {
        return (array) json_decode($this->geojson);
    }

    public function getPropertiesAttribute(): array
    {
        return [
            'uuid' => $this->occurrence_id,
            'dataSource' => $this->data_source,
            'catalogNumber' => $this->catalog_number,
            'taxonId' => $this->taxon_id,
            'speciesId' => $this->species_id,
            'acceptedNameUsageId' => $this->accepted_name_usage_id,
            'scientificName' => $this->scientific_name,
            'speciesName' => $this->species_name,
            'acceptedNameUsage' => $this->accepted_name,
            'decimalLatitude' => $this->latitude,
            'decimalLongitude' => $this->longitude,
            'occurrenceStatus' => $this->occurrence_status,
            'occurrenceStatusSource' => $this->occurrence_status_source,
            'establishmentMeans' => $this->establishment_means,
            'establishmentMeansSource' => $this->establishment_means_source
        ];
    }
}
