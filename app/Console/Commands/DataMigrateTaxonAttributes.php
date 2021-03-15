<?php

namespace App\Console\Commands;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\TaxonAttribute;
use App\Models\TaxonConcept;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataMigrateTaxonAttributes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:taxon-attributes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load taxon attributes (EPBC, VROT, FFG)';

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

        $epbc = Attribute::create([
            'guid' => Str::uuid(),
            'name' => 'EPBC',
            'created_by_id' => 1
        ]);

        $epbcValues = $conn->table('vicflora_taxonattribute')
                ->where('Attribute', 'EPBC (Jan. 2014)')
                ->distinct()
                ->pluck('StrValue');

        foreach ($epbcValues as $val) {
            AttributeValue::create([
                'guid' => Str::uuid(),
                'attribute_id' => $epbc->id,
                'value' => $val,
                'created_by_id' => 1
            ]);
        }

        $taxonAttributes = $conn->table('vicflora_taxonattribute as ta')
                ->join('vicflora_taxon as t', 'ta.TaxonID', '=', 't.TaxonID')
                ->where('ta.Attribute', 'EPBC (Jan. 2014)')
                ->select('t.guid as taxon_id', 'StrValue as value')
                ->get();

        foreach ($taxonAttributes as $attr) {
            TaxonAttribute::create([
                'attribute_id' => $epbc->id,
                'attribute_value_id' => AttributeValue::where('attribute_id', $epbc->id)
                        ->where('value', $attr->value)->value('id'),
                'taxon_concept_id' => TaxonConcept::where('guid', $attr->taxon_id)->value('id'),
                'created_by_id' => 1
            ]);
        }

        $vrot = Attribute::create([
            'guid' => Str::uuid(),
            'name' => 'VROT',
            'created_by_id' => 1
        ]);

        $vrotValues = $conn->table('vicflora_taxonattribute')
                ->where('Attribute', 'VROT')
                ->distinct()
                ->pluck('StrValue');

        foreach ($vrotValues as $val) {
            AttributeValue::create([
                'guid' => Str::uuid(),
                'attribute_id' => $vrot->id,
                'value' => $val,
                'created_by_id' => 1
            ]);
        }

        $taxonAttributes = $conn->table('vicflora_taxonattribute as ta')
                ->join('vicflora_taxon as t', 'ta.TaxonID', '=', 't.TaxonID')
                ->where('ta.Attribute', 'VROT')
                ->select('t.guid as taxon_id', 'StrValue as value')
                ->get();

        foreach ($taxonAttributes as $attr) {
            TaxonAttribute::create([
                'attribute_id' => $vrot->id,
                'attribute_value_id' => AttributeValue::where('attribute_id', $vrot->id)
                        ->where('value', $attr->value)->value('id'),
                'taxon_concept_id' => TaxonConcept::where('guid', $attr->taxon_id)->value('id'),
                'created_by_id' => 1
            ]);
        }

        $ffg = Attribute::create([
            'guid' => Str::uuid(),
            'name' => 'FFG',
            'created_by_id' => 1
        ]);

        $ffgValues = $conn->table('vicflora_taxonattribute')
                ->where('Attribute', 'FFG')
                ->distinct()
                ->pluck('StrValue');

        foreach ($ffgValues as $val) {
            AttributeValue::create([
                'guid' => Str::uuid(),
                'attribute_id' => $ffg->id,
                'value' => $val,
                'created_by_id' => 1
            ]);
        }

        $taxonAttributes = $conn->table('vicflora_taxonattribute as ta')
                ->join('vicflora_taxon as t', 'ta.TaxonID', '=', 't.TaxonID')
                ->where('ta.Attribute', 'FFG')
                ->select('t.guid as taxon_id', 'StrValue as value')
                ->get();

        foreach ($taxonAttributes as $attr) {
            TaxonAttribute::create([
                'attribute_id' => $ffg->id,
                'attribute_value_id' => AttributeValue::where('attribute_id', $ffg->id)
                        ->where('value', $attr->value)->value('id'),
                'taxon_concept_id' => TaxonConcept::where('guid', $attr->taxon_id)->value('id'),
                'created_by_id' => 1
            ]);
        }
    }
}
