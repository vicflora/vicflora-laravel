<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $taxonId
 * @property integer $acceptedId
 * @property string $timestampCreated
 * @property string $timestampModified
 * @property int $version
 * @property string $assetCreationDate
 * @property string $caption
 * @property string $catalogNumber
 * @property string $copyrightOwner
 * @property string $country
 * @property string $countryCode
 * @property string $creationDate
 * @property string $creator
 * @property string $cumulusCatalog
 * @property integer $cumulusRecordId
 * @property string $cumulusRecordName
 * @property float $decimalLatitude
 * @property float $decimalLongitude
 * @property boolean $heroImage
 * @property string $license
 * @property string $locality
 * @property string $modified
 * @property string $originatingProgram
 * @property int $pixelXDimension
 * @property int $pixelYDimension
 * @property integer $rating
 * @property string $recordedBy
 * @property string $recordNumber
 * @property string $rights
 * @property string $scientificName
 * @property string $source
 * @property string $stateProvince
 * @property string $subjectCategory
 * @property string $subjectOrientation
 * @property string $subjectPart
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
    protected $fillable = ['taxon_id', 'accepted_id', 'timestamp_created', 'timestamp_modified', 'version', 'asset_creation_date', 'caption', 'catalog_number', 'copyright_owner', 'country', 'country_code', 'creation_date', 'creator', 'cumulus_catalog', 'cumulus_record_id', 'cumulus_record_name', 'decimal_latitude', 'decimal_longitude', 'hero_image', 'license', 'locality', 'modified', 'originating_program', 'pixel_x_dimension', 'pixel_y_dimension', 'rating', 'recorded_by', 'record_number', 'rights', 'scientific_name', 'source', 'state_province', 'subject_category', 'subject_orientation', 'subject_part', 'subtype', 'title', 'type', 'uid'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function acceptedConcept()
    {
        return $this->belongsTo('App\Models\TaxonConcept', 'accepted_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonConcept()
    {
        return $this->belongsTo('App\Models\TaxonConcept', 'taxon_id');
    }
}
