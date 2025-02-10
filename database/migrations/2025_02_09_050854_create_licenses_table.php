<?php

use App\Database\Migrations\MigrationTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use MigrationTrait;

    protected $tableName = 'licenses';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('label');
            $table->string('license_url')->nullable();
            $table->string('license_logo_url')->nullable();
        });

        $this->setGlobalSequence();

        DB::table('licenses')->insert([
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'cc0',
                'label' => 'CC0',
                'license_url' => 'https://creativecommons.org/publicdomain/zero/1.0/',
                'license_logo_url' => 'https://mirrors.creativecommons.org/presskit/buttons/88x31/svg/cc-zero.svg'
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'cc-by',
                'label' => 'CC BY 4.0',
                'license_url' => 'https://creativecommons.org/licenses/by/4.0/',
                'license_logo_url' => 'https://mirrors.creativecommons.org/presskit/buttons/88x31/svg/by.svg'
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'cc-by-nc',
                'label' => 'CC BY-NC 4.0',
                'license_url' => 'https://creativecommons.org/licenses/by-nc/4.0/',
                'license_logo_url' => 'https://mirrors.creativecommons.org/presskit/buttons/88x31/svg/by-nc.svg'
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'cc-by-nc-nd',
                'label' => 'CC BY-NC-ND 4.0',
                'license_url' => 'https://creativecommons.org/licenses/by-nc-nd/4.0/',
                'license_logo_url' => 'https://mirrors.creativecommons.org/presskit/buttons/88x31/svg/by-nc-nd.svg'
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'cc-by-nc-sa',
                'label' => 'CC BY-NC-SA 4.0',
                'license_url' => 'https://creativecommons.org/licenses/by-nc-sa/4.0/',
                'license_logo_url' => 'https://mirrors.creativecommons.org/presskit/buttons/88x31/svg/by-nc-sa.svg'
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'cc-by-nd',
                'label' => 'CC BY-ND 4.0',
                'license_url' => 'https://creativecommons.org/licenses/by-nd/4.0/',
                'license_logo_url' => 'https://mirrors.creativecommons.org/presskit/buttons/88x31/svg/by-nd.svg'
            ],
            [
                'created_at' => date('Y-m-d H:i:s'),
                'name' => 'cc-by-sa',
                'label' => 'CC BY-SA 4.0',
                'license_url' => 'https://creativecommons.org/licenses/by-sa/4.0/',
                'license_logo_url' => 'https://mirrors.creativecommons.org/presskit/buttons/88x31/svg/by-sa.svg'
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
};
