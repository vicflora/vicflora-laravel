<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $created_by_id
 * @property integer $modified_by_id
 * @property string $name
 * @property string $url
 * @property string $label
 * @property string $description
 * @property string $guid
 * @property string $timestamp_created
 * @property string $timestamp_modified
 * @property string $shorthand
 */
class GlossaryRelationshipType extends Model
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
    protected $table = 'relationship_types';
}
