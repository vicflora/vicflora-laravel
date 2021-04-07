<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property string $id
 * @property string $name
 * @property string $type
 * @property string $type_abbr
 * @property string $iucn
 * @property string $nrs_pa
 * @property date $gaz_date
 * @property string $authority
 * @property string $datasource
 */
class ProtectedArea extends Model
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
    protected $table = 'vicflora.protected_areas';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * @return string
     */
    public function getTypeAttribute()
    {
        return 'Feature';
    }

    public function getGeometryAttribute()
    {
    //     $geometry = DB::connection('mapper')->table('vicflora.protected_areas')
    //             ->where('id', $this->id)
    //             ->value(DB::raw('ST_AsGeoJSON(geom)'));

    //     return json_decode($geometry);
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
            'type' => $this->type,
            'typeAbbr' => $this->type_abbr,
            'iucn' => $this->iucn,
            'authority' => $this->authority,
            'dataSource' => $this->datasource,
        ];
    }

}
