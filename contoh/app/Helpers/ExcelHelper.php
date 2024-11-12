<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelHelper
{
    public static function readExcelFile($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        return $sheetData;
    }
}
