<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * @property integer $id
 * @property integer $referenceTypeId
 * @property int $parentId
 * @property integer $authorId
 * @property int $createdById
 * @property int $modifiedById
 * @property string $created
 * @property string $publicationYear
 * @property string $title
 * @property string $shortTitle
 * @property string $edition
 * @property string $volume
 * @property string $issue
 * @property int $page_start
 * @property int $page_end
 * @property string $pages
 * @property int $numberOfPages
 * @property string $publisher
 * @property string $placeOfPublication
 * @property string $shortDescription
 * @property string $abstract
 * @property string $isbn
 * @property string $issn
 * @property string $doi
 * @property string $citation
 * @property string $url
 * @property int $number
 * @property string $citationHtml
 * @property integer $version
 * @property string $createdAt
 * @property string $updatedAt
 * @property Agent $createdBy
 * @property Agent $modifiedBy
 * @property Agent $author
 * @property ReferenceType $referenceType
 * @property VicfloraReference $vicfloraReference
 * @property Contributor[] $contributors
 * @property TaxonName[] $taxonNames
 * @property TaxonConcept[] $taxonConcepts
 * @property Profile[] $profiles
 */
class Reference extends BaseModel
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'references';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['reference_type_id', 'parent_id', 'author_id', 'created_by_id', 'modified_by_id', 'created', 'publication_year', 'title', 'short_title', 'edition', 'volume', 'issue', 'page_start', 'page_end', 'pages', 'number_of_pages', 'publisher', 'place_of_publication', 'short_description', 'abstract', 'isbn', 'issn', 'doi', 'citation', 'url', 'number', 'citation_html', 'version', 'created_at', 'updated_at'];

    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo('App\Models\Agent', 'author_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referenceType()
    {
        return $this->belongsTo('App\Models\ReferenceType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\Reference', 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contributors()
    {
        return $this->hasMany('App\Models\Contributor', 'reference_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxonNames()
    {
        return $this->hasMany('App\Models\TaxonName', 'protologue_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxonConcepts()
    {
        return $this->hasMany('App\Models\TaxonConcept', 'according_to_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profiles()
    {
        return $this->hasMany('App\Models\Profile', 'source_id');
    }
}
