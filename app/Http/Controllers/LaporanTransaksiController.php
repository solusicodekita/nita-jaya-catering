<?php

namespace App\Http\Controllers;

use App\Exports\LaporanTransaksiExport;
use App\Models\LaporanTransaksi;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LaporanTransaksiController extends Controller
{
    public function index()
    {
        $model = LaporanTransaksi::latest('id')->get();
        return view('admin.laporan_transaksi.index', compact('model'));
    }

    public function create()
    {
        try {
            $bulan = date('m', strtotime('-1 month'));
            $tahun = date('Y', strtotime('-1 month'));

            $bulanIniSudahAda = LaporanTransaksi::where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->exists();

            if ($bulanIniSudahAda) {
                return redirect()->route('admin.laporan_transaksi.index')->with('error', 'Laporan transaksi bulan kemarin sudah dibuat.');
            }

            $filename = 'laporan_transaksi_bulan_' . $this->convertBulan($bulan) . '_' . $tahun . '.xlsx';
            $filePath = 'laporan_transaksi/' . $filename;

            $laporanTransaksi = new LaporanTransaksi();
            $laporanTransaksi->bulan = $bulan;
            $laporanTransaksi->tahun = $tahun;
            $laporanTransaksi->url_laporan = $filePath;
            $laporanTransaksi->save();

            $tglAwal = date('Y-m-d', strtotime($tahun . '-' . $bulan . '-01'));
            $tglAkhir = date('Y-m-d', strtotime($tahun . '-' . $bulan . '-01 +1 month -1 day'));
            Excel::store(new LaporanTransaksiExport($bulan, $tahun, $tglAwal, $tglAkhir), $filePath);

            return redirect()->route('admin.laporan_transaksi.index')->with('success', 'Laporan transaksi berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->route('admin.laporan_transaksi.index')->with('error', 'Laporan transaksi gagal dibuat');
        }
    }

    public function download($id)
    {
        $laporanTransaksi = LaporanTransaksi::find($id);
        return Storage::download($laporanTransaksi->url_laporan);
    }

    public function convertBulan($bulan)
    {
        $namaBulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        return $namaBulan[$bulan];
    }

    public function preview(Request $request) {
        try {
            $tgl_awal = date('Y-m-d', strtotime('first day of this month'));
            $tgl_akhir = $request->tgl_akhir;
            $bulan = date('m', strtotime('-1 month'));
            $tahun = date('Y', strtotime('-1 month'));

            $filename = 'preview_laporan_transaksi_' . date('d_m_Y_His') . '.xlsx';
            
            return Excel::download(
                new LaporanTransaksiExport($bulan, $tahun, $tgl_awal, $tgl_akhir, 1),
                $filename
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat preview laporan: ' . $e->getMessage()
            ]);
        }
    }

    public function coba() {
        try {
            // $tgl_awal = date('Y-m-d', strtotime('first day of this month'));
            // $tgl_akhir = $request->tgl_akhir;
            $tgl_awal = '2025-09-01';
            $tgl_akhir = '2025-09-30';
            $bulan = '09';
            $tahun = '2025';

            $filename = 'preview_laporan_transaksi_' . date('2025-09-01 His') . '.xlsx';
            
            return Excel::download(
                new LaporanTransaksiExport($bulan, $tahun, $tgl_awal, $tgl_akhir, 1),
                $filename
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat preview laporan: ' . $e->getMessage()
            ]);
        }
    }
}
