<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanTransaksi extends Model
{
    use HasFactory;
    protected $guarded = [];

    public static function convertBulan($bulan)
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

    public static function transaksiMasuk($item_id, $warehouse_id, $tgl_awal, $tgl_akhir) {
        $barangMasuk = StockTransactionDetail::leftJoin('stock_transactions', 'stock_transaction_details.stock_transaction_id', '=', 'stock_transactions.id')
            ->where('stock_transaction_details.item_id', $item_id)
            ->where('stock_transaction_details.warehouse_id', $warehouse_id)
            ->where('stock_transactions.type', 'in')
            ->whereDate('stock_transactions.date', '>=', $tgl_awal)
            ->whereDate('stock_transactions.date', '<=', $tgl_akhir)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('stock_transactions.is_adjustment', 0)->orWhere('stock_transactions.is_adjustment', null);
                    })
                      ->orWhere(function ($query) {
                          $query->where('stock_transactions.is_adjustment', 1)
                                ->where('stock_transactions.is_verifikasi_adjustment', 1);
                    });
            })
            ->sum('stock_transaction_details.quantity');
        return $barangMasuk;
    }

    public static function transaksiKeluar($item_id, $warehouse_id, $tgl_awal, $tgl_akhir) {
        $barangKeluar = StockTransactionDetail::leftJoin('stock_transactions', 'stock_transaction_details.stock_transaction_id', '=', 'stock_transactions.id')
            ->where('stock_transaction_details.item_id', $item_id)
            ->where('stock_transaction_details.warehouse_id', $warehouse_id)
            ->where('stock_transactions.type', 'out')
            ->whereDate('stock_transactions.date', '>=', $tgl_awal)
            ->whereDate('stock_transactions.date', '<=', $tgl_akhir)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('stock_transactions.is_adjustment', 0)
                      ->orWhere('stock_transactions.is_adjustment', null);
                })
                      ->orWhere(function ($query) {
                          $query->where('stock_transactions.is_adjustment', 1)
                                ->where('stock_transactions.is_verifikasi_adjustment', 1);
                      });
                })
            ->sum('stock_transaction_details.quantity');
        return $barangKeluar;
    }

    public static function hargaTertinggi($item_id, $warehouse_id, $tgl_awal, $tgl_akhir) {
        $hargaTertinggi = HistoryHarga::where('item_id', $item_id)
            ->where('warehouse_id', $warehouse_id)
            ->whereDate('created_at', '>=', $tgl_awal)
            ->whereDate('created_at', '<=', $tgl_akhir)
            ->max('harga_baru');

        // Jika $hargaTertinggi kosong, lakukan pencarian ke bulan sebelumnya berturut-turut sampai dapat datanya
        $tgl_awal_loop = $tgl_awal;
        $tgl_akhir_loop = $tgl_akhir;

        while (!$hargaTertinggi) {
            // Kurangi satu bulan
            $tgl_awal_loop = date('Y-m-01', strtotime($tgl_awal_loop . ' -1 month'));
            $tgl_akhir_loop = date('Y-m-t', strtotime($tgl_awal_loop));

            $hargaTertinggi = HistoryHarga::where('item_id', $item_id)
                ->where('warehouse_id', $warehouse_id)
                ->whereDate('created_at', '>=', $tgl_awal_loop)
                ->whereDate('created_at', '<=', $tgl_akhir_loop)
                ->max('harga_baru');

            // Safety net: jika sudah terlalu mundur (>36 bulan = 3 tahun), break perulangan
            $limitDate = date('Y-m-01', strtotime('-36 months', strtotime($tgl_awal)));
            if ($tgl_awal_loop < $limitDate) {
                break;
            }
        }
        return $hargaTertinggi;
    }
}
