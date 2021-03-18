<?php

namespace App\Console\Commands;

use App\Models\Highlight;
use Illuminate\Console\Command;

class DataHighlights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:highlights';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create highlights records';

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
        Highlight::create([
            "img_src" => "https://vicflora.rbg.vic.gov.au/images/home/lucid-logo-icon-150.png",
            "text" => "<h5><b><span class=\"m-hightlight-new\">New</span> multi-access keys</b></h5>
                New multi-access keys to <a href=\"https://vicflora.rbg.vic.gov.au/static/keys/fabaceae\">Fabaceae</a> (excl. Acacia), 
                <a href=\"https://vicflora.rbg.vic.gov.au/static/keys/cyperaceae\">Cyperaceae</a> and 
                <a href=\"https://vicflora.rbg.vic.gov.au/static/keys/juncaceae\">Juncaceae</a> in Victoria."
        ]);

        Highlight::create([
            "img_src" => "https://vicflora.rbg.vic.gov.au/images/home/eucalcom.jpg",
            "text" => "<b><a href=\"https://vicflora.rbg.vic.gov.au/static/keys/eucalypts\">Multi-access key to the Eucalypts</a></b>
                <p>Check out our new multi-access key to the 159 species and infraspecific taxa of <i>Eucalyptus</i>, <i>Angophora</i> and <i>Corymbia</i> in Victoria.</p>"
        ]);

        Highlight::create([
            "img_src" => "https://vicflora.rbg.vic.gov.au/images/home/microwal.jpg",
            "text" => "<b><a href=\"https://vicflora.rbg.vic.gov.au/static/keys/asteraceae\">Multi-access key to the Asteraceae</a></b>
                <p>Check out our new multi-access key to the 618 species and infraspecific taxa of Asteraceae in Victoria.</p>"
        ]);
    }
}
