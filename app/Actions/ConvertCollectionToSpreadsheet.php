<?php
// Copyright 2024 Royal Botanic Gardens Board
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

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Convert query result collection to spreadsheet that can be saved to an XSLX 
 * file
 */
class ConvertCollectionToSpreadsheet
{

    /**
     * Convert collection to spreadsheet
     *
     * @param Collection $collection
     * @return Spreadsheet
     */
    public function __invoke(Collection $collection): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $worksheet = $spreadsheet->getActiveSheet();

        foreach ($collection as $index => $row) {
            if (!$index) {
                $worksheet->fromArray(array_keys((array) $row));
            }
            $worksheet->fromArray(
                array_values((array) $row),
                null,
                'A' . ($index + 2)
            );
        }

        // get last column
        $lastColumn = $worksheet->getHighestColumn();

        // set styles for header row 
        $styleArrayFirstRow = [
            'font' => [
                'bold' => 'true',
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'CCCCCC',
                ],
            ],
        ];
        $worksheet->getStyle('A1:' . $lastColumn . '1')
            ->applyFromArray($styleArrayFirstRow);

        // autoresize columns
        $lastColumn++;
        for ($column = 'A'; $column != $lastColumn; $column++) {
            $worksheet->getColumnDimension($column)->setAutoSize(true);
        }

        // freeze first row
        $worksheet->freezePane('A2');

        // set auto-filter
        $worksheet->setAutoFilter($worksheet->calculateWorksheetDimension());

        return $spreadsheet;
    }
}
