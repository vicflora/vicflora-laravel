<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property integer $term_id
 * @property string $image_url
 * @property string $creator
 * @property string $rights
 * @property string $license_url
 * @property string $caption
 * @property string $guid
 * @property integer $version
 * @property string $timestamp_created
 * @property string $timestamp_modified
 * @property string $license_logo_url
 * @property GlossaryTerm $term
 */
class GlossaryTermImage extends Model
{
    /**
     * The database connection that should be used by the model.
     *
     * @var string
     */
    protected $connection = 'glossary';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'term_images';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(GlossaryTerm::class, 'term_id');
    }

}
