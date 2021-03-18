<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property integer $subject_taxon_concept_id
 * @property integer $object_taxon_concept_id
 * @property integer $taxon_relationship_type_id
 * @property integer $taxon_relationship_component_id
 * @property integer $taxon_relationship_qualifier_id
 * @property integer $relationship_according_to_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $remarks
 * @property string $guid
 * @property integer $version
 * @property TaxonRelationshipQualifier $taxonRelationshipQualifier
 * @property TaxonRelationshipComponent $taxonRelationshipComponent
 * @property TaxonRelationshipType $taxonRelationshipType
 * @property Agent $createdBy
 * @property Agent $modifiedBy
 * @property TaxonConcept $subjectTaxonConcept
 * @property TaxonConcept $objectTaxonConcept
 * @property Reference $relationshipAccordingTo
 */
class TaxonRelationship extends BaseModel
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
    protected $fillable = ['created_by_id', 'modified_by_id', 
        'subject_taxon_concept_id', 'object_taxon_concept_id', 
        'taxon_relationship_type_id', 'taxon_relationship_component_id', 
        'taxon_relationship_qualifier_id', 'created_at', 'updated_at', 
        'relationship_according_to_id', 'remarks', 'guid', 'version'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonRelationshipQualifier(): BelongsTo
    {
        return $this->belongsTo(TaxonRelationshipQualifier::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonRelationshipComponent(): BelongsTo
    {
        return $this->belongsTo(TaxonRelationshipComponent::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonRelationshipType(): BelongsTo
    {
        return $this->belongsTo(TaxonRelationshipType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function objectTaxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'object_taxon_concept_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subjectTaxonConcept(): BelongsTo
    {
        return $this->belongsTo(TaxonConcept::class, 'subject_taxon_concept_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function relationshipAccordingTo(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'relationship_according_to_id');
    }
}
