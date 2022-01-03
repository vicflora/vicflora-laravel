<?php

namespace App\Console\Commands;

use App\Models\Assertion;
use App\Models\Occurrence;
use App\Models\TermValue;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use SplFileObject;

class ImportAlaAssertions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mapper:import-ala-assertions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import AVH assertions, matching by catalogue numbers';

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
        $term_values = TermValue::whereHas('term', function (Builder $query) {
            $query->where('iri', 'https://rs.tdwg.org/dwc/terms/establishmentMeans');
        })->get();

        $establishment_means = [];
        foreach($term_values as $val) {
            $establishment_means[$val->value] = $val->id;
        }

        $file = new SplFileObject(storage_path('app/ala/assertions_avh.csv'), 'r');
        $file->seek(PHP_INT_MAX);
        $max = $file->key();
        $file->rewind();

        $firstRow = $file->fgetcsv();

        $bar = $this->output->createProgressBar($max);

        while (!$file->eof()) {
            $bar->advance();
            $row = [];
            $line = $file->fgetcsv();
            if ($line) {
                foreach ($line as $key => $value) {
                    $row[$firstRow[$key]] = $value ?: null;
                }

                $occurrence = Occurrence::find($row['uuid']);

                if ($occurrence) {
                    Assertion::create([
                        'occurrence_id' => $occurrence->uuid,
                        'term_id' => 2,
                        'term_value_id' => $establishment_means[$row['establishment_means']],
                        'assertion_source_id' => 2
                    ]);

                }
            }
        }
        $bar->finish();
        $this->info("\nDone");


    }
}
