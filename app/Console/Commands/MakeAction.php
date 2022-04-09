<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class MakeAction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:action {action : Name of the action in pascal case}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new action class [VicFlora]';

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
        $action = $this->argument('action');
        $year = Carbon::today()->year;

        $text = <<<EOT
<?php
// Copyright {$year} Royal Botanic Gardens Board
// 
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
// 
//     http://www.apache.org/licenses/LICENSE-2.0
// 
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

namespace App\Actions;

class {$action} {
    
    public function __invoke()
    {
        return 0;
    }
}
EOT;
        $path = app_path("/Actions/{$action}.php");
        file_put_contents($path, $text);
        return 0;
    }
}
