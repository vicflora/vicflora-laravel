<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id
 * @property integer $reference_type_id
 * @property int $parent_id
 * @property integer $author_id
 * @property int $created_by_id
 * @property int $modified_by_id
 * @property string $created
 * @property string $publication_year
 * @property string $title
 * @property string $short_title
 * @property string $edition
 * @property string $volume
 * @property string $issue
 * @property int $page_start
 * @property int $page_end
 * @property string $pages
 * @property int $number_of_pages
 * @property string $publisher
 * @property string $place_of_publication
 * @property string $short_description
 * @property string $abstract
 * @property string $isbn
 * @property string $issn
 * @property string $doi
 * @property string $citation
 * @property string $url
 * @property int $number
 * @property string $citation_html
 * @property integer $version
 * @property string $guid
 * @property string $created_at
 * @property string $updated_at
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
    protected $fillable = ['reference_type_id', 'parent_id', 'author_id',
            'created_by_id', 'modified_by_id', 'created', 'publication_year',
            'title', 'short_title', 'edition', 'volume', 'issue', 'page_start',
            'page_end', 'pages', 'number_of_pages', 'publisher',
            'place_of_publication', 'short_description', 'abstract', 'isbn',
            'issn', 'doi', 'citation', 'url', 'number', 'citation_html',
            'version', 'guid', 'created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'author_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referenceType(): BelongsTo
    {
        return $this->belongsTo(ReferenceType::class);
    }

    /**
     * Gets the reference type controlled value
     *
     * @return string|null
     */
    public function getReferenceTypeNameAttribute(): ?string
    {
        if ($this->reference_type_id) {
            $rt = ReferenceType::find($this->reference_type_id);
            return $rt->name;
        }
        return null;
    }

    /**
     * Sets reference_type_id if referenceTypeName attribute is provided
     *
     * @param string|null $value
     * @return void
     */
    public function setReferenceTypeNameAttribute(?string $value)
    {
        if ($value) {
            $rt = ReferenceType::where('name', $value)->first();
            $this->reference_type_id = $rt->id;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Reference::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function contributors(): HasMany
    {
        return $this->hasMany(Contributor::class, 'reference_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxonNames(): HasMany
    {
        return $this->hasMany(TaxonName::class, 'protologue_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxonConcepts(): HasMany
    {
        return $this->hasMany(TaxonConcept::class, 'according_to_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class, 'source_id');
    }


    public function getContributorStringAttribute()
    {
        $contributors = Contributor::join('references',
                'contributors.reference_id', '=', 'references.id')
                ->join('contributor_roles', 'contributors.contributor_role_id',
                        '=', 'contributor_roles.id')
                ->join('agents', 'contributors.agent_id', '=', 'agents.id')
                ->where('references.id', $this->id)
                ->select('references.id as reference_id',
                        'contributors.sequence',
                        'agents.last_name',
                        'agents.initials',
                        'contributor_roles.name as role')
                ->get();

        $agents = [];
        foreach ($contributors as $index => $contributor) {
            if ($index === 0) {
                $role = $contributor->role;
                $agents[] = $contributor->last_name . ', ' . $contributor->initials;
            }
            else {
                $agents[] = $contributor->initials . ' ' . $contributor->last_name;
            }
        }

        $count = count($agents);
        if ($count > 1) {
            $str = implode(', ', array_slice($agents, 0, $count-1))
                    . ' & ' . $agents[$count-1];
        }
        else {
            $str = $agents[0];
        }
        if ($role !== 'Author') {
            if ($count > 1) {
                $str .= ' (eds)';
            }
            else {
                $str .= ' (ed.)';
            }
        }
        return $str;
    }

    /**
     * Citation without markup
     *
     * @return string
     */
    public function getCitationAttribute(): string
    {
        $type = $this->referenceType->name;
        $citation = '';
        switch ($type) {
            case 'Journal':
                case 'Series':
                    $citation .= $this->title;
                    break;

                case 'Book':
                case 'Report':
                case 'AudioVisualDocument':
                    $citation .= $this->contributorString
                            . ' (' . $this->publication_year . '). '
                            . $this->title . '. ';
                    if ($this->publisher) {
                        $citation .= ' ' . $this->publisher;
                        if ($this->place_of_publication) {
                            $citation .= ', ' . $this->place_of_publication;
                        }
                        $citation .= '.';
                    }
                    break;

                case 'Article':
                    $citation .= $this->contributorString
                            . ' (' . $this->publication_year . '). '
                            . $this->title;
                    $citation .= $this->parent->title;
                    if ($this->volume) {
                        $citation .= ' ' . $this->volume;
                        if ($this->issue) {
                            $citation .= '(' . $this->issue . ')';
                        }
                        if ($this->page_start) {
                            $citation .= ': ' .
                                    $this->page_start . '–' . $this->page_end;
                        }
                        elseif ($this->pages) {
                            $citation .= ': ' . $this->pages;
                        }
                    }
                    elseif ($this->number) {
                        $citation .= ' ' . $this->number;
                    }
                    $citation .= '.';

                case 'Chapter':
                    $citation .= $this->contributorString
                            . ' (' . $this->publication_year . '). '
                            . $this->title . '. ';
                    $citation .= 'In: ' . $this->parent->contributorString
                            . ', ' . $this->parent->title
                            . ', pp. ' . $this->page_start
                            . '–' . $this->page_end
                            . '.';
                    if ($this->parent->publisher) {
                        $citation .= ' ' . $this->parent->publisher;
                        if ($this->parent->place_of_publication) {
                            $citation .= ', '
                                . $this->parent->place_of_publication;
                        }
                        $citation .= '.';
                    }
                    break;

                case 'Protologue':
                    if ($this->author_id) {
                        $citation .= 'in ' . $this->author->name . ', ';
                    }
                    $citation .= $this->title;
                    if ($this->volume) {
                        $citation .= ' ' . $this->volume;
                    }
                    if ($this->issue) {
                        $citation .= '(' . $this->issue . ')';
                    }
                    $citation .= ': ' . $this->pages
                            . ' (' . $this->publication_year . ')';
                    break;

                default:
                    break;
        }
        return $citation;
    }

    /**
     * Citation with Markdown markup
     *
     * @return string
     */
    public function getCitationMarkdownAttribute(): string
    {
        $type = $this->referenceType->name;
        $citation = '';
        switch ($type) {
            case 'Journal':
                case 'Series':
                    $citation .= $this->title;
                    break;

                case 'Book':
                case 'Report':
                case 'AudioVisualDocument':
                    $citation .= '**' . $this->contributorString
                            . ' (' . $this->publication_year . ')**. '
                            . $this->title . '. ';
                    if ($this->publisher) {
                        $citation .= ' ' . $this->publisher;
                        if ($this->place_of_publication) {
                            $citation .= ', ' . $this->place_of_publication;
                        }
                        $citation .= '.';
                    }
                    break;

                case 'Article':
                    $citation .= '**' . $this->contributorString
                            . ' (' . $this->publication_year . ')**. '
                            . $this->title;
                    $citation .= '*' . $this->parent->title . '*';
                    if ($this->volume) {
                        $citation .= ' **' . $this->volume . '**';
                        if ($this->issue) {
                            $citation .= '(' . $this->issue . ')';
                        }
                        if ($this->page_start) {
                            $citation .= ': ' .
                                    $this->page_start . '–' . $this->page_end;
                        }
                        elseif ($this->pages) {
                            $citation .= ': ' . $this->pages;
                        }
                    }
                    elseif ($this->number) {
                        $citation .= ' ' . $this->number;
                    }
                    $citation .= '.';

                case 'Chapter':
                    $citation .= '**' . $this->contributorString
                            . ' (' . $this->publication_year . ')**. '
                            . $this->title . '. ';
                    $citation .= 'In: ' . $this->parent->contributorString
                            . ', *&zwj;' . $this->parent->title . '&zwj;*'
                            . ', pp. ' . $this->page_start
                            . '–' . $this->page_end
                            . '.';
                    if ($this->parent->publisher) {
                        $citation .= ' ' . $this->parent->publisher;
                        if ($this->parent->place_of_publication) {
                            $citation .= ', '
                                . $this->parent->place_of_publication;
                        }
                        $citation .= '.';
                    }
                    break;

                case 'Protologue':
                    if ($this->author_id) {
                        $citation .= 'in ' . $this->author->name . ', ';
                    }
                    $citation .= '*' . $this->title . '*';
                    if ($this->volume) {
                        $citation .= ' **' . $this->volume . '**';
                    }
                    if ($this->issue) {
                        $citation .= '(' . $this->issue . ')';
                    }
                    $citation .= ': ' . $this->pages
                            . ' (' . $this->publication_year . ')';
                    break;

                default:
                    break;
        }
        return $citation;
    }

    /**
     * Citation with HTML markup
     *
     * @return string
     */
    public function getCitationHtmlAttribute(): string
    {
        $type = $this->referenceType->name;
        $citation = '';
        switch ($type) {
            case 'Journal':
                case 'Series':
                    $citation .= $this->title;
                    break;

                case 'Book':
                case 'Report':
                case 'AudioVisualDocument':
                    $citation .= '<strong>' . $this->contributorString
                            . ' (' . $this->publication_year . ')</strong>. '
                            . $this->title . '. ';
                    if ($this->publisher) {
                        $citation .= ' ' . $this->publisher;
                        if ($this->place_of_publication) {
                            $citation .= ', ' . $this->place_of_publication;
                        }
                        $citation .= '.';
                    }
                    break;

                case 'Article':
                    $citation .= '<strong>' . $this->contributorString
                            . ' (' . $this->publication_year . ').</strong> '
                            . $this->title;
                    $citation .= '<em>' . $this->parent->title . '</em>';
                    if ($this->volume) {
                        $citation .= ' <strong>' . $this->volume . '</strong>';
                        if ($this->issue) {
                            $citation .= '(' . $this->issue . ')';
                        }
                        if ($this->page_start) {
                            $citation .= ': ' .
                                    $this->page_start . '–' . $this->page_end;
                        }
                        elseif ($this->pages) {
                            $citation .= ': ' . $this->pages;
                        }
                    }
                    elseif ($this->number) {
                        $citation .= ' ' . $this->number;
                    }
                    $citation .= '.';

                case 'Chapter':
                    $citation .= '<strong>' . $this->contributorString
                            . ' (' . $this->publication_year . ')</strong>. '
                            . $this->title . '. ';
                    $citation .= 'In: ' . $this->parent->contributorString
                            . ', <em>' . $this->parent->title . '</em>'
                            . ', pp. ' . $this->page_start
                            . '–' . $this->page_end
                            . '.';
                    if ($this->parent->publisher) {
                        $citation .= ' ' . $this->parent->publisher;
                        if ($this->parent->place_of_publication) {
                            $citation .= ', '
                                . $this->parent->place_of_publication;
                        }
                        $citation .= '.';
                    }
                    break;

                case 'Protologue':
                    if ($this->author_id) {
                        $citation .= 'in ' . $this->author->name . ', ';
                    }
                    $citation .= '<em>' . $this->title . '</em>';
                    if ($this->volume) {
                        $citation .= ' <strong>' . $this->volume . '</strong>';
                    }
                    if ($this->issue) {
                        $citation .= '(' . $this->issue . ')';
                    }
                    $citation .= ': ' . $this->pages
                            . ' (' . $this->publication_year . ')';
                    break;

                default:
                    break;
        }
        return $citation;
    }
}
