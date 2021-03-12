<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $protologueId
 * @property integer $nomenclaturalStatusId
 * @property integer $nameTypeId
 * @property integer $basionymId
 * @property integer $replacedSynonymId
 * @property integer $createdById
 * @property integer $modifiedById
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $namePart
 * @property string $fullName
 * @property string $authorship
 * @property string $fullNameWithAuthorship
 * @property string $nomenclaturalNote
 * @property string $remarks
 * @property integer $version
 * @property string $guid
 * @property NameType $nameType
 * @property NomenclaturalStatus $nomenclaturalStatus
 * @property TaxonName $basionym
 * @property TaxonName $replacedSynonym
 * @property Reference $protologue
 * @property Agent $createdBy
 * @property Agent $modifiedBy
 * @property TaxonConcept[] $taxonConcepts
 */
class TaxonName extends BaseModel
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
    protected $fillable = ['protologue_id', 'nomenclatural_status_id', 'name_type_id', 'basionym_id', 'replaced_synonym_id', 'created_by_id', 'modified_by_id', 'created_at', 'updated_at', 'name_part', 'full_name', 'authorship', 'full_name_with_authorship', 'nomenclatural_note', 'remarks', 'version', 'guid'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nameType()
    {
        return $this->belongsTo('App\Models\NameType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nomenclaturalStatus()
    {
        return $this->belongsTo('App\Models\NomenclaturalStatus');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function replacedSynonym()
    {
        return $this->belongsTo('App\Models\TaxonName', 'replaced_synonym_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function basionym()
    {
        return $this->belongsTo('App\Models\TaxonName', 'basionym_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function protologue()
    {
        return $this->belongsTo('App\Models\Reference', 'protologue_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxonConcepts()
    {
        return $this->hasMany('App\Models\TaxonConcept');
    }
}
