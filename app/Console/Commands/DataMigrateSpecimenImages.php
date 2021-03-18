<?php

namespace App\Console\Commands;

use App\Models\SpecimenImage;
use App\Models\TaxonConcept;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DataMigrateSpecimenImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:specimen-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load specimen images';

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

        $images = $conn->table('vicflora_specimen_images as i')
                ->join('vicflora_taxon as t', 'i.taxon_id', '=', 't.TaxonID')
                ->join('vicflora_taxon as a', 'i.accepted_id', '=', 'a.TaxonID')
                ->select('i.cumulus_record_id',
                        'i.record_name',
                        'i.ala_image_uuid',
                        'i.title',
                        'i.caption',
                        'i.originating_program',
                        'i.subject_category',
                        'i.pixel_x_dimension',
                        'i.pixel_y_dimension',
                        'i.scientific_name',
                        't.guid as taxon_guid',
                        'a.guid as accepted_guid')
                ->get();

        foreach ($images as $image) {

            $catalogNumber = str_replace('MEL', 'MEL ', substr($image->record_name, 0, -4));
            if (strpos($catalogNumber, '_') !== false) {
                $catalogNumber = str_replace('_', '', $catalogNumber);
            }

            SpecimenImage::create([
                'cumulus_record_id' => $image->cumulus_record_id,
                'record_name' => $image->record_name,
                'catalog_number' => $catalogNumber,
                'ala_image_guid' => $image->ala_image_uuid,
                'title' => $image->title,
                'caption' => $image->caption,
                'originating_program' => $image->originating_program,
                'subject_category' => $image->subject_category,
                'pixel_x_dimension' => $image->pixel_x_dimension,
                'pixel_y_dimension' => $image->pixel_y_dimension,
                'scientific_name' => $image->scientific_name,
                'taxon_concept_id' => TaxonConcept::where('guid', $image->taxon_guid)->value('id'),
                'accepted_id' => TaxonConcept::where('guid', $image->accepted_guid)->value('id')
            ]);
        }
    }
}
