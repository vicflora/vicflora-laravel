<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LocalGovernmentArea extends Model
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
    protected $table = 'vicflora.local_government_areas_view';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'gid';

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
