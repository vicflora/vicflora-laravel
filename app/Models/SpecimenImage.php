<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $taxon_concept_id
 * @property integer $accepted_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $cumulus_record_id
 * @property string $record_name
 * @property string $catalog_number
 * @property string $ala_image_guid
 * @property string $title
 * @property string $caption
 * @property string $originating_program
 * @property string $subject_category
 * @property integer $pixel_x_dimension
 * @property integer $pixel_y_dimension
 * @property string $scientific_name
 * @property TaxonConcept $taxonConcept
 * @property TaxonConcept $acceptedConcept
 */
class SpecimenImage extends BaseModel
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
    protected $fillable = ['taxon_concept_id', 'accepted_id', 'created_at', 
            'updated_at', 'cumulus_record_id', 'record_name', 'catalog_number', 
            'ala_image_guid', 'title', 'caption', 'originating_program', 
            'subject_category', 'pixel_x_dimension', 'pixel_y_dimension', 
            'scientific_name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function acceptedConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'accepted_id');
    }

    /**
     * @return string
     */
    public function getThumbnailUrlAttribute()
    {
        return 'https://images.ala.org.au/image/proxyImageThumbnail?imageId=' . 
                $this->ala_image_guid;
    }
}
