<?php

namespace App\Models;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property string $typeAbbr
 * @property-read array<mixed> $geometry
 * @property-read array<mixed> $properties
 */
class ProtectedArea extends Model
{
    use HasPostgisColumns;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mapper.protected_areas_victoria_mv';

    protected array $postgisColumns = [
        'geom' => [
            'type' => 'geometry',
            'srid' => 4326,
        ],
    ];

    /**
     * @return string
     */
    public function getTypeAttribute()
    {
        return 'Feature';
    }

    // /**
    //  * @return string
    //  */
    // public function getFeatureTypeAttribute()
    // {
    //     return "MultiPolygon";
    // }


    /**
     * GeoJSON 'geometry' object
     *
     * @return array<mixed>
     */
    public function getGeometryAttribute()
    {
        return DB::select("select ST_AsGeoJSON($this->geom) as geojson")[0]->geojson;
    }

    /**
     * GeoJson 'properties' object
     *
     * @return array<mixed>
     */
    public function getPropertiesAttribute()
    {
        return [
            'id' => $this->id,
            'name' => $this->label,
            'label' => $this->label,
            'type' => $this->type,
            'typeAbbr' => $this->type_abbr,
            'state' => $this->state,
            'slug' => $this->slug,
        ];
    }

}
