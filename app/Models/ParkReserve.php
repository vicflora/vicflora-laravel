<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $short_name
 * @property string $area_type
 * @property string $area_src
 * @property string $veac_rec
 * @property string $veac_study
 * @property string $iucn
 * @property string $estab_date
 * @property string $poly_src
 * @property string $last_mod
 * @property string $vers_date
 * @property float $area_sqm
 * @property string $geojson
 *
 * @property string $type
 * @property array<mixed> $geometry
 * @property array<mixed> $properties
 */
class ParkReserve extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mapper_overlays.park_reserves';

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
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->name,
            'nameShort' => $this->name_short,
            'areaType' => $this->area_type,
            'areaSrc' => $this->area_src,
            'veacRec' => $this->veac_rec,
            'veacStudy' => $this->veac_study,
            'iucn' => $this->iucn,
            'establishmentDate' => $this->estab_date,
            'areaSqm' => $this->area_sqm
        ];
    }

}
