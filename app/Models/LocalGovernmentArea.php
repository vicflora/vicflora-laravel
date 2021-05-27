<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * @property integer $id
 * @property string $lga_pid
 * @property string $lga_name
 * @property string $abb_name
 * @property integer $state_pid
 * @property string $geom
 * 
 * @property string $type
 * @property string $featureType
 * @property array<mixed> $geometry
 * @property array<mixed> $properties
 */
class LocalGovernmentArea extends Model
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
    protected $table = 'local_government_areas';

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
        $geometry = DB::connection('mapper')->table('local_government_areas')
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
            'id' => $this->lga_pid,
            'name' => Str::title($this->lga_name),
            'nameAbbr' => Str::title($this->abb_name),
            'created' => $this->dt_create,
            'gazetted' => $this->dt_gazetd,
        ];
    }
}
