<?php

namespace App\Console\Commands;

use App\Services\MapOccurrencesService;
use Illuminate\Console\Command;
use SplFileObject;

class ProcessDownloadedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ala:process-downloaded-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $field_names = $this->getFieldNames();
        $this->info(json_encode($field_names));

        $service = new MapOccurrencesService;

        $file = new \SplFileObject(storage_path('app/ala/avh_data/data.csv'), 'r');
        $file->seek(PHP_INT_MAX);
        $max = $file->key();

        $this->info('Processing...');
        $start = microtime(true);

        $bar = $this->output->createProgressBar($max);

        $file->rewind();
        $file->fgetcsv();
        while (!$file->eof()) {
            $line = $file->fgetcsv();
            $row= [] ;
            if ($line) {
                foreach ($line as $key => $value) {
                    $row[$field_names[$key]] = $value ?: null;
                }

                if (isset($row['raw_scientificName']) && $row['raw_scientificName']) {
                    $service->loadOccurrence((object) $row);
                }
                $bar->advance();
            }
        }
        $bar->finish();

        $this->info('Done');
        $end = microtime(true);
        $this->info('Execution time: ' . ceil(($end - $start)/60) . ' minutes');
    }


    protected function getFieldNames()
    {
        $headingsFile = fopen(storage_path('app/ala/avh_data/headings.csv'), 'r');
        $columns = [];
        $firstRow = fgetcsv($headingsFile);
        $key = array_search('Field name', $firstRow);
        while (!feof($headingsFile)) {
            $row = fgetcsv($headingsFile);
            if (isset($row[$key])) {
                $columns[] = $row[$key];
            }
        }
        fclose($headingsFile);
        return $columns;
    }
}
