<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanOpnameExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    protected $data;
    protected $meta;
    protected $no = 1;

    public function __construct($data, $meta)
    {
        $this->data = collect($data);
        $this->meta = $meta;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            ['LAPORAN STOK OPNAME - NITA JAYA CATERING'],
            ['Periode: ' . $this->meta['periode_label'] . ' (' . $this->meta['tgl_awal'] . ' s/d ' . $this->meta['tgl_akhir'] . ')'],
            ['Gudang: ' . $this->meta['warehouse_label'] . ' | Dicetak: ' . date('d-m-Y H:i:s')],
            [],
            [
                'No',
                'Kode Item',
                'Nama Item',
                'Kategori',
                'Lokasi Gudang',
                'Satuan',
                'Stok Awal',
                'Total Masuk',
                'Total Keluar',
                'Stok Akhir Sistem',
                'Stok Fisik',
                'Selisih',
                'Keterangan'
            ]
        ];
    }

    public function map($row): array
    {
        return [
            $this->no++,
            $row['item_code'] ?? '-',
            $row['item_name'],
            $row['category_name'] ?? '-',
            $row['warehouse_name'],
            $row['unit'],
            $row['stok_awal'],
            $row['masuk'],
            $row['keluar'],
            $row['stok_akhir'],
            '', // Kolom Fisik untuk diisi audit/print
            '', // Kolom Selisih
            ''  // Kolom Keterangan
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '#,##0.00',
            'H' => '#,##0.00',
            'I' => '#,##0.00',
            'J' => '#,##0.00',
            'K' => '#,##0.00',
            'L' => '#,##0.00',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $highestRow = $sheet->getHighestRow();

                // Judul
                $sheet->mergeCells('A1:M1');
                $sheet->mergeCells('A2:M2');
                $sheet->mergeCells('A3:M3');

                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A2:A3')->getFont()->setSize(10)->setItalic(true);

                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Header tabel baris 5
                $sheet->getStyle('A5:M5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1B3B6F'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Border seluruh data tabel
                if ($highestRow >= 5) {
                    $sheet->getStyle('A5:M' . $highestRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'D0D5DD'],
                            ],
                        ],
                    ]);
                }
            },
        ];
    }
}
