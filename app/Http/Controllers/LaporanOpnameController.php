<?php

namespace App\Http\Controllers;

use App\Exports\LaporanOpnameExport;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\LaporanTransaksi;
use App\Models\Stock;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanOpnameController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $currentDay = (int)date('d');
        $defaultPeriode = $currentDay <= 15 ? '1' : '2';
        $periode = $request->get('periode', $defaultPeriode);
        $warehouseId = $request->get('warehouse_id', 'all');

        [$tglAwal, $tglAkhir, $periodeLabel] = $this->resolveDateRange($bulan, $tahun, $periode, $request);

        $warehouses = Warehouse::orderBy('name', 'asc')->get();
        $reportData = $this->generateReportData($tglAwal, $tglAkhir, $warehouseId);

        $summary = [
            'total_items' => count($reportData),
            'total_stok_awal' => array_sum(array_column($reportData, 'stok_awal')),
            'total_masuk' => array_sum(array_column($reportData, 'masuk')),
            'total_keluar' => array_sum(array_column($reportData, 'keluar')),
            'total_stok_akhir' => array_sum(array_column($reportData, 'stok_akhir')),
        ];

        return view('admin.laporan_opname.index', compact(
            'reportData',
            'warehouses',
            'bulan',
            'tahun',
            'periode',
            'warehouseId',
            'tglAwal',
            'tglAkhir',
            'periodeLabel',
            'summary'
        ));
    }

    public function exportExcel(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $periode = $request->get('periode', '1');
        $warehouseId = $request->get('warehouse_id', 'all');

        [$tglAwal, $tglAkhir, $periodeLabel] = $this->resolveDateRange($bulan, $tahun, $periode, $request);

        $warehouseLabel = 'Semua Gudang';
        if ($warehouseId !== 'all') {
            $wh = Warehouse::find($warehouseId);
            if ($wh) {
                $warehouseLabel = $wh->name;
            }
        }

        $reportData = $this->generateReportData($tglAwal, $tglAkhir, $warehouseId);

        $meta = [
            'periode_label' => $periodeLabel,
            'warehouse_label' => $warehouseLabel,
            'tgl_awal' => Carbon::parse($tglAwal)->translatedFormat('d F Y'),
            'tgl_akhir' => Carbon::parse($tglAkhir)->translatedFormat('d F Y'),
        ];

        $filename = 'Laporan_Opname_' . str_replace(' ', '_', $periodeLabel) . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new LaporanOpnameExport($reportData, $meta), $filename);
    }

    public function simpanCheckpoint(Request $request)
    {
        $request->validate([
            'tgl_akhir' => 'required|date',
            'warehouse_id' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $tglAwal = $request->get('tgl_awal');
            $tglAkhir = $request->get('tgl_akhir');
            $warehouseId = $request->get('warehouse_id', 'all');

            $reportData = $this->generateReportData($tglAwal, $tglAkhir, $warehouseId);
            $cutoffDate = Carbon::parse($tglAkhir)->format('Y-m-d') . ' 23:59:59';
            $savedCount = 0;

            foreach ($reportData as $row) {
                // Periksa apakah checkpoint sudah ada untuk item, gudang, dan tanggal ini
                $existing = Stock::where('item_id', $row['item_id'])
                    ->where('warehouse_id', $row['warehouse_id'])
                    ->where('date_opname', $cutoffDate)
                    ->first();

                if ($existing) {
                    $existing->initial_stock = $row['stok_awal'];
                    $existing->final_stock = $row['stok_akhir'];
                    $existing->updated_by = Auth::id();
                    $existing->save();
                } else {
                    $stock = new Stock();
                    $stock->item_id = $row['item_id'];
                    $stock->warehouse_id = $row['warehouse_id'];
                    $stock->initial_stock = $row['stok_awal'];
                    $stock->final_stock = $row['stok_akhir'];
                    $stock->date_opname = $cutoffDate;
                    $stock->is_laporan = 1;
                    $stock->created_by = Auth::id();
                    $stock->updated_by = Auth::id();
                    $stock->save();
                }
                $savedCount++;
            }

            if (class_exists(ActivityLog::class)) {
                ActivityLog::record(
                    'STOCK_OPNAME',
                    null,
                    "Menyimpan Checkpoint Stok Opname {$savedCount} item per tanggal {$cutoffDate}"
                );
            }

            DB::commit();
            return redirect()->back()->with('success', "Berhasil menyimpan checkpoint opname untuk {$savedCount} item per {$cutoffDate}.");
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan checkpoint opname: ' . $th->getMessage());
        }
    }

    private function resolveDateRange($bulan, $tahun, $periode, Request $request): array
    {
        $bulanFormatted = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $namaBulan = LaporanTransaksi::convertBulan($bulanFormatted);

        if ($periode === '1') {
            $tglAwal = "{$tahun}-{$bulanFormatted}-01";
            $tglAkhir = "{$tahun}-{$bulanFormatted}-15";
            $label = "Periode 1 (01 - 15 {$namaBulan} {$tahun})";
        } elseif ($periode === '2') {
            $lastDay = date('t', strtotime("{$tahun}-{$bulanFormatted}-01"));
            $tglAwal = "{$tahun}-{$bulanFormatted}-16";
            $tglAkhir = "{$tahun}-{$bulanFormatted}-{$lastDay}";
            $label = "Periode 2 (16 - {$lastDay} {$namaBulan} {$tahun})";
        } else {
            $tglAwal = $request->get('tgl_awal', date('Y-m-01'));
            $tglAkhir = $request->get('tgl_akhir', date('Y-m-d'));
            $label = "Kustom (" . date('d/m/Y', strtotime($tglAwal)) . " s/d " . date('d/m/Y', strtotime($tglAkhir)) . ")";
        }

        return [$tglAwal, $tglAkhir, $label];
    }

    private function generateReportData($tglAwal, $tglAkhir, $warehouseId): array
    {
        $tglAwalPrev = date('Y-m-d 23:59:59', strtotime($tglAwal . ' -1 day'));

        $query = Stock::select('stocks.item_id', 'stocks.warehouse_id')
            ->join('items', 'stocks.item_id', '=', 'items.id')
            ->join('warehouses', 'stocks.warehouse_id', '=', 'warehouses.id')
            ->groupBy('stocks.item_id', 'stocks.warehouse_id')
            ->orderBy('warehouses.name', 'asc')
            ->orderBy('items.name', 'asc');

        if ($warehouseId !== 'all') {
            $query->where('stocks.warehouse_id', $warehouseId);
        }

        $stockPairs = $query->get();
        $itemsMap = Item::with('category')->get()->keyBy('id');
        $warehousesMap = Warehouse::all()->keyBy('id');

        $result = [];

        foreach ($stockPairs as $pair) {
            $itemId = $pair->item_id;
            $whId = $pair->warehouse_id;

            $item = $itemsMap->get($itemId);
            $warehouse = $warehousesMap->get($whId);

            if (!$item || !$warehouse) {
                continue;
            }

            // Stok Awal pada awal periode
            $stokAwal = Stock::liveStock($itemId, $whId, $tglAwalPrev);

            // Mutasi Masuk & Keluar selama periode
            $masuk = (float) LaporanTransaksi::transaksiMasuk($itemId, $whId, $tglAwal, $tglAkhir);
            $keluar = (float) LaporanTransaksi::transaksiKeluar($itemId, $whId, $tglAwal, $tglAkhir);

            // Stok Akhir Sistem pada akhir periode
            $stokAkhir = round($stokAwal + $masuk - $keluar, 4);

            $result[] = [
                'item_id' => $itemId,
                'item_code' => $item->code,
                'item_name' => $item->name,
                'category_name' => $item->category->name ?? '-',
                'warehouse_id' => $whId,
                'warehouse_name' => $warehouse->name,
                'unit' => $item->unit,
                'price' => (float)$item->price,
                'stok_awal' => $stokAwal,
                'masuk' => $masuk,
                'keluar' => $keluar,
                'stok_akhir' => $stokAkhir,
            ];
        }

        return $result;
    }
}
