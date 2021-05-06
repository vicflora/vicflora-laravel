<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property Integer $bioregion_id
 * @property String $sub_code_7
 * @property String $sub_name_7
 * @property String $reg_code_7
 * @property String $reg_name_7
 * @property String $geom
 * @property String $depi_code
 * @property Integer $depi_order
 * @property Array $coordinates
 */
class Bioregion extends Model
{
    use HasFactory;

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
    protected $table = 'bioregions';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'bioregion_id';


    public function getCoordinatesAttribute()
    {
        $result = DB::connection('mapper')->table('vicflora.vicflora_bioregion')
                ->where('bioregion_id', $this->bioregion_id)
                ->value(DB::raw('ST_AsGeoJSON(geom)'));

        $geojson = json_decode($result);

        return $geojson->coordinates;
    }

    public function getGeometryAttribute()
    {
        $geometry = DB::connection('mapper')->table('bioregions')
                ->where('id', $this->id)
                ->value(DB::raw('ST_AsGeoJSON(geom)'));

        return json_decode($geometry);
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
            'regionCode' => $this->reg
        ];
    }

    public function getTypeAttribute()
    {
        return 'Feature';
    }

}
