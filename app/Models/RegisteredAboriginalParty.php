<?php

namespace App\Models;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property integere $id
 * @property string $name
 * @property string $short_name
 *
 */
class RegisteredAboriginalParty extends Model
{
    use HasPostgisColumns;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mapper.raps';

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

    /**
     * GeoJSON 'geometry' object
     *
     * @return array<mixed>
     */
    public function getGeometryAttribute()
    {
        return json_decode($this->geojson);
    }

    public function getPropertiesAttribute()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->name,
            'shortName' => $this->shortName,
            'traditionalOweners' => $this->traditional_owners,
            'slug' => $this->slug,
        ];
    }
}
