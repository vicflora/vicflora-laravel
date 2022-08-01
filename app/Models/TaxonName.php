<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
 * @property string $full_name
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
     * @return string|null
     */
    public function getNameTypeNameAttribute(): ?string
    {
        if ($this->name_type_id) {
            $nt = NameType::find($this->name_type_id);
            return $nt->name;
        }
        return null;
    }

    /**
     * Set name_type_id if nameTypeName attribute is provided
     *
     * @param string|null $value
     * @return void
     */
    public function setNameTypeAttribute(?string $value)
    {
        if ($value) {
            $nt = NameType::where('name', $value)->first();
            $this->name_type_id = $nt->id;
        }
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
    public function publishedIn(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'published_in_id');
    }

    /**
     * @return BelongsTo
     */
    public function parentName(): BelongsTo
    {
        return $this->belongsTo(TaxonName::class);
    }

    /**
     * @return BelongsTo
     */
    public function nameRank(): BelongsTo
    {
        return $this->belongsTo(TaxonTreeDefItem::class, 'name_rank_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxonConcepts(): HasMany
    {
        return $this->hasMany(TaxonConcept::class);
    }

    /**
     * Link to APNI name record
     *
     * @return HasOne
     */
    public function apniName(): HasOne
    {
        return $this->hasOne(ApniName::class, 'taxon_name_id', 'guid');
    }

    /**
     * Link to VBA taxon record
     *
     * @return HasOne
     */
    public function vbaName(): HasOne
    {
        return $this->hasOne(VbaName::class, 'taxon_name_id', 'guid');
    }

    public function getNameRankAttribute(): ?string
    {
        if ($this->name_rank_id) {
            $tr = TaxonTreeDefItem::find($this->name_rank_id);
            return $tr->name;
        }
        return null;
    }

}
