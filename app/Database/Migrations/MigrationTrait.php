<?php

/*
 * Copyright 2021 Royal Botanic Gardens Victoria.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Database\Migrations;

use Illuminate\Support\Facades\DB;

/**
 * Description of MigrationTrait
 *
 * @author Niels.Klazenga <Niels.Klazenga at rbg.vic.gov.au>
 */
trait MigrationTrait 
{
    protected function setGlobalSequence()
    {
        $setGlobalSequence = "ALTER TABLE {$this->tableName} ALTER COLUMN id "
                . "SET DEFAULT nextval('vicflora_global_seq')";
        DB::unprepared($setGlobalSequence);
        $dropLocalSequence = "DROP SEQUENCE IF EXISTS {$this->tableName}_id_seq";
        DB::unprepared($dropLocalSequence);
    }
}