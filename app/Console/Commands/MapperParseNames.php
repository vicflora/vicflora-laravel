<?php

namespace App\Console\Commands;

use App\Services\ParseNameService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MapperParseNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mapper:parse-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse new name strings using the GBIF name parser and update rcord in mapper.parsed_names table';

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
        $service = new ParseNameService();

        $max = DB::table('mapper.parsed_names')->where('type', 'UNPARSED')->count();
        $bar = $this->output->createProgressBar($max);

        $names = DB::table('mapper.parsed_names')
            ->where('type', 'UNPARSED')
            ->select('id', 'scientific_name')
            ->get();

        foreach ($names as $name) {
            $parsedName = $service->getParsedNameFromService($name->scientific_name);
            if (isset($parsedName->canonicalName)) {
                $matchedName = $service->matchParsedName($parsedName);
                $service->updateUnparsedName($name->id, $parsedName, $matchedName);
            }
            $bar->advance();
        }
        $bar->finish();
        $this->info('Done');
    }
}
