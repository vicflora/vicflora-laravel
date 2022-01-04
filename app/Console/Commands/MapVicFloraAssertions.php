<?php

namespace App\Console\Commands;

use App\Models\Assertion;
use App\Models\Occurrence;
use App\Models\TermValue;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MapVicFloraAssertions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mapper:map-vicflora-assertions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import VicFlora assertions, map catalogue number to new UUID';

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
        $agent_map = [
            1 => 1,
            2 => 2,
            3 => 3,
            7 => 4,
            8 => 5
        ];

        $notFound = fopen(storage_path('app/assertions_vicflora_catalog_numbers_not found.csv'), 'w');

        $handle = fopen(storage_path('app/vicflora_assertions.csv'), 'r');
        $firstRow = fgetcsv($handle);
        while (!feof($handle)) {

            $row = [];
            $line = fgetcsv($handle);
            if ($line) {
                foreach ($line as $key => $value) {
                    $row[$firstRow[$key]] = $value ?: null;
                }
                if (is_numeric($row['catalog_number'])) {
                    $row['catalog_number'] = floor($row['catalog_number']);
                }
                if ($row['asserted_value']) {
                    // $occurrence = Occurrence::where('catalog_number', $row['catalog_number'])->first();
                    $occurrence = Occurrence::whereRaw("replace(catalog_number, ' ', '')='" . str_replace(' ', '', $row['catalog_number']) . "'")
                            ->first();

                    if ($occurrence) {
                        $this->info($occurrence->uuid);
                        $term_value = TermValue::where('value', $row['asserted_value'])->first();

                        if (!$term_value) {
                            $fill = [
                                'term_id' => $row['term'] === 'occurrenceStatus' ? 1 : 2,
                                'value' => $row['asserted_value'],
                                'label' => Str::title($row['asserted_value']),
                            ];
                            $term_value = TermValue::create($fill);
                        }

                        Assertion::create([
                            'occurrence_id' => $occurrence->uuid,
                            'term_id' => $term_value->term_id,
                            'term_value_id' => $term_value->id,
                            'reason' => $row['reason'],
                            'remarks' => $row['comment'],
                            'created_at' => $row['timestamp_modified'],
                            'updated_at' => $row['timestamp_modified'],
                            'agent_id' => isset($row['user_id']) ? $agent_map[$row['user_id']] : null,
                            'assertion_source_id' => 1
                        ]);
                    }
                    else {
                        $this->info('Not found: ' . $row['catalog_number']);
                        fputcsv($notFound, [$row['catalog_number']]);
                    }

                }
            }
        }
        fclose($handle);
        fclose($notFound);
    }
}
