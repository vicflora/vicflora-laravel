<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integere $id
 * @property string $name
 * @property string $short_name
 *  
 */
class RegisteredAboriginalParty extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mapper.raps';

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
        ];
    }
}
