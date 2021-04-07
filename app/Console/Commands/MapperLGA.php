<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MapperLGA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mapper:lga';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Incorporate Local Government Areas';

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
        // Schema::connection('mapper')->table('vicflora.occurrence_attribute', function (Blueprint $table) {
        //     $table->dropColumn('lga');
        //     $table->integer('lga_gid')->nullable();
        //     $table->string('lga_pid', 32)->nullable();
        //     $table->string('lga_name', 64)->nullable();
        // });

        $lgas = DB::connection('mapper')
                ->table('vicflora.local_government_areas_view')
                ->select('gid', 'lga_pid', 'lga_name', 'geom')
                ->get();

        foreach ($lgas as $lga) {
            $lga->lga_name = Str::title($lga->lga_name);
            
            $uuids = DB::connection('mapper')
                    ->table('vicflora.avh_occurrence')
                    ->whereRaw("ST_Intersects(geom, '{$lga->geom}')")
                    ->pluck('uuid');

            foreach ($uuids as $uuid) {
                DB::connection('mapper')
                        ->table('vicflora.occurrence_attribute')
                        ->where('uuid', $uuid)
                        ->update([
                            'lga_gid' => $lga->gid,
                            'lga_pid' => $lga->lga_pid,
                            'lga_name' => $lga->lga_name
                        ]);
            }
        }

        Schema::connection('mapper')->table('vicflora.occurrence_attribute', function (Builder $table) {
            $table->index('lga_gid');
        });
    }
}
