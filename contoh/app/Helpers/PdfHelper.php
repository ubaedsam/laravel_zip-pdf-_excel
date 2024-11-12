<?php

namespace App\Helpers;

use App\Models\ExcelData;
use App\Models\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfHelper
{
    public static function checkAndStoreData($pdfDirectory, $excelData)
    {
        foreach ($excelData as $row) {
            $idDipotong = $row[0]; // Asumsikan kolom pertama adalah ID_DIPOTONG
            $pdfName = $row[1]; // Asumsikan kolom kedua adalah nama file PDF

            // Ekstrak nomor dari nama file PDF
            $pdfNumber = self::extractNumberFromPdfName($pdfName);
            if (!$pdfNumber) {
                continue;
            }

            if ($idDipotong == $pdfNumber) {
                $pdfPath = $pdfDirectory . '/' . $pdfName;

                if (file_exists($pdfPath)) {
                    // Simpan file PDF ke database
                    $pdf = Pdf::create([
                        'name' => $pdfName,
                        'path' => $pdfPath,
                    ]);

                    // Simpan data Excel dan relasikan dengan file PDF
                    ExcelData::create([
                        'id_dipotong' => $idDipotong,
                        'pdf_name' => $pdfName,
                    ]);
                }
            }
        }
    }

    private static function extractNumberFromPdfName($pdfName)
    {
        // Asumsikan format nama file PDF: 9000000099_099999910085000_5046bedd-940c-4845-af0f-70sd5thu43fd2
        // Nomor berada di bagian awal nama file sebelum underscore pertama
        $parts = explode('_', $pdfName);
        return isset($parts[0]) ? $parts[0] : null;
    }
}
