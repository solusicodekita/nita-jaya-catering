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
            ->sum('stock_transaction_details.quantity');
        return $barangKeluar;
    }
}
