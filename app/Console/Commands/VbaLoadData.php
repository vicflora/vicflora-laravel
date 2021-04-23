<?php

namespace App\Console\Commands;

use App\Models\TaxonName;
use App\Models\VbaTaxon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class VbaLoadData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vba:load-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads VBA taxon list';

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
        Artisan::call('vba:drop-table');
        Artisan::call('vba:create-table');


        $filePath = storage_path('app/data_vic/VBA_TAXA_LIST.csv');
        $taxonList = fopen($filePath, 'r');
        $headerRow = fgetcsv($taxonList);
        while (!feof($taxonList)) {
            $line = fgetcsv($taxonList);
            if ($line) {
                $row = [];
                foreach ($line as $key => $value) {
                    $row[$headerRow[$key]] = $value;
                }
                if ($row['DISCIPLINE'] == 'Flora') {
                    $vbaTaxon = [
                        'vba_id' => $row['TAXON_ID'],
                        'scientific_name' => $row['SCI_NAME'],
                        'common_name' => $row['COMM_NAME'] ?: null,
                        'authority' => $row['AUTHORITY'] ?: null,
                        'family' => $row['FAMILY'] ?: null,
                        'ffg' => $row['FFG'] ?: null,
                        'ffg_desc' => $row['FFG_DESC'] ?: null,
                        'epbc' => $row['EPBC'] ?: null,
                        'epbc_desc' => $row['EPBC_DESC'] ?: null,
                        'vic_adv' => $row['VICADV'] ?: null,
                        'vic_adv_desc' => $row['VICADV_DESC'] ?: null,
                        'restriction' => $row['RESTRICTION'] ?: null,
                        'origin' => $row['ORIGIN'] ?: null,
                        'taxon_type' => $row['TAXON_TYPE'] ?: null,
                        'vic_life_form' => $row['VIC_LF'] ?: null,
                        'fire_response' => $row['FIRE_RESP'] ?: null,
                        'nvis_growth_form' => $row['NVIS_GF'] ?: null,
                        'treaty' => $row['TREATY'] ?: null,
                        'discipline' => $row['DISCIPLINE'] ?: null,
                        'taxon_level' => $row['TAXON_LEVEL'] ?: null,
                        'fis_species_number' => $row['FIS_SPECNUM'] ?: null,
                        'record_modification_date' => $this->convertDateString($row['TAXON_MOD']),
                        'version_date' => $row['VERS_DATE'] ?: null,
                        'taxon_name_id' => $this->matchVbaNameString($row['SCI_NAME']),
                    ];
                    $taxon = new VbaTaxon($vbaTaxon);
                    $taxon->save();
                }
            }
        }
    }

    /**
     * Converts date string from 'dd/mm/yy' to 'yyyy-mm-dd'
     *
     * @param string $str
     * @return string|null
     */
    protected function convertDateString($str)
    {
        if ($str) {
            list ($day, $month, $year) = explode('/', $str);
            if ($year < 22) {
                $year = "20{$year}";
            }
            else {
                $year = "19{$year}";
            }
            return "{$year}-{$month}-{$day}";
        }
        return null;
    }

    /**
     * Matches VBA scientific name string to name in VicFlora
     *
     * @param string $str
     * @return string|null
     */
    protected function matchVbaNameString($str) 
    {
        $strToMatch = $str;
        if (preg_match('/s\.s\.$/', $str)) {
            $strToMatch = trim(substr($str, 0, strpos($str, 's.s.')));
        }
        $name = TaxonName::where('full_name', $strToMatch)->first();
        if ($name) {
            return $name->id;
        }
        return null;
    }


    protected function migrate()
    {

    }
}
