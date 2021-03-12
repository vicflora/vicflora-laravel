<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $createdById
 * @property integer $modifiedById
 * @property integer $subjectTaxonConceptId
 * @property integer $objectTaxonConceptId
 * @property integer $taxonRelationshipTypeId
 * @property integer $taxonRelationshipComponentId
 * @property integer $taxonRelationshipQualifierId
 * @property string $createdAt
 * @property string $updatedAt
 * @property integer $relationshipAccordingToId
 * @property string $remarks
 * @property string $guid
 * @property integer $version
 * @property TaxonRelationshipQualifier $taxonRelationshipQualifier
 * @property TaxonRelationshipComponent $taxonRelationshipComponent
 * @property TaxonRelationshipType $taxonRelationshipType
 * @property Agent $agent
 * @property Agent $agent
 * @property TaxonConcept $subjectTaxonConcept
 * @property TaxonConcept $objectTaxonConcept
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
    protected $fillable = ['created_by_id', 'modified_by_id', 'subject_taxon_concept_id', 'object_taxon_concept_id', 'taxon_relationship_type_id', 'taxon_relationship_component_id', 'taxon_relationship_qualifier_id', 'created_at', 'updated_at', 'relationship_according_to_id', 'remarks', 'guid', 'version'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonRelationshipQualifier()
    {
        return $this->belongsTo('App\Models\TaxonRelationshipQualifier');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonRelationshipComponent()
    {
        return $this->belongsTo('App\Models\TaxonRelationshipComponent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxonRelationshipType()
    {
        return $this->belongsTo('App\Models\TaxonRelationshipType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function objectTaxonConcept()
    {
        return $this->belongsTo('App\Models\TaxonConcept', 'object_taxon_concept_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subjectTaxonConcept()
    {
        return $this->belongsTo('App\Models\TaxonConcept', 'subject_taxon_concept_id');
    }
}
