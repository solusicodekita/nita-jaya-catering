<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TransaksiPesanan;
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;
use App\Models\Warehouse;
use App\Models\Stock;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class ProcessTodayOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catering:process-today-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatisasi pemotongan stok Gudang Dapur pada Hari-H acara (pukul 00:01 WIB)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->format('Y-m-d');
        $this->info("=== Memulai Pemotongan Stok Otomatis Pesanan Hari-H ($today) ===");

        // Cari warehouse Gudang Dapur (transit)
        $dapurWarehouse = Warehouse::where('name', 'LIKE', '%Dapur%')->first() 
            ?? Warehouse::first();

        if (!$dapurWarehouse) {
            $this->error("Gudang Dapur tidak ditemukan dalam sistem.");
            return 1;
        }

        // Cari pesanan yang bertanggal hari ini dan belum dipotong stoknya
        $pesanans = TransaksiPesanan::with(['details.menu.menuDetails.item'])
            ->whereDate('event_date', $today)
            ->whereIn('status', ['PESANAN', 'DICEK_DAPUR'])
            ->get();

        if ($pesanans->isEmpty()) {
            $this->info("Tidak ada pesanan bertanggal hari ini ($today) yang memerlukan pemotongan stok.");
            return 0;
        }

        $processedCount = 0;

        foreach ($pesanans as $pesanan) {
            DB::beginTransaction();
            try {
                // Hitung total kebutuhan bahan baku untuk pesanan ini
                $ingredientSummary = [];
                foreach ($pesanan->details as $detail) {
                    if (!$detail->menu || !$detail->menu->menuDetails) continue;

                    foreach ($detail->menu->menuDetails as $mDetail) {
                        if (!$mDetail->item) continue;
                        $itemId = $mDetail->item_id;
                        $neededQty = $mDetail->quantity * $detail->qty_porsi;

                        if (isset($ingredientSummary[$itemId])) {
                            $ingredientSummary[$itemId]['qty'] += $neededQty;
                        } else {
                            $ingredientSummary[$itemId] = [
                                'item' => $mDetail->item,
                                'qty' => $neededQty,
                            ];
                        }
                    }
                }

                if (empty($ingredientSummary)) {
                    $pesanan->update(['status' => 'DIPROSES_GUDANG']);
                    DB::commit();
                    continue;
                }

                // Hitung total harga transaksi stok
                $totalHargaKeseluruhan = 0;
                foreach ($ingredientSummary as $data) {
                    $totalHargaKeseluruhan += $data['qty'] * ($data['item']->price ?? 0);
                }

                // 1. Buat Stock Transaction (OUT_STOCK)
                $trxOut = StockTransaction::create([
                    'type' => 'out',
                    'alasan_adjustment' => "Pemotongan Stok Otomatis Hari-H Pesanan: {$pesanan->order_number} ({$pesanan->customer_name})",
                    'total_harga_keseluruhan' => $totalHargaKeseluruhan,
                    'date' => now(),
                    'created_by' => 1, // System automated user
                ]);

                // 2. Insert Rincian Stok Keluar Gudang Dapur
                foreach ($ingredientSummary as $itemId => $data) {
                    $item = $data['item'];
                    $qtyOut = $data['qty'];
                    $stokSebelumnya = Stock::liveStock($itemId, $dapurWarehouse->id);

                    StockTransactionDetail::create([
                        'stock_transaction_id' => $trxOut->id,
                        'item_id' => $itemId,
                        'warehouse_id' => $dapurWarehouse->id,
                        'quantity' => $qtyOut,
                        'stok_sebelumnya' => $stokSebelumnya,
                        'harga_satuan' => $item->price ?? 0,
                        'total_harga' => $qtyOut * ($item->price ?? 0),
                        'created_by' => 1,
                    ]);
                }

                // 3. Update status pesanan
                $pesanan->update([
                    'status' => 'DIPROSES_GUDANG',
                    'stock_transaction_id' => $trxOut->id,
                ]);

                // 4. Buat Notifikasi Sistem
                $rolesToNotify = ['admin-kantor', 'admin-dapur', 'admin-gudang-utama', 'admin'];
                $url = route('admin.pesanan.show', $pesanan->id);
                foreach ($rolesToNotify as $roleTarget) {
                    Notification::create([
                        'role_target' => $roleTarget,
                        'title' => "Pesanan Diproses Hari-H: {$pesanan->order_number}",
                        'message' => "Pemotongan stok otomatis Gudang Dapur telah berhasil dieksekusi untuk pesanan {$pesanan->order_number} ({$pesanan->customer_name}).",
                        'url' => $url,
                        'transaksi_pesanan_id' => $pesanan->id,
                        'is_read' => false,
                    ]);
                }

                DB::commit();
                $processedCount++;
                $this->info("SUCCESS: Pesanan #{$pesanan->order_number} berhasil dipotong stoknya.");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("ERROR: Gagal memproses pesanan #{$pesanan->order_number} - " . $e->getMessage());
            }
        }

        $this->info("=== Selesai. Total $processedCount pesanan diproses. ===");
        return 0;
    }
}
