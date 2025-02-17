<?php

namespace App\Models;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property string $uuid
 * @property string $catalog_number
 * @property float $latitude
 * @property float $longitude
 * @property string $geojson
 * @property string $data_source
 * @property string $taxon_id
 * @property string $accepted_name_usage_id
 * @property string $species_id
 * @property string $scientific_name
 * @property string $accepted_name_usage
 * @property string $species
 * @property string $occurrence_status
 * @property string $occurrence_status_source
 * @property string $establishment_means
 * @property string $establishment_means_source
 *
 * @property string $type
 * @property array<mixed> $geometry
 * @property array<mixed> $properties
 */
class Occurrence extends Model
{
    use HasPostgisColumns;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'occurrences';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'uuid',
        'parsed_name_id',
        'data_resource_uid',
        'data_source_id',
        'bioregion_id',
        'nrm_region_id',
        'scientific_name_id',
        'created_at',
        'updated_at',
        'institution_code',
        'collection_code',
        'catalog_number',
        'scientific_name',
        'unprocessed_scientific_name',
        'latitude',
        'longitude',
        'geom',
        'deleted_at',
        'taxon_id',
        'data_source_id',
    ];

    protected array $postgisColumns = [
        'geom' => [
            'type' => 'geometry',
            'srid' => 4326,
        ],
    ];

    public function getTypeAttribute()
    {
        return 'Feature';
    }

    public function getGeometryAttribute()
    {
        return json_decode($this->geojson);
    }

    public function getPropertiesAttribute()
    {
        return [
            'id' => $this->uuid,
            'catalogNumber' => $this->catalog_number,
            'dataSource' => $this->data_source,
            'decimalLatitude' => $this->latitude,
            'decimalLongitude' => $this->longitude,
            'scientificName' => $this->scientific_name,
            'acceptedNameUsage' => $this->accepted_name_usage,
            'occurrenceStatus' => $this->occurrence_status,
            'occurrenceStatusSource' => $this->occurrence_status_source,
            'establishmentMeans' => $this->establishment_means,
            'establishmentMeansSource' => $this->establishment_means_source,
            // 'sensitive' => $this->sensitive,
            // 'generalised' => $this->generalised,
        ];
    }


}
