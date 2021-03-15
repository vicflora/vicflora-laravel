<?php

namespace App\Console\Commands;

use App\Models\ReferenceType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataMigrateReferencesReferenceTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:references:reference-types';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load reference types';

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

        $protologues = $conn->table('vicflora_name')
                ->whereNotNull('ProtologueID')
                ->distinct()
                ->pluck('ProtologueID');

        $conn->table('vicflora_reference')
                ->whereIn('ReferenceID', $protologues)
                ->whereNull('ReferenceType')
                ->update(['ReferenceType' => 'Protologue']);

        $ref_types = $conn->table('vicflora_reference')
            ->whereNotNull('ReferenceType')
            ->where('ReferenceType', '!=', 'MISSING')
            ->distinct()
            ->pluck('ReferenceType');
        
        foreach ($ref_types as $ref_type) {
            ReferenceType::create([
                'name' => $ref_type,
                'label' => Str::title(Str::snake($ref_type)),
                'guid' => Str::uuid(),
                'created_by_id' => 1,
            ]);
        }

        ReferenceType::create([
            'name' => 'Journal',
            'label' => 'Journal',
            'guid' => Str::uuid(),
            'created_by_id' => 1,
        ]);

        ReferenceType::create([
            'name' => 'BookSeries',
            'label' => 'Book Series',
            'guid' => Str::uuid(),
            'created_by_id' => 1,
        ]);

        ReferenceType::create([
            'name' => 'WebPage',
            'label' => 'Web Page',
            'guid' => Str::uuid(),
            'created_by_id' => 1,
        ]);
    }
}
