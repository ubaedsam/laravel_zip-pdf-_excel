<?php

namespace App\Helpers;

use ZipArchive;

class ZipHelper
{
    public static function extractZip($zipFilePath, $extractTo)
    {
        $zip = new ZipArchive;

        if ($zip->open($zipFilePath) === TRUE) {
            $zip->extractTo($extractTo);
            $zip->close();
            return true;
        }
        return false;
    }
}
