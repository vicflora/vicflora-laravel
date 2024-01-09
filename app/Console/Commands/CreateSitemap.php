<?php

namespace App\Console\Commands;

use DOMDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vicflora:create-sitemap';

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
        $this->info('Creating site map...');

        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->load('https://raw.githubusercontent.com/vicflora/vicflora-content/main/sitemap.xml');
        $urlSet = $doc->documentElement;

        $updatedProfiles = DB::table('profiles')->where('version', '>', 1)
                ->distinct(['taxon_concept_id'])
                ->select('taxon_concept_id', 'version', 'updated_at')
                ->orderBy('taxon_concept_id')
                ->orderBy('version', 'desc');

        $concepts = DB::table('taxon_concepts as tc')
                ->leftJoinSub($updatedProfiles, 'p', function ($join) {
                    $join->on('tc.id', '=', 'p.taxon_concept_id');
                })
                ->select('tc.guid', DB::raw('date(coalesce(p.updated_at, tc.updated_at)) as updated_at'))
                ->get();

        foreach ($concepts as $concept) {
            $url = $doc->createElement('url');
            $loc = $doc->createElement('loc', "https://vicflora.rbg.vic.gov.au/flora/taxon/" . $concept->guid);
            $lastmod = $doc->createElement('lastmod', $concept->updated_at);
            $url->appendChild($loc);
            $url->appendChild($lastmod);
            $urlSet->appendChild($url);
        }

        file_put_contents(public_path('sitemap.xml'), $doc->saveXML());

        $this->info('Site map has been saved to ' . public_path('sitemap.xml'));

        return Command::SUCCESS;
    }
}
