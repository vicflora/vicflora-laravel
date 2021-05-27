<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
 * @property string $geom
 */
class ParkReserve extends Model
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
    protected $table = 'park_reserves';

    /**
     * @return string
     */
    public function getTypeAttribute()
    {
        return 'Feature';
    }
    
    /**
     * @return string
     */
    public function getFeatureTypeAttribute()
    {
        return "MultiPolygon";
    }


    /**
     * GeoJSON 'geometry' object
     *
     * @return array<mixed>
     */
    public function getGeometryAttribute()
    {
        $geometry = DB::connection('mapper')->table('park_reserves')
                ->where('id', $this->id)
                ->value(DB::raw('ST_AsGeoJSON(geom)'));

        return json_decode($geometry);
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
