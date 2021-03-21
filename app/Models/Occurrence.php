<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Occurrence extends Model
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
    protected $table = 'vicflora.occurrences';

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

    public function getTypeAttribute()
    {
        return 'Feature';
    }


    public function getGeometryAttribute()
    {
        return json_decode($this->geo_json);
    }

    public function getPropertiesAttribute()
    {
        return [
            'id' => $this->id,
            'catalogNumber' => $this->catalog_number,
            'dataSource' => $this->data_source,
            'decimalLatitude' => $this->decimal_latitude,
            'decimalLongitude' => $this->decimal_longitude,
            'scientificName' => $this->scientific_name,
            'acceptedNameUsage' => $this->accepted_name_usage,
            'subregion' => $this->sub_name_7,
            'subregionCode' => $this->sub_code_7,
            'region' => $this->reg_name_7,
            'regionCode' => $this->reg_code_7,
            'occurrenceStatus' => $this->occurrence_status,
            'occurrenceStatusSource' => $this->occurrence_status_source,
            'establishmentMeans' => $this->establishment_means,
            'establishmentMeansSource' => $this->establishment_means_source,
            'sensitive' => $this->sensitive,
            'generalised' => $this->generalised,
        ];
    }


}
