<?php

namespace App\Console\Commands;

use App\Actions\ConvertCollectionToSpreadsheet;
use App\Actions\GetConservationList;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminExportConservationList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:export-conservation-list {--list=ffg : code for the conservation list}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export a conservation list';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $getList = new GetConservationList;
        $convert = new ConvertCollectionToSpreadsheet;

        $list = $getList($this->option('list'));

        $spreadsheet = $convert($list);
        $spreadsheet->getActiveSheet()
            ->setTitle(strtoupper($this->option('list')));

        $writer = new Xlsx($spreadsheet);
        $filename = "conservation-list-{$this->option('list')}.xlsx";
        $filePath = storage_path("app/ffg/$filename");
        $writer->save($filePath);

        return Command::SUCCESS;
    }
}
