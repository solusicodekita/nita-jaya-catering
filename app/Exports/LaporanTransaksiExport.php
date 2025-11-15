<?php

namespace App\Exports;

use App\Models\LaporanTransaksi;
use App\Models\Stock;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

class LaporanTransaksiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    protected $stock;
    protected $no = 1;
    protected $bulan;
    protected $tahun;
    protected $tglAwal;
    protected $tglAkhir;
    protected $totalStokAwal = 0;
    protected $totalHargaStokAwal = 0;
    protected $totalStokMasuk = 0;
    protected $totalHargaStokMasuk = 0;
    protected $totalStokKeluar = 0;
    protected $totalHargaStokKeluar = 0;
    protected $totalStokAkhir = 0;
    protected $totalHargaStokAkhir = 0;
    protected $jumlahData;
    protected $preview;

    public function __construct($bulan, $tahun, $tglAwal, $tglAkhir, $preview = null)
    {   
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->tglAwal = $tglAwal;
        $this->tglAkhir = $tglAkhir;
        $this->stock = Stock::select('item_id', 'warehouse_id')
            ->join('items', 'stocks.item_id', '=', 'items.id')
            ->orderBy('warehouse_id')
            ->groupBy('item_id', 'warehouse_id')->get();
        $this->jumlahData = $this->stock->count();
        $this->preview = $preview;
    }

    public function collection()
    {
        $data = $this->stock;
        $data->push([]);
        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Barang',
            'Unit',
            'Harga',
            'Stok Awal',
            'Jumlah Stok Awal',
            'Masuk',
            'Jumlah Masuk',
            'Keluar',
            'Jumlah Keluar',
            'Stok Akhir',
            'Jumlah Stok Akhir',
            'Lokasi Item',
        ];
    }
    
    public function map($stock): array
    {
        if ($this->no > $this->jumlahData) {
            $row = [
                'Total',
                '',
                '',
                '',
                '',
                $this->totalHargaStokAwal,
                '',
                $this->totalHargaStokMasuk, 
               '',
                $this->totalHargaStokKeluar,
                '',
                $this->totalHargaStokAkhir,
                '',
            ];
            return $row;
        }
        $modelStokAwal = Stock::where('item_id', $stock->item_id)
            ->where('warehouse_id', $stock->warehouse_id)
            // ->whereMonth('date_opname', '09') // Jika ingin filter bulan tertentu
            ->latest('date_opname')
            ->first();

        if ($modelStokAwal) {
            $stokAwal = $modelStokAwal->final_stock;
        } else {
            $stokAwal = 0;
        }
        
        $hargaTertinggi = LaporanTransaksi::hargaTertinggi($stock->item_id, $stock->warehouse_id, $this->tglAwal, $this->tglAkhir);
        
        
        $masuk = LaporanTransaksi::transaksiMasuk($stock->item_id, $stock->warehouse_id, $this->tglAwal, $this->tglAkhir);
        $keluar = LaporanTransaksi::transaksiKeluar($stock->item_id, $stock->warehouse_id, $this->tglAwal, $this->tglAkhir);
        $stokAkhir = $stokAwal + $masuk - $keluar;

        $totalHargaStokAwal = $hargaTertinggi * $stokAwal;
        $totalHargaStokMasuk = $hargaTertinggi * $masuk;
        $totalHargaStokKeluar = $hargaTertinggi * $keluar;
        $totalHargaStokAkhir = $hargaTertinggi * $stokAkhir;

        $this->totalStokAwal += $stokAwal;
        $this->totalHargaStokAwal += $totalHargaStokAwal;
        $this->totalStokMasuk += $masuk;
        $this->totalHargaStokMasuk += $totalHargaStokMasuk;
        $this->totalStokKeluar += $keluar;
        $this->totalHargaStokKeluar += $totalHargaStokKeluar;
        $this->totalStokAkhir += $stokAkhir;
        $this->totalHargaStokAkhir += $totalHargaStokAkhir;

        if (empty($this->preview)) {
            # update harga stok
            $modStock = new Stock();
            $modStock->item_id = $stock->item_id;
            $modStock->warehouse_id = $stock->warehouse_id;
            $modStock->initial_stock = $stokAwal;
            $modStock->final_stock = $stokAkhir;
            $modStock->date_opname = date('Y-m-d 23:59:59', strtotime($this->tglAkhir));
            $modStock->created_by = auth()->user()->id;
            $modStock->updated_by = auth()->user()->id;
            $modStock->is_laporan = 1;
            $modStock->save();
        }

        $row = [
            $this->no++,
            $stock->item->name,
            $stock->item->unit,
            $hargaTertinggi,
            $stokAwal,
            $totalHargaStokAwal,
            ($masuk == 0 ? '0' : $masuk),
            ($masuk == 0 ? '0' : $totalHargaStokMasuk),
            ($keluar == 0 ? '0' : $keluar),
            ($keluar == 0 ? '0' : $totalHargaStokKeluar),
            ($stokAkhir == 0 ? '0' : $stokAkhir),
            ($stokAkhir == 0 ? '0' : $totalHargaStokAkhir),
            $stock->warehouse->name,
        ];

        return $row;
    }

    public function columnFormats(): array
    {
        return [
            'D' => '#,##0_-',
            'F' => '#,##0_-',
            'H' => '#,##0_-',
            'J' => '#,##0_-',
            'L' => '#,##0_-',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastRow = $this->jumlahData + 2; // +2 for header row and 0-based index
                
                // Merge cells A to D for TOTAL
                $event->sheet->mergeCells("A{$lastRow}:D{$lastRow}");
                
                // Set bold font and center alignment for TOTAL
                $event->sheet->getStyle("A{$lastRow}:L{$lastRow}")->applyFromArray([
                    'font' => [
                        'bold' => true
                    ],
                ]);

                // Set bold font and center alignment for TOTAL
                $event->sheet->getStyle("A{$lastRow}:D{$lastRow}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT
                    ],
                ]);

                // Add borders to all cells
                $event->sheet->getStyle("A1:M{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }
        ];
    }
}
