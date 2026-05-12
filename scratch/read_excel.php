<?php

require 'vendor/autoload.php';

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;

// We need to bootstrap Laravel to use the Excel facade if we want to use it this way,
// but it's easier to use the underlying PhpSpreadsheet library since we are running a standalone script.

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = '/home/hasanarofid/Documents/solkit/katering/FILE RESEP INDUK NITA JAYA CATERING SERVICE/RESEP INDUK/009 RESEP BUMBU DASAR.xlsx';
$sheetName = 'BUMBUNASGORXO';

try {
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getSheetByName($sheetName);
    
    if (!$sheet) {
        echo "Sheet '$sheetName' not found.\n";
        $names = $spreadsheet->getSheetNames();
        echo "Available sheets: " . implode(', ', $names) . "\n";
        exit(1);
    }

    $data = $sheet->toArray(null, true, true, true);

    foreach ($data as $row) {
        echo implode("\t", array_map(function($val) { return $val ?? ''; }, $row)) . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
