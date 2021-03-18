<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $taxon_id
 * @property integer $accepted_id
 * @property string $timestamp_created
 * @property string $timestamp_modified
 * @property int $version
 * @property string $asset_creation_date
 * @property string $caption
 * @property string $catalog_number
 * @property string $copyright_Owner
 * @property string $country
 * @property string $country_code
 * @property string $creation_date
 * @property string $creator
 * @property string $cumulus_catalog
 * @property integer $cumulus_record_id
 * @property string $cumulus_record_name
 * @property float $decimal_latitude
 * @property float $decimal_longitude
 * @property boolean $hero_image
 * @property string $license
 * @property string $locality
 * @property string $modified
 * @property string $originating_program
 * @property integer $pixel_x_dimension
 * @property integer $pixel_y_dimension
 * @property integer $rating
 * @property string $recorded_by
 * @property string $record_number
 * @property string $rights
 * @property string $scientific_name
 * @property string $source
 * @property string $state_province
 * @property string $subject_category
 * @property string $subject_orientation
 * @property string $subject_part
 * @property string $subtype
 * @property string $title
 * @property string $type
 * @property string $uid
 * @property TaxonConcept $taxonConcept
 * @property TaxonConcept $acceptedConcept
 */
class Image extends BaseModel
{
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['taxon_id', 'accepted_id', 'timestamp_created', 
            'timestamp_modified', 'version', 'asset_creation_date', 'caption', 
            'catalog_number', 'copyright_owner', 'country', 'country_code', 
            'creation_date', 'creator', 'cumulus_catalog', 'cumulus_record_id', 
            'cumulus_record_name', 'decimal_latitude', 'decimal_longitude', 
            'hero_image', 'license', 'locality', 'modified', 'originating_program', 
            'pixel_x_dimension', 'pixel_y_dimension', 'rating', 'recorded_by', 
            'record_number', 'rights', 'scientific_name', 'source', 
            'state_province', 'subject_category', 'subject_orientation', 
            'subject_part', 'subtype', 'title', 'type', 'uid'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function acceptedConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'accepted_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'taxon_id');
    }


    public function getThumbnailUrlAttribute()
    {
        return env('IMAGE_SERVER_BASE_URL') . 'preview/thumbnail/public/' 
                . $this->cumulus_record_id;
    }

    public function getPreviewUrlAttribute()
    {
        return env('IMAGE_SERVER_BASE_URL') . 'preview/image/public/' 
                . $this->cumulus_record_id . '?maxsize=1024';
    }
}
