<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property integer $id
 * @property string $lga_pid
 * @property string $lga_name
 * @property string $abb_name
 * @property integer $state_pid
 * @property string $geojson
 *
 * @property string $type
 * @property array<mixed> $geometry
 * @property array<mixed> $properties
 */
class LocalGovernmentArea extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mapper.local_government_areas';

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
        return json_decode($this->geojson);
    }

    /**
     * GeoJson 'properties' object
     *
     * @return array<mixed>
     */
    public function getPropertiesAttribute()
    {
        return [
            'id' => $this->lga_pid,
            'name' => Str::title($this->lga_name),
            'label' => Str::title($this->lga_name),
            'nameAbbr' => Str::title($this->abb_name),
            'created' => $this->dt_create,
            'gazetted' => $this->dt_gazetd,
        ];
    }
}
