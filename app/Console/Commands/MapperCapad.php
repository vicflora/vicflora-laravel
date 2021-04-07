<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MapperCapad extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mapper:capad';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Loads occurrences in protected areas';

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
        $areas = DB::connection('mapper')->table('vicflora.capad_2012')
                ->where('state', 'VIC')
                ->select('gid', 'geom')
                ->get();

        foreach ($areas as $area) {
            $sql = <<<SQL
insert into vicflora.protected_area_occurrences (occurrence_id, protected_area_id)
select o.id, pa.gid 
from vicflora.occurrences o
join vicflora.capad_2012 pa on ST_Intersects(o.geom, ST_Transform(pa.geom, 4326))
where pa.gid={$area->gid}
SQL;


            DB::connection('mapper')->unprepared($sql);
        }
    }
}
