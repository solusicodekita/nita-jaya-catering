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
        if (auth()->user()->hasRole('admin-dapur')) {
            return redirect()->route('admin.pesanan.index')->with('error', 'Admin Dapur hanya memiliki akses untuk melihat dan memverifikasi pesanan.');
        }

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

        // Pengaturan pengurangan stok dihapus agar pesanan selalu otomatis memotong stok.

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
                'address' => $request->address,
                'city' => $request->city,
                'event_place' => $request->event_place,
                'phone' => $request->phone,
                'cs_name' => $request->cs_name,
                'reference' => $request->reference,
                'event_day' => $request->event_day,
                'porsi_total' => $request->porsi_total,
                'event_name' => $request->event_name,
                'delivery_time' => $request->delivery_time,
                'ready_time' => $request->ready_time,
                'invitation_qty' => $request->invitation_qty,
                'nuansa_theme' => $request->nuansa_theme,
                'notes' => $request->notes,
                'free_note' => $request->free_note,
                'dp1' => $request->dp1,
                'dp1_note' => $request->dp1_note,
                'dp2' => $request->dp2,
                'dp2_note' => $request->dp2_note,
                'dp3' => $request->dp3,
                'dp3_note' => $request->dp3_note,
                'lunas_note' => $request->lunas_note,
                'kekurangan' => $request->kekurangan,
            ]);

            $totalCostAll = 0;
            $grandTotalAll = 0;

            // Variabel agregasi bahan baku
            $aggregatedIngredients = [];

            // 2. Iterasi Menu yang dipesan
            foreach ($request->menu_id as $index => $menuId) {
                $qty = $request->qty_porsi[$index];
                
                $menu = Menu::with('menuDetails.item')->findOrFail($menuId);

                if ($menu->menuDetails->isEmpty()) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Gagal! Resep '{$menu->name}' belum memiliki bahan baku (item kosong). Harap isi bahan baku di Master Resep terlebih dahulu.");
                }

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

            // 3. Lakukan Transaksi Stok Keluar (HANYA jika BUKAN admin-kantor)
            $isKantor = auth()->user()->hasRole('admin-kantor');
            if (!$isKantor && count($aggregatedIngredients) > 0) {
                $totalHargaKeseluruhan = 0;
                $gudangDapur = \App\Models\Warehouse::where('name', 'LIKE', '%dapur%')->first();
                $warehouseId = $gudangDapur ? $gudangDapur->id : (\App\Models\Warehouse::first()->id ?? 2);

                $stockTransaction = StockTransaction::create([
                    'type' => 'out',
                    'total_harga_keseluruhan' => 0, // diupdate nanti
                    'date' => now(),
                    'created_by' => Auth::id(),
                ]);

                foreach ($aggregatedIngredients as $itemId => $data) {
                    $item = $data['item'];
                    $qtyOut = $data['total_qty'];
                    $hargaSatuan = $item->price ?? 0;
                    $totalHarga = $hargaSatuan * $qtyOut;
                    $totalHargaKeseluruhan += $totalHarga;

                    $stokSebelumnya = Stock::liveStock($itemId, $warehouseId);

                    StockTransactionDetail::create([
                        'stock_transaction_id' => $stockTransaction->id,
                        'item_id' => $itemId,
                        'warehouse_id' => $warehouseId,
                        'quantity' => $qtyOut,
                        'harga_satuan' => $hargaSatuan,
                        'total_harga' => $totalHarga,
                        'description' => "Otomatis dipotong dari pesanan: " . $pesanan->order_number,
                        'stok_sebelumnya' => $stokSebelumnya,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }

                $stockTransaction->update([
                    'total_harga_keseluruhan' => $totalHargaKeseluruhan
                ]);

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

            // Buat Notifikasi Database untuk Dapur, Gudang, dan Admin Utama
            $creatorName = Auth::user()->fullname ?? (Auth::user()->username ?? 'Admin Kantor');
            $url = route('admin.pesanan.show', $pesanan->id);
            $rolesToNotify = ['admin-dapur', 'admin-gudang-utama', 'admin'];

            foreach ($rolesToNotify as $roleTarget) {
                \App\Models\Notification::create([
                    'role_target' => $roleTarget,
                    'title' => "Pesanan Baru: {$pesanan->order_number}",
                    'message' => "Pesanan baru dari Klien {$pesanan->customer_name} telah dibuat oleh {$creatorName}.",
                    'url' => $url,
                    'transaksi_pesanan_id' => $pesanan->id,
                    'is_read' => false,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.pesanan.index')->with('success', 'Pesanan berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $pesanan = TransaksiPesanan::with([
            'details.menu.menuDetails.item',
            'createdBy',
            'verifiedBy',
            'stockTransaction.stockTransactionDetails.item'
        ])->findOrFail($id);

        // Agregasi Bahan Baku & Cek Live Stock (Gudang Utama [Total Non-Dapur] vs Gudang Dapur)
        $ingredientCheck = [];
        $gudangDapur = \App\Models\Warehouse::where('name', 'LIKE', '%dapur%')->first();
        $dapurId = $gudangDapur ? $gudangDapur->id : 4;
        $gudangUtamaList = \App\Models\Warehouse::where('id', '!=', $dapurId)->get();

        foreach ($pesanan->details as $detail) {
            if (isset($detail->menu->menuDetails)) {
                foreach ($detail->menu->menuDetails as $mDetail) {
                    $itemId = $mDetail->item_id;
                    $needed = $mDetail->quantity * $detail->qty_porsi;

                    if (!isset($ingredientCheck[$itemId])) {
                        $liveStockDapur = \App\Models\Stock::liveStock($itemId, $dapurId);
                        
                        $liveStockUtama = 0;
                        foreach ($gudangUtamaList as $gw) {
                            $liveStockUtama += \App\Models\Stock::liveStock($itemId, $gw->id);
                        }

                        $ingredientCheck[$itemId] = [
                            'item' => $mDetail->item,
                            'total_needed' => 0,
                            'live_stock' => $liveStockDapur,
                            'live_stock_dapur' => $liveStockDapur,
                            'live_stock_utama' => $liveStockUtama,
                        ];
                    }
                    $ingredientCheck[$itemId]['total_needed'] += $needed;
                }
            }
        }

        return view('admin.pesanan.show', compact('pesanan', 'ingredientCheck'));
    }

    public function edit($id)
    {
        if (auth()->user()->hasRole('admin-dapur')) {
            return redirect()->route('admin.pesanan.index')->with('error', 'Admin Dapur hanya memiliki akses untuk melihat dan memverifikasi pesanan.');
        }

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

    public function verifikasiDapur($id)
    {
        if (!auth()->user()->hasRole('admin-dapur') && !auth()->user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya Admin Dapur yang dapat melakukan verifikasi ini.');
        }

        $pesanan = TransaksiPesanan::findOrFail($id);
        $pesanan->update([
            'status' => 'DICEK_DAPUR',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        // Buat Notifikasi Database untuk Kantor, Gudang Utama, dan Admin
        $verifierName = Auth::user()->fullname ?? (Auth::user()->username ?? 'Admin Dapur');
        $url = route('admin.pesanan.show', $pesanan->id);
        $rolesToNotify = ['admin-kantor', 'admin-gudang-utama', 'admin'];

        foreach ($rolesToNotify as $roleTarget) {
            \App\Models\Notification::create([
                'role_target' => $roleTarget,
                'title' => "Pesanan Checked Dapur: {$pesanan->order_number}",
                'message' => "Bahan baku pesanan {$pesanan->order_number} ({$pesanan->customer_name}) telah diverifikasi oleh {$verifierName}.",
                'url' => $url,
                'transaksi_pesanan_id' => $pesanan->id,
                'is_read' => false,
            ]);
        }

        return redirect()->back()->with('success', "Pesanan #{$pesanan->order_number} berhasil diverifikasi ketersediaan bahannya oleh Admin Dapur.");
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
                $oldStockTx = StockTransaction::find($pesanan->stock_transaction_id);
                if ($oldStockTx) {
                    // Hapus data transaksi stok lama (stok akan kembali karena stok dihitung secara live)
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

                if ($menu->menuDetails->isEmpty()) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Gagal! Resep '{$menu->name}' belum memiliki bahan baku (item kosong). Harap isi bahan baku di Master Resep terlebih dahulu.");
                }

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

            // 4. Potong Stok Baru (HANYA jika BUKAN admin-kantor)
            $newStockTxId = null;
            $isKantor = auth()->user()->hasRole('admin-kantor');

            if (!$isKantor && count($aggregatedIngredients) > 0) {
                $totalHargaKeseluruhan = 0;
                $gudangDapur = \App\Models\Warehouse::where('name', 'LIKE', '%dapur%')->first();
                $warehouseId = $gudangDapur ? $gudangDapur->id : (\App\Models\Warehouse::first()->id ?? 2);

                $stockTransaction = StockTransaction::create([
                    'type' => 'out',
                    'total_harga_keseluruhan' => 0,
                    'date' => now(),
                    'created_by' => Auth::id(),
                ]);
                $newStockTxId = $stockTransaction->id;

                foreach ($aggregatedIngredients as $itemId => $data) {
                    $item = $data['item'];
                    $qtyOut = $data['total_qty'];
                    $hargaSatuan = $item->price ?? 0;
                    $totalHarga = $hargaSatuan * $qtyOut;
                    $totalHargaKeseluruhan += $totalHarga;

                    $stokSebelumnya = Stock::liveStock($itemId, $warehouseId);

                    StockTransactionDetail::create([
                        'stock_transaction_id' => $stockTransaction->id,
                        'item_id' => $itemId,
                        'warehouse_id' => $warehouseId,
                        'quantity' => $qtyOut,
                        'harga_satuan' => $hargaSatuan,
                        'total_harga' => $totalHarga,
                        'description' => "Otomatis dipotong dari pesanan: " . $pesanan->order_number,
                        'stok_sebelumnya' => $stokSebelumnya,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }

                $stockTransaction->update([
                    'total_harga_keseluruhan' => $totalHargaKeseluruhan
                ]);
            }

            // 5. Update Header Pesanan
            $pesanan->update([
                'customer_name' => $request->customer_name,
                'event_date' => $request->event_date,
                'total_cost' => $totalCostAll,
                'grand_total' => $grandTotalAll,
                'stock_transaction_id' => $newStockTxId,
                'address' => $request->address,
                'city' => $request->city,
                'event_place' => $request->event_place,
                'phone' => $request->phone,
                'cs_name' => $request->cs_name,
                'reference' => $request->reference,
                'event_day' => $request->event_day,
                'porsi_total' => $request->porsi_total,
                'event_name' => $request->event_name,
                'delivery_time' => $request->delivery_time,
                'ready_time' => $request->ready_time,
                'invitation_qty' => $request->invitation_qty,
                'nuansa_theme' => $request->nuansa_theme,
                'notes' => $request->notes,
                'free_note' => $request->free_note,
                'dp1' => $request->dp1,
                'dp1_note' => $request->dp1_note,
                'dp2' => $request->dp2,
                'dp2_note' => $request->dp2_note,
                'dp3' => $request->dp3,
                'dp3_note' => $request->dp3_note,
                'lunas_note' => $request->lunas_note,
                'kekurangan' => $request->kekurangan,
                'updated_by' => Auth::id()
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
        if (auth()->user()->hasRole('admin-dapur')) {
            return redirect()->route('admin.pesanan.index')->with('error', 'Admin Dapur tidak memiliki akses untuk menghapus pesanan.');
        }

        $pesanan = TransaksiPesanan::findOrFail($id);

        DB::beginTransaction();
        try {
            // Hapus Transaksi Stok jika ada (stok live otomatis kembali)
            if ($pesanan->stock_transaction_id) {
                $stockTx = StockTransaction::find($pesanan->stock_transaction_id);
                if ($stockTx) {
                    StockTransactionDetail::where('stock_transaction_id', $stockTx->id)->delete();
                    $stockTx->delete();
                }
            }

            // Hapus Detail Pesanan
            TransaksiPesananDetail::where('transaksi_pesanan_id', $id)->delete();

            // Log Aktivitas
            ActivityLog::record('DELETE', $pesanan, "Menghapus Pesanan Katering: {$pesanan->order_number}");

            // Hapus Header Pesanan
            $pesanan->delete();

            DB::commit();
            return redirect()->route('admin.pesanan.index')->with('success', 'Pesanan berhasil dihapus dan stok dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function calculateRecipe(Request $request)
    {
        $menuIds = $request->input('menu_id', []);
        $qtyPorsis = $request->input('qty_porsi', []);

        if (empty($menuIds)) {
            return response()->json([
                'ingredients' => [],
                'total_cost' => 0,
                'grand_total' => 0
            ]);
        }

        $aggregatedIngredients = [];
        $totalCostAll = 0;
        $grandTotalAll = 0;

        foreach ($menuIds as $index => $menuId) {
            $qty = (float) ($qtyPorsis[$index] ?? 1);
            $menu = Menu::with('menuDetails.item')->find($menuId);
            if (!$menu) continue;

            $totalCostMenu = 0;
            foreach ($menu->menuDetails as $detail) {
                $item = $detail->item;
                if (!$item) continue;

                $totalCostMenu += ($item->price ?? 0) * $detail->quantity;

                $itemId = $item->id;
                $totalQtyNeeded = $detail->quantity * $qty;

                if (!isset($aggregatedIngredients[$itemId])) {
                    $aggregatedIngredients[$itemId] = [
                        'name' => $item->name,
                        'unit' => $item->unit ?? '',
                        'total_qty' => 0,
                        'price' => $item->price ?? 0,
                    ];
                }
                $aggregatedIngredients[$itemId]['total_qty'] += $totalQtyNeeded;
            }

            $costFactorVal = $totalCostMenu * (($menu->cost_factor ?? 20) / 100);
            $totalCost2 = $totalCostMenu + $costFactorVal;
            $profitVal = $totalCost2 * (($menu->profit_margin ?? 30) / 100);
            $sellingPricePerPortion = $totalCost2 + $profitVal;

            $totalCostAll += ($totalCostMenu * $qty);
            $grandTotalAll += ($sellingPricePerPortion * $qty);
        }

        return response()->json([
            'ingredients' => array_values($aggregatedIngredients),
            'total_cost' => $totalCostAll,
            'grand_total' => $grandTotalAll
        ]);
    }

    public function cetak($id)
    {
        $pesanan = TransaksiPesanan::with(['details.menu.menuDetails.item', 'createdBy'])->findOrFail($id);
        return view('admin.pesanan.cetak', compact('pesanan'));
    }

    public function pdf($id)
    {
        $pesanan = TransaksiPesanan::with(['details.menu.menuDetails.item', 'createdBy'])->findOrFail($id);

        $logoPath = public_path('images/nitajaya.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        }

        $html = view('admin.pesanan.cetak_pdf', compact('pesanan', 'logoBase64'))->render();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 8,
            'margin_bottom' => 8,
        ]);

        $mpdf->WriteHTML($html);
        return response($mpdf->Output("Bukti_Pesanan_{$pesanan->order_number}.pdf", 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Bukti_Pesanan_' . $pesanan->order_number . '.pdf"',
        ]);
    }
}
