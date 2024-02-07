<?php

namespace App\Console\Commands;

use App\Actions\FindCurrentTaxonConcept;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class AdminImportThreatStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:import-ffg-epbc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $findTaxon = new FindCurrentTaxonConcept;
        $lists = [];
        $statuses = [];
        

        $filepath = storage_path('app/ffg/ffg_listed_taxa.xlsx');
        $reader = new Xlsx();
        $spreadsheet = $reader->load($filepath);
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet_arr = $worksheet->toArray();

        foreach ($worksheet_arr as $index=>$row) {
            $taxonName = $row[0];
            $ffg = $row[1];
            $epbc = $row[2];

            $tc = $findTaxon($taxonName);

            if ($tc) {
                if ($row[1]) {
                    if (in_array('ffg', array_keys($lists))) {
                        $listId = $lists['ffg'];
                    }
                    else {
                        $listId = DB::table('conservation_lists')
                            ->where('code', 'ffg')
                            ->value('id');
                        $lists['ffg'] = $listId;
                    }
                    if (in_array($row[1], array_keys($statuses))) {
                        $statusId = $statuses[$row[1]];
                    }
                    else {
                        $statusId = DB::table('iucn_categories')
                            ->where('code', $row[1])
                            ->value('id');
                        $statuses[$row[1]] = $statusId;
                    }

                    if ($statusId) {

                        try {
                            DB::table('taxon_concept_threat_statuses')->insert([
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'taxon_concept_id' => $tc['id'],
                                'conservation_list_id' => $listId,
                                'iucn_category_id' => $statusId,
                                'as' => $tc['as'],
                            ]);
                        }
                        catch(QueryException $e) {
                            $this->error($e->getMessage());
                        }
                    }
                }

                if ($row[2]) {
                    if (in_array('epbc', array_keys($lists))) {
                        $listId = $lists['epbc'];
                    }
                    else {
                        $listId = DB::table('conservation_lists')
                            ->where('code', 'epbc')
                            ->value('id');
                        $lists['epbc'] = $listId;
                    }
                    if (in_array($row[2], array_keys($statuses))) {
                        $statusId = $statuses[$row[2]];
                    }
                    else {
                        $statusId = DB::table('iucn_categories')
                            ->where('code', $row[2])
                            ->value('id');
                        $statuses[$row[2]] = $statusId;
                    }
                    DB::table('taxon_concept_threat_statuses')->insert([
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'taxon_concept_id' => $tc['id'],
                        'conservation_list_id' => $listId,
                        'iucn_category_id' => $statusId,
                        'as' => $tc['as'],
                    ]);
                }
            }
        }

        return Command::SUCCESS;
    }
}
