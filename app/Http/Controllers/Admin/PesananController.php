<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\TransaksiPesanan;
use App\Models\TransaksiPesananDetail;
use App\Models\StockTransaction;
use App\Models\StockTransactionDetail;
use App\Models\Stock;
use App\Models\SettingWebsite;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    public function index()
    {
        $pesanans = TransaksiPesanan::with(['createdBy', 'stockTransaction'])->orderBy('created_at', 'desc')->get();
        return view('admin.pesanan.index', compact('pesanans'));
    }

    public function create()
    {
        $menus = Menu::with('menuDetails.item')->where('is_active', true)->orderBy('name', 'asc')->get();
        foreach ($menus as $menu) {
            $totalCostMenu = 0;
            foreach ($menu->menuDetails as $detail) {
                $totalCostMenu += ($detail->item->price ?? 0) * $detail->quantity;
            }
            $costFactorVal = $totalCostMenu * (($menu->cost_factor ?? 20) / 100);
            $totalCost2 = $totalCostMenu + $costFactorVal;
            $profitVal = $totalCost2 * (($menu->profit_margin ?? 30) / 100);
            
            $menu->total_cost = $totalCostMenu;
            $menu->selling_price = $totalCost2 + $profitVal;
        }
        
        $lastOrder = TransaksiPesanan::orderBy('id', 'desc')->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
        $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('admin.pesanan.create', compact('menus', 'orderNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_number' => 'required|unique:transaksi_pesanans,order_number',
            'customer_name' => 'required|string|max:191',
            'event_date' => 'required|date',
            'menu_id' => 'required|array',
            'qty_porsi' => 'required|array',
        ]);

        $setting = SettingWebsite::first();
        // Cek apakah sistem diatur untuk memotong stok (bisa jadi diatur di pengaturan global)
        $shouldReduceStock = $setting ? $setting->default_reduce_stock : true;

        DB::beginTransaction();
        try {
            // 1. Buat Header Pesanan
            $pesanan = TransaksiPesanan::create([
                'order_number' => $request->order_number,
                'customer_name' => $request->customer_name,
                'event_date' => $request->event_date,
                'total_cost' => 0,
                'grand_total' => 0,
                'created_by' => Auth::id(),
            ]);

            $totalCostAll = 0;
            $grandTotalAll = 0;

            // Variabel agregasi bahan baku
            $aggregatedIngredients = [];

            // 2. Iterasi Menu yang dipesan
            foreach ($request->menu_id as $index => $menuId) {
                $qty = $request->qty_porsi[$index];
                
                $menu = Menu::with('menuDetails.item')->findOrFail($menuId);

                // Kalkulasi HPP dan Harga Jual (Per Porsi) berdasarkan bahan baku saat ini
                $totalCostMenu = 0;
                foreach ($menu->menuDetails as $detail) {
                    $totalCostMenu += ($detail->item->price ?? 0) * $detail->quantity;
                    
                    // Agregasi Bahan Baku untuk pemotongan stok
                    $itemId = $detail->item_id;
                    $totalQtyNeeded = $detail->quantity * $qty;
                    if (!isset($aggregatedIngredients[$itemId])) {
                        $aggregatedIngredients[$itemId] = [
                            'item' => $detail->item,
                            'total_qty' => 0
                        ];
                    }
                    $aggregatedIngredients[$itemId]['total_qty'] += $totalQtyNeeded;
                }

                $costFactorVal = $totalCostMenu * (($menu->cost_factor ?? 20) / 100);
                $totalCost2 = $totalCostMenu + $costFactorVal;
                $profitVal = $totalCost2 * (($menu->profit_margin ?? 30) / 100);
                $sellingPricePerPortion = $totalCost2 + $profitVal;

                $subtotalCost = $totalCostMenu * $qty;
                $subtotalPrice = $sellingPricePerPortion * $qty;

                // Create Pesanan Detail
                TransaksiPesananDetail::create([
                    'transaksi_pesanan_id' => $pesanan->id,
                    'menu_id' => $menuId,
                    'qty_porsi' => $qty,
                    'subtotal_cost' => $subtotalCost,
                    'subtotal_price' => $subtotalPrice,
                ]);

                $totalCostAll += $subtotalCost;
                $grandTotalAll += $subtotalPrice;
            }

            // 3. Lakukan Transaksi Stok Keluar
            if ($shouldReduceStock && count($aggregatedIngredients) > 0) {
                $stockTransaction = StockTransaction::create([
                    'type' => 'out',
                    'date' => now(),
                    'created_by' => Auth::id(),
                ]);

                foreach ($aggregatedIngredients as $itemId => $data) {
                    $item = $data['item'];
                    $qtyOut = $data['total_qty'];

                    StockTransactionDetail::create([
                        'stock_transaction_id' => $stockTransaction->id,
                        'item_id' => $itemId,
                        'quantity' => $qtyOut,
                        'price' => $item->price ?? 0,
                    ]);

                    // Potong dari tabel Stock
                    $stock = Stock::where('item_id', $itemId)->first();
                    if ($stock) {
                        $stock->decrement('quantity', $qtyOut);
                    } else {
                        Stock::create([
                            'item_id' => $itemId,
                            'quantity' => -$qtyOut, // Minus jika stok belum ada recordnya
                        ]);
                    }
                }

                // Relasikan Transaksi Stok ke Pesanan
                $pesanan->update([
                    'stock_transaction_id' => $stockTransaction->id
                ]);
            }

            // 4. Update Total di Header Pesanan
            $pesanan->update([
                'total_cost' => $totalCostAll,
                'grand_total' => $grandTotalAll,
            ]);

            ActivityLog::record('CREATE', $pesanan, "Membuat Pesanan Katering: {$pesanan->order_number}", [
                'total_cost' => $totalCostAll,
                'grand_total' => $grandTotalAll
            ]);

            DB::commit();
            return redirect()->route('admin.pesanan.index')->with('success', 'Pesanan berhasil dibuat dan stok otomatis terpotong.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $pesanan = TransaksiPesanan::with(['details.menu', 'createdBy', 'stockTransaction.details.item'])->findOrFail($id);
        return view('admin.pesanan.show', compact('pesanan'));
    }

    public function edit($id)
    {
        $pesanan = TransaksiPesanan::with(['details.menu'])->findOrFail($id);
        $menus = Menu::with('menuDetails.item')->where('is_active', true)->orderBy('name', 'asc')->get();
        foreach ($menus as $menu) {
            $totalCostMenu = 0;
            foreach ($menu->menuDetails as $detail) {
                $totalCostMenu += ($detail->item->price ?? 0) * $detail->quantity;
            }
            $costFactorVal = $totalCostMenu * (($menu->cost_factor ?? 20) / 100);
            $totalCost2 = $totalCostMenu + $costFactorVal;
            $profitVal = $totalCost2 * (($menu->profit_margin ?? 30) / 100);
            
            $menu->total_cost = $totalCostMenu;
            $menu->selling_price = $totalCost2 + $profitVal;
        }
        return view('admin.pesanan.edit', compact('pesanan', 'menus'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_name' => 'required|string|max:191',
            'event_date' => 'required|date',
            'menu_id' => 'required|array',
            'qty_porsi' => 'required|array',
        ]);

        $pesanan = TransaksiPesanan::findOrFail($id);

        DB::beginTransaction();
        try {
            // 1. REVERT STOK LAMA JIKA ADA
            if ($pesanan->stock_transaction_id) {
                $oldStockTx = StockTransaction::with('details')->find($pesanan->stock_transaction_id);
                if ($oldStockTx) {
                    foreach ($oldStockTx->details as $detail) {
                        $stock = Stock::where('item_id', $detail->item_id)->first();
                        if ($stock) {
                            $stock->increment('quantity', $detail->quantity);
                        }
                    }
                    // Hapus data transaksi stok lama
                    StockTransactionDetail::where('stock_transaction_id', $oldStockTx->id)->delete();
                    $oldStockTx->delete();
                }
            }

            // 2. Hapus detail pesanan lama
            TransaksiPesananDetail::where('transaksi_pesanan_id', $id)->delete();

            // 3. Kalkulasi Ulang
            $totalCostAll = 0;
            $grandTotalAll = 0;
            $aggregatedIngredients = [];

            foreach ($request->menu_id as $index => $menuId) {
                $qty = $request->qty_porsi[$index];
                $menu = Menu::with('menuDetails.item')->findOrFail($menuId);

                $totalCostMenu = 0;
                foreach ($menu->menuDetails as $detail) {
                    $totalCostMenu += ($detail->item->price ?? 0) * $detail->quantity;
                    $itemId = $detail->item_id;
                    $totalQtyNeeded = $detail->quantity * $qty;
                    if (!isset($aggregatedIngredients[$itemId])) {
                        $aggregatedIngredients[$itemId] = ['item' => $detail->item, 'total_qty' => 0];
                    }
                    $aggregatedIngredients[$itemId]['total_qty'] += $totalQtyNeeded;
                }

                $costFactorVal = $totalCostMenu * (($menu->cost_factor ?? 20) / 100);
                $totalCost2 = $totalCostMenu + $costFactorVal;
                $profitVal = $totalCost2 * (($menu->profit_margin ?? 30) / 100);
                $sellingPricePerPortion = $totalCost2 + $profitVal;

                $subtotalCost = $totalCostMenu * $qty;
                $subtotalPrice = $sellingPricePerPortion * $qty;

                TransaksiPesananDetail::create([
                    'transaksi_pesanan_id' => $pesanan->id,
                    'menu_id' => $menuId,
                    'qty_porsi' => $qty,
                    'subtotal_cost' => $subtotalCost,
                    'subtotal_price' => $subtotalPrice,
                ]);

                $totalCostAll += $subtotalCost;
                $grandTotalAll += $subtotalPrice;
            }

            // 4. Potong Stok Baru
            $setting = SettingWebsite::first();
            $shouldReduceStock = $setting ? $setting->default_reduce_stock : true;
            $newStockTxId = null;

            if ($shouldReduceStock && count($aggregatedIngredients) > 0) {
                $stockTransaction = StockTransaction::create([
                    'type' => 'out',
                    'date' => now(),
                    'created_by' => Auth::id(),
                ]);
                $newStockTxId = $stockTransaction->id;

                foreach ($aggregatedIngredients as $itemId => $data) {
                    $item = $data['item'];
                    $qtyOut = $data['total_qty'];

                    StockTransactionDetail::create([
                        'stock_transaction_id' => $stockTransaction->id,
                        'item_id' => $itemId,
                        'quantity' => $qtyOut,
                        'price' => $item->price ?? 0,
                    ]);

                    $stock = Stock::where('item_id', $itemId)->first();
                    if ($stock) {
                        $stock->decrement('quantity', $qtyOut);
                    } else {
                        Stock::create(['item_id' => $itemId, 'quantity' => -$qtyOut]);
                    }
                }
            }

            // 5. Update Header Pesanan
            $pesanan->update([
                'customer_name' => $request->customer_name,
                'event_date' => $request->event_date,
                'total_cost' => $totalCostAll,
                'grand_total' => $grandTotalAll,
                'stock_transaction_id' => $newStockTxId,
            ]);

            ActivityLog::record('UPDATE', $pesanan, "Mengubah Pesanan Katering: {$pesanan->order_number}", [
                'total_cost' => $totalCostAll,
                'grand_total' => $grandTotalAll
            ]);

            DB::commit();
            return redirect()->route('admin.pesanan.index')->with('success', 'Pesanan berhasil diperbarui dan stok telah disesuaikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $pesanan = TransaksiPesanan::findOrFail($id);

        DB::beginTransaction();
        try {
            // Revert Stock
            if ($pesanan->stock_transaction_id) {
                $oldStockTx = StockTransaction::with('details')->find($pesanan->stock_transaction_id);
                if ($oldStockTx) {
                    foreach ($oldStockTx->details as $detail) {
                        $stock = Stock::where('item_id', $detail->item_id)->first();
                        if ($stock) {
                            $stock->increment('quantity', $detail->quantity);
                        }
                    }
                    StockTransactionDetail::where('stock_transaction_id', $oldStockTx->id)->delete();
                    $oldStockTx->delete();
                }
            }

            // Hapus Detail
            TransaksiPesananDetail::where('transaksi_pesanan_id', $id)->delete();
            
            ActivityLog::record('DELETE', $pesanan, "Menghapus Pesanan Katering: {$pesanan->order_number}");

            // Hapus Header
            $pesanan->delete();

            DB::commit();
            return redirect()->route('admin.pesanan.index')->with('success', 'Pesanan dibatalkan. Stok berhasil dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }
}
