<?php

namespace App\Http\Controllers;


use App\Helpers\ZipHelper;
use App\Helpers\ExcelHelper;
use App\Helpers\PdfHelper;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use RarArchive;
use RarEntry;
// use Illuminate\Support\Facades\Storage;
// use ZipArchive;
// use Maatwebsite\Excel\Facades\Excel;
use App\Models\PdfFile;
use Illuminate\Support\Facades\File;

class PdfController extends Controller
{
    // public function uploadZip(Request $request)
    // {
    //     $request->validate([
    //         'excel' => 'required|file'
    //     ]);

    //     $file = $request->file('excel');

    //     $data = [];

    //     $dataExcel = Excel::toArray(null, $file);
    //     $column = [];

    //     foreach ($dataExcel[0] as $key => $excel) {

    //         if ($key == 0) {
    //             foreach ($excel as $key => $value) {
    //                 $column[strtolower($value)] = $key;
    //             }

    //             continue;
    //         }

    //         if ($excel[$column['no_bukti_potong']] == '') {
    //             continue;
    //         }

    //         $data[] = [
    //             'no_bukti_potong' => $excel[$column['no_bukti_potong']],
    //             // 'nama' => $excel[$column['nama']],
    //         ];
    //     }

    //     dd($data);

    //     return response()->json([
    //         'success' => 1,
    //         'message' => 'File uploaded successfully',
    //         'data' => $data
    //     ]);
    // }

    public function uploadZip(Request $request)
    {
        $request->validate([
            'rar' => 'required|file|mimes:zip',
            'excel' => 'required|file|mimes:xls,xlsx,ods'
        ]);

        $zipFile = $request->file('rar');
        $excelFile = $request->file('excel');

        // Ekstrak file ZIP
        $extractedPath = $this->extractZip($zipFile);

        // Proses file Excel
        $data = $this->processExcel($excelFile);

        // Simpan file PDF yang cocok dan tampilkan data
        $this->savePdfFiles($extractedPath, $data);

        return response()->json([
            'success' => true,
            'message' => 'Files uploaded and processed successfully',
            'data' => $data
        ]);
    }

    private function extractZip($zipFile)
    {
        $extractPath = storage_path('app/extracted_files');

        if (!File::exists($extractPath)) {
            File::makeDirectory($extractPath, 0755, true);
        }

        $zip = new ZipArchive;
        $res = $zip->open($zipFile);
        if ($res === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
            return $extractPath;
        } else {
            throw new \Exception('Failed to extract ZIP file');
        }
    }

    private function processExcel($excelFile)
    {
        $data = [];
        $dataExcel = Excel::toArray(null, $excelFile);
        $column = [];

        foreach ($dataExcel[0] as $key => $excel) {
            if ($key == 0) {
                foreach ($excel as $key => $value) {
                    $column[strtolower($value)] = $key;
                }
                continue;
            }

            if ($excel[$column['no_bukti_potong']] == '') {
                continue;
            }

            $data[] = [
                'no_bukti_potong' => str_replace(' ', '', $excel[$column['no_bukti_potong']]),
            ];
        }

        return $data;
    }

    private function savePdfFiles($extractedPath, $data)
    {
        $pdfFiles = File::files($extractedPath);
        $matchedFiles = [];

        // Pastikan folder publik ada
        if (!File::exists(public_path('pdf_files'))) {
            File::makeDirectory(public_path('pdf_files'), 0755, true);
        }

        foreach ($pdfFiles as $file) {
            $fileName = $file->getFilename();
            $idDipotong = explode('_', $fileName)[0];

            foreach ($data as $entry) {
                if ($entry['no_bukti_potong'] == $idDipotong) {
                    $publicPath = public_path('pdf_files/' . $fileName);

                    // Salin file ke folder publik
                    File::copy($file->getPathname(), $publicPath);

                    $matchedFiles[] = [
                        'no_bukti_potong' => $entry['no_bukti_potong'],
                        'file_path' => 'pdf_files/' . $fileName,
                    ];

                    // Simpan ke database
                    PdfFile::create([
                        'no_bukti_potong' => $entry['no_bukti_potong'],
                        'file_path' => $fileName,
                    ]);
                }
            }
        }

        // Tampilkan data yang cocok sebelum disimpan ke database
        dd($matchedFiles);
    }

    // public function contoh()
    // {
    //     $request->validate([
    //         'file' => 'required|file|mimes:zip|max:10240', // 10 MB max
    //     ]);

    //     $file = $request->file('file');
    //     $zipFileName = $file->getClientOriginalName();
    //     $zipPath = $file->storeAs('public/uploads', $zipFileName);

    //     $extractTo = storage_path('app/public/extracted/' . pathinfo($zipFileName, PATHINFO_FILENAME));

    //     // Ekstrak ZIP
    //     if (ZipHelper::extractZip(storage_path('app/' . $zipPath), $extractTo)) {
    //         $excelFilePath = $extractTo . '/sample.xlsx';
    //         $excelData = ExcelHelper::readExcelFile($excelFilePath);
    //         PdfHelper::checkAndStoreData($extractTo, $excelData);
    //         return response()->json(['message' => 'Data and PDF files have been processed and saved to the database.']);
    //     }

    //     return response()->json(['message' => 'Failed to extract ZIP file.'], 500);
    // }
}
