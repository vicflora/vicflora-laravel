<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Models\TaxonConcept;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DataMigrateImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load images';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $conn = DB::connection('mysql');

        $images = $conn->table('cumulus_images_cip as i')
                ->join('vicflora_taxon as t', 'i.taxon_id', '=', 't.TaxonID')
                ->join('vicflora_taxon as a', 'i.accepted_id', '=', 'a.TaxonID')
                ->select('i.timestamp_created',
                        'i.timestamp_modified',
                        'i.version',
                        'i.asset_creation_date',
                        'i.caption',
                        'i.catalog_number',
                        'i.copyright_owner',
                        'i.country',
                        'i.country_code',
                        'i.creation_date',
                        'i.creator',
                        'i.cumulus_catalog',
                        'i.cumulus_record_id',
                        'i.cumulus_record_name',
                        'i.decimal_latitude',
                        'i.decimal_longitude',
                        'i.hero_image',
                        'i.license',
                        'i.locality',
                        'i.modified',
                        'i.originating_program',
                        'i.pixel_x_dimension',
                        'i.pixel_y_dimension',
                        'i.rating',
                        'i.recorded_by',
                        'i.record_number',
                        'i.rights',
                        'i.scientific_name',
                        'i.source',
                        'i.state_province',
                        'i.subject_category',
                        'i.subject_orientation',
                        'i.subject_part',
                        'i.subtype',
                        'i.title',
                        'i.type',
                        'i.uid',
                        't.guid as taxon_guid',
                        'a.guid as accepted_guid')
                ->get();

        foreach($images as $img) {
            Image::create([
                'timestamp_created' => $img->timestamp_created,
                'timestamp_modified' => $img->timestamp_modified,
                'version' => $img->version,
                'asset_creation_date' => $img->asset_creation_date,
                'caption' => $img->caption,
                'catalog_number' => $img->catalog_number,
                'copyright_owner' => $img->copyright_owner,
                'country' => $img->country,
                'country_code' => $img->country_code,
                'creation_date' => $img->creation_date,
                'creator' => $img->creator,
                'cumulus_catalog' => $img->cumulus_catalog,
                'cumulus_record_id' => $img->cumulus_record_id,
                'cumulus_record_name' => $img->cumulus_record_name,
                'decimal_latitude' => $img->decimal_latitude,
                'decimal_longitude' => $img->decimal_longitude,
                'hero_image' => $img->hero_image,
                'license' => $img->license,
                'locality' => $img->locality,
                'modified' => $img->modified,
                'originating_program' => $img->originating_program,
                'pixel_x_dimension' => $img->pixel_x_dimension,
                'pixel_y_dimension' => $img->pixel_y_dimension,
                'rating' => $img->rating,
                'recorded_by' => $img->recorded_by,
                'record_number' => $img->record_number,
                'rights' => $img->rights,
                'scientific_name' => $img->scientific_name,
                'source' => $img->source,
                'state_province' => $img->state_province,
                'subject_category' => $img->subject_category,
                'subject_orientation' => $img->subject_orientation,
                'subject_part' => $img->subject_part,
                'subtype' => $img->subtype,
                'title' => $img->title,
                'type' => $img->type,
                'uid' => $img->uid,
                'taxon_id' => TaxonConcept::where('guid', $img->taxon_guid)->value('id'),
                'accepted_id' => TaxonConcept::where('guid', $img->taxon_guid)->value('id'),
            ]);
        }
    }
}
