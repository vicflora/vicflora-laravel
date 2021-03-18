<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DataMigrateReferencesCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:references:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup references in MySQL database';

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

        
        $conn->unprepared("update vicflora_profile 
                set SourceID=null
                where SourceID not in (select ReferenceID from vicflora_reference)");

        $conn->unprepared("update vicflora_reference r
                left join vicflora_name n on r.ReferenceID=n.ProtologueID
                left join vicflora_profile p on r.ReferenceID=p.SourceID
                left join vicflora_taxon_reference tr on r.ReferenceID=tr.ReferenceID
                set r.ReferenceType='MISSING'
                where n.NameID is null and p.ProfileID is null and tr.TaxonReferenceID is null");

        $conn->unprepared("update vicflora_reference r
                join vicflora_name n on r.ReferenceID=n.ProtologueID
                set r.ReferenceType='Protologue'");

        $conn->unprepared("update vicflora_name n
                join vicflora_reference r on n.ProtologueID=r.ReferenceID
                set n.ProtologueID=null
                where r.Title is null and r.JournalOrBook is null");

        $conn->unprepared("delete from vicflora_reference 
                where ReferenceType='Protologue' and coalesce(JournalOrBook, Title) is null");
                
        $conn->unprepared("update vicflora_reference 
                set ReferenceType='Chapter'
                where ReferenceType is null AND InPublicationID is not null");

        $conn->unprepared("update vicflora_reference 
                set ReferenceType='Article'
                where ReferenceType is null and JournalOrBook is not null");

        $conn->unprepared("update vicflora_reference
                set ReferenceType='WebSite'
                where GUID='a996a99a-37ea-443f-8a44-f81d408642cd'");

        $conn->unprepared("update vicflora_reference 
                set ReferenceType='Book'
                where ReferenceType is null");

        $conn->unprepared("update vicflora_reference
                set ReferenceType='SoftwareApplication'
                where GUID='bc2d8c88-a593-4eaa-86b2-d19c312f5da5'");

        $conn->unprepared("update vicflora_reference r
                join vicflora_reference p on r.InPublicationID=p.ReferenceID
                set p.ReferenceType='Book'");

        $conn->unprepared("update vicflora_reference set Page=trim(replace(Page, '–', '-'))");
    }
}
