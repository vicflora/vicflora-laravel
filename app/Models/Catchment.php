<?php

namespace App\Models;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Catchment extends Model
{
    use HasPostgisColumns;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mapper_overlays.catchments';

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

    /**
     * GeoJSON 'geometry' object
     *
     * @return array<mixed>
     */
    public function getGeometryAttribute()
    {
        return [
            'type' => 'MultiPolygon',
            'coordinates' => json_decode($this->geom->toJson(), true)['coordinates'],
        ];
    }


    public function getPropertiesAttribute()
    {
        return [
            'id' => $this->id,
            'name' => $this->area_name,
            'label' => $this->area_name,
            'nameShort' => $this->area_name,
            'code' => $this->area_code,
            'state' => 'Victoria',
            'slug' => Str::slug($this->area_name),
        ];
    }
}
