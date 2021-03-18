<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property integer $protologue_id
 * @property integer $nomenclatural_status_id
 * @property integer $name_type_id
 * @property integer $basionym_id
 * @property integer $replaced_synonym_id
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $name_part
 * @property string $full_ame
 * @property string $authorship
 * @property string $full_name_with_authorship
 * @property string $nomenclatural_note
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
    protected $fillable = ['protologue_id', 'nomenclatural_status_id', 
            'name_type_id', 'basionym_id', 'replaced_synonym_id', 'created_by_id', 
            'modified_by_id', 'created_at', 'updated_at', 'name_part', 'full_name', 
            'authorship', 'full_name_with_authorship', 'nomenclatural_note', 
            'remarks', 'version', 'guid'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nameType(): BelongsTo
    {
        return $this->belongsTo(NameType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nomenclaturalStatus(): BelongsTo
    {
        return $this->belongsTo(NomenclaturalStatus::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function replacedSynonym(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class, 'replaced_synonym_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function basionym(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class, 'basionym_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function protologue(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'protologue_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxonConcepts(): HasMany
    {
        return $this->hasMany(TaxonConcept::class);
    }
}
