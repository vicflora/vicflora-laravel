<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\EstablishmentMeans;
use App\Models\OccurrenceStatus;
use App\Models\Reference;
use App\Models\ReferenceType;
use App\Models\TaxonConcept;
use App\Models\TaxonName;
use App\Models\TaxonomicStatus;
use App\Models\TaxonTreeDefItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataMigrateTaxa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:taxa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load taxa';

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

        $taxa = $conn->table('vicflora_taxon as t')
                ->join('vicflora_name as n', 't.NameID', '=', 'n.NameID')
                ->leftJoin('vicflora_taxon as a', 't.AcceptedID', '=', 'a.TaxonID')
                ->leftJoin('vicflora_taxon as p', 't.ParentID', '=', 'p.TaxonID')
                ->leftJoin('users as cb', 't.CreatedByID', '=', 'cb.UsersID')
                ->leftJoin('users as mb', 't.ModifiedByID', '=', 'mb.UsersID')
                ->leftJoin('vicflora_taxontree as tt', 't.TaxonID', '=', 'tt.TaxonID')
                ->select('t.guid', 't.sensu',
                        DB::raw("coalesce(t.TaxonomicStatus, 'undefined') as taxonomic_status"),
                        't.OccurrenceStatus as occurrence_status',
                        't.EstablishmentMeans as establishment_means',
                        't.RankID as rank_id',
                        'n.guid as name_guid',
                        'a.guid as accepted_guid', 'p.guid as parent_guid',
                        'cb.Email as created_by', 'mb.Email as modified_by',
                        't.TimestampCreated as created_at',
                        't.TimestampModified as updated_at')
                ->whereNotNull('tt.TaxonTreeID')
                ->orWhere('t.TaxonomicStatus', '!=', 'accepted')
                ->orWhereNull('t.TaxonomicStatus')
                ->get();

        $undefined = TaxonTreeDefItem::where('name', 'undefined')->first();
        $undefined->rank_id = -9999;
        $undefined->save();

        ReferenceType::create([
            'name' => 'Sensu',
            'guid' => Str::uuid(),
            'label' => 'Sensu',
            'created_by_id' => 1
        ]);

        foreach ($taxa as $taxon) {
            if ($taxon->taxonomic_status) {
                $taxonomicStatus = TaxonomicStatus::where('name', Str::camel($taxon->taxonomic_status))
                        ->first();
                if (!$taxonomicStatus) {
                    TaxonomicStatus::create([
                        'guid' => Str::uuid(),
                        'name' => Str::camel($taxon->taxonomic_status),
                        'label' => Str::title($taxon->taxonomic_status),
                        'created_by_id' => 1
                    ]);
                }
            }

            if ($taxon->occurrence_status) {
                $occurrenceStatus = OccurrenceStatus::where('name', Str::camel($taxon->occurrence_status))
                        ->first();
                if (!$occurrenceStatus) {
                    OccurrenceStatus::create([
                        'guid' => Str::uuid(),
                        'name' => Str::camel($taxon->occurrence_status),
                        'label' => Str::title($taxon->occurrence_status),
                        'created_by_id' => 1
                    ]);
                }
            }

            if ($taxon->establishment_means) {
                $establishmentMeans = EstablishmentMeans::where('name', Str::camel($taxon->establishment_means))
                        ->first();
                if (!$establishmentMeans) {
                    EstablishmentMeans::create([
                        'guid' => Str::uuid(),
                        'name' => Str::camel($taxon->establishment_means),
                        'label' => Str::title($taxon->establishment_means),
                        'created_by_id' => 1
                    ]);
                }
            }

            if ($taxon->sensu) {
                $sensu = Reference::where('title', $taxon->sensu)->first();
                if (!$sensu) {
                    $sensu = Reference::create([
                        'guid' => Str::uuid(),
                        'title' => $taxon->sensu,
                        'reference_type_id' => ReferenceType::where('name', 'Sensu')->value('id'),
                        'created_by_id' => 1,
                    ]);
                }
            }

            TaxonConcept::create([
                'guid' => $taxon->guid,
                'according_to_id' => $taxon->sensu ? $sensu->id : null,
                'taxonomic_status_id' => $taxonomicStatus ? $taxonomicStatus->id : null,
                'occurrence_status_id' => $occurrenceStatus ? $occurrenceStatus->id : null,
                'establishment_means_id' => $establishmentMeans ? $establishmentMeans->id : null,
                'taxon_name_id' => TaxonName::where('guid', $taxon->name_guid)->value('id'),
                'rank_id' => $taxon->rank_id ?: -9999,
                'taxon_tree_def_item_id' => TaxonTreeDefItem::where('rank_id', $taxon->rank_id ?: -9999)->value('id'),
                'created_by_id' => $taxon->created_by ? Agent::where('email', $taxon->created_by)->value('id') : 1,
                'modified_by_id' => $taxon->modified_by ? Agent::where('email', $taxon->modified_by)->value('id') : null,
                'created_at' => $taxon->created_at ? $taxon->created_at . '+10' : now(),
                'updated_at' => $taxon->updated_at ? $taxon->updated_at . '+10' : now(),
            ]);
        }

        foreach ($taxa as $taxon) {
            $concept = TaxonConcept::where('guid', $taxon->guid)->first();
            $concept->parent_id = TaxonConcept::where('guid', $taxon->parent_guid)->value('id');
            $concept->accepted_id = $taxon->guid != $taxon->accepted_guid ?
                    TaxonConcept::where('guid', $taxon->accepted_guid)->value('id')
                    : null;
            $concept->save();
        }
    }
}
