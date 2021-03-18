<?php

namespace App\Console\Commands;

use App\Models\CarouselSlide;
use Illuminate\Console\Command;

class DataCarouselSlides extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:carousel-slides';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create carousel slides records';

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
        CarouselSlide::create([
            'img_src' => 'https://data.rbg.vic.gov.au/cip/preview/image/public/4?maxsize=600',
            'caption' => 'Illustration: Barley, Anita. ©2021 Royal Botanic Gardens Victoria. CC BY-NC-SA 4.0.'
        ]);

        CarouselSlide::create([
            'img_src' => "https://data.rbg.vic.gov.au/cip/preview/image/public/20?maxsize=600",
            'caption' => "Illustration: Barley, Anita. ©2021 Royal Botanic Gardens Victoria. CC BY-NC-SA 4.0."
        ]);

        CarouselSlide::create([
            'img_src' => "https://data.rbg.vic.gov.au/cip/preview/image/public/12?maxsize=600",
            'caption' => "Illustration: Barley, Anita. ©2021 Royal Botanic Gardens Victoria. CC BY-NC-SA 4.0."
        ]);

        CarouselSlide::create([
            'img_src' => "https://data.rbg.vic.gov.au/cip/preview/image/public/14?maxsize=600",
            'caption' => "Illustration: Barley, Anita. ©2021 Royal Botanic Gardens Victoria. CC BY-NC-SA 4.0."
        ]);

        CarouselSlide::create([
            'img_src' => "https://data.rbg.vic.gov.au/cip/preview/image/public/2?maxsize=600",
            'caption' => "Illustration: Barley, Anita. ©2021 Royal Botanic Gardens Victoria. CC BY-NC-SA 4.0."
        ]);

        CarouselSlide::create([
            'img_src' => "https://data.rbg.vic.gov.au/cip/preview/image/public/6?maxsize=600",
            'caption' => "Illustration: Barley, Anita. ©2021 Royal Botanic Gardens Victoria. CC BY-NC-SA 4.0."
        ]);
    }
}
