<?php

namespace App\Models;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $bioregno
 * @property string $bioregion
 * @property string $bioregcode
 * @property string $geom
 * @property string $geojson
 * @property string $depi_code
 * @property integer $depi_order
 *
 * @property string $type
 * @property array<mixed> $geometry
 * @property array<mixed> $properties
 */
class Bioregion extends Model
{
    use HasPostgisColumns;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mapper_overlays.bioregions';

    protected array $postgisColumns = [
        'geom' => [
            'type' => 'geometry',
            'srid' => 4326,
        ],
    ];

    public function getGeometryAttribute()
    {
        return json_decode($this->geojson);
    }

    public function getFeatureTypeAttribute()
    {
        return "MultiPolygon";
    }

    public function getPropertiesAttribute()
    {
        return [
            'id' => $this->id,
            'number' => $this->bioregno,
            'name' => $this->bioregion,
            'code' => $this->bioregcode,
            'label' => $this->bioregion . ' (' . $this->bioregcode . ')',
            'slug' => $this->slug,
        ];
    }

    public function getTypeAttribute()
    {
        return 'Feature';
    }

}
